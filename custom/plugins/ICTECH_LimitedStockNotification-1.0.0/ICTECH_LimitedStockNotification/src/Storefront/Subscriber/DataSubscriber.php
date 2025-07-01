<?php

declare(strict_types=1);

namespace ICTECH_LimitedStockNotification\Storefront\Subscriber;

use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\Event\AfterLineItemAddedEvent;
use Shopware\Core\Checkout\Cart\Event\AfterLineItemQuantityChangedEvent;
use Shopware\Core\Checkout\Cart\Event\AfterLineItemRemovedEvent;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Page\Checkout\Confirm\CheckoutConfirmPageLoadedEvent;
use Shopware\Storefront\Page\Checkout\Offcanvas\OffcanvasCartPageLoadedEvent;
use Shopware\Storefront\Page\Checkout\Register\CheckoutRegisterPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class DataSubscriber implements EventSubscriberInterface
{
    private EntityRepository $ictCartWishlistRepository;
    private RequestStack $requestStack;
    private SystemConfigService $systemConfigService;

    public function __construct(
        EntityRepository $ictCartWishlistRepository,
        RequestStack $requestStack,
        SystemConfigService $systemConfigService,
    ) {
        $this->ictCartWishlistRepository = $ictCartWishlistRepository;
        $this->currentRequest = $requestStack->getCurrentRequest();
        $this->systemConfigService = $systemConfigService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AfterLineItemAddedEvent::class => 'onLineItemAdded',
            AfterLineItemQuantityChangedEvent::class => 'onLineItemQtyChanged',
            AfterLineItemRemovedEvent::class => 'onLineItemRemoved',
            OffcanvasCartPageLoadedEvent::class => 'onOffcanvasCartPageLoaded',
            CheckoutRegisterPageLoadedEvent::class => 'onCheckoutRegisterPageLoaded',
            CheckoutConfirmPageLoadedEvent::class => 'onCheckoutConfirmPageLoaded',
        ];
    }

    public function onLineItemAdded(AfterLineItemAddedEvent $event): void
    {
        $cart = $event->getCart();
        $salesChannelContext = $event->getSalesChannelContext();
        if ($this->currentRequest->attributes->get('sw-domain-id')) {
            $this->upsertIctCartWishlist($cart, $salesChannelContext, true);
        }
    }

    public function onLineItemRemoved(AfterLineItemRemovedEvent $event): void
    {
        $cart = $event->getCart();
        $salesChannelContext = $event->getSalesChannelContext();

        if ($this->currentRequest->attributes->get('sw-domain-id')) {
            $this->upsertIctCartWishlist($cart, $salesChannelContext, true);
        }
    }

    public function onLineItemQtyChanged(AfterLineItemQuantityChangedEvent $event): void
    {
        $cart = $event->getCart();
        $salesChannelContext = $event->getSalesChannelContext();
        if ($this->currentRequest->attributes->get('sw-domain-id')) {
            $this->upsertIctCartWishlist($cart, $salesChannelContext, true);
        }
    }

    public function onOffcanvasCartPageLoaded(OffcanvasCartPageLoadedEvent $event): void
    {
        $cart = $event->getPage()->getCart();
        $this->upsertIctCartWishlist($cart, $event->getSalesChannelContext());
    }

    public function onCheckoutConfirmPageLoaded(CheckoutConfirmPageLoadedEvent $event): void
    {
        $cart = $event->getPage()->getCart();
        $this->upsertIctCartWishlist($cart, $event->getSalesChannelContext());
    }

    public function onCheckoutRegisterPageLoaded(CheckoutRegisterPageLoadedEvent $event): void
    {
        $cart = $event->getPage()->getCart();
        $this->upsertIctCartWishlist($cart, $event->getSalesChannelContext());
    }

    protected function upsertIctCartWishlist(Cart $cart, SalesChannelContext $context, bool $cartShouldExist = false): void
    {
        $salesChannelId = $context->getSalesChannel()->getId();
        $active = $this->systemConfigService->get('ICTECH_LimitedStockNotification.config.active', $salesChannelId);
        $configStock = $this->systemConfigService->get('ICTECH_LimitedStockNotification.config.limitedStockAlert', $salesChannelId);

        $customer = $context->getCustomer();

        if (is_null($customer)) {
            return;
        }
        $salesChannelDomainId = $this->currentRequest->attributes->get('sw-domain-id');

        if (is_null($salesChannelDomainId)) {
            return;
        }

        $criteria = new Criteria();
        # Get cart by customer email to avoid cart duplication.
        $criteria->addFilter(new EqualsFilter('email', $customer->getEmail()));
        $criteria->addFilter(new EqualsFilter('cartToken', $cart->getToken()));
        $cartResult = $this->ictCartWishlistRepository->search($criteria, $context->getContext());

        if ($cartShouldExist && $cartResult->count() <= 0) {
            return;
        }

        $lineItems = json_encode($cart->getLineItems()->getElements());

        if ($lineItems === false) {
            return;
        }

        $lineItems = json_decode($lineItems, true);

        // Filter line items based on the stock condition
        $filteredLineItems = array_filter($lineItems, function ($cartData) use ($configStock) {
            return ($configStock >= $cartData['deliveryInformation']['stock'])
                && ($cartData['deliveryInformation']['stock'] > 0) && (! $cartData['deliveryInformation']['stock'] == 0);
        });

        if ($active === true) {
            $data = [
                'id' => $cartResult->first() ? $cartResult->first()->getId() : Uuid::randomHex(),
                'cartToken' => $cart->getToken(),
                'lineItems' => $filteredLineItems,
                'currencyId' => $context->getCurrency()->getId(),
                'paymentMethodId' => $context->getPaymentMethod()->getId(),
                'shippingMethodId' => $context->getShippingMethod()->getId(),
                'countryId' => $context->getShippingLocation()->getCountry()->getId(),
                'salesChannelId' => $context->getSalesChannel()->getId(),
                'salesChannelDomainId' => $salesChannelDomainId,
                'customerId' => $customer->getId(),
                'email' => $customer->getEmail(),
            ];

            $this->ictCartWishlistRepository->upsert([$data], $context->getContext());

            $criteria = new Criteria();
            $criteria->addFilter(new EqualsFilter('email', $customer->getEmail()));
            $criteria->addFilter(new EqualsFilter('cartToken', $cart->getToken()));
            $cartWishlistData = $this->ictCartWishlistRepository->search($criteria, $context->getContext())->first();

            if (empty($cartWishlistData->getLineItems())) {
                $this->ictCartWishlistRepository->delete([
                    [
                        'id' => $cartWishlistData->getId(),
                    ],
                ], $context->getContext());
            }
        }
    }
}
