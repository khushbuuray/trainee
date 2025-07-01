<?php

declare(strict_types=1);

namespace ICTECH_ReminderEmailWishlist\Subscriber;

use function array_map;

use Shopware\Core\Checkout\Cart\Event\AfterLineItemAddedEvent;
use Shopware\Core\Checkout\Cart\Event\AfterLineItemRemovedEvent;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\Framework\Uuid\Uuid;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CartProductAddedSubscriber implements EventSubscriberInterface
{
    private EntityRepository $customCartRepository;
    private EntityRepository $customRestockRepository;

    public function __construct(
        EntityRepository $customCartRepository,
        EntityRepository $customRestockRepository,
    ) {
        $this->customCartRepository = $customCartRepository;
        $this->customRestockRepository = $customRestockRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AfterLineItemAddedEvent::class => 'onCartProductAdd',
            AfterLineItemRemovedEvent::class => 'onCartProductRemove',
        ];
    }

    public function onCartProductAdd(AfterLineItemAddedEvent $event): void
    {
        if ($event->getsalesChannelContext()->getCustomerId() !== null && $event->getSalesChannelContext()->getCustomer()->getGuest() === true) {
            return;
        }
        $productId = null;
        foreach ($event->getLineItems() as $lineItem) {
            $productId = $lineItem->getId();
        }

        $customerId = $event->getsalesChannelContext()->getCustomerId();

        $cartProToken = $event->getCart()->getToken();
        $salesChannelId = $event->getSalesChannelContext()->getSalesChannelId();
        if ($event->getSalesChannelContext()->getCustomerId() !== null) {
            $criteria = new Criteria();
            $criteria->addFilter(new EqualsFilter('productId', $productId));
            $criteria->addFilter(new EqualsFilter('customerId', $customerId));
            $criteria->addFilter(new EqualsFilter('salesChannelId', $salesChannelId));

            $existingCartProducts = $this->customCartRepository->search($criteria, $event->getContext());
            if ($existingCartProducts->getTotal() === 0) {
                // If no existing product, add a new entry
                $data = [];
                $data[] = [
                    'id' => Uuid::randomHex(),
                    'token' => $cartProToken,
                    'salesChannelId' => $salesChannelId,
                    'customerId' => $customerId,
                    'productId' => $productId,
                    'currencyId' => $event->getContext()->getCurrencyId(),
                ];

                $this->customCartRepository->upsert($data, $event->getContext());
            }
            $cartProductData = $event->getLineItems();
            foreach ($cartProductData as $cart) {
                if ($cart->getPayload()) {
                    // store product detail in a restocked table
                    $restockData = [];
                    $restockData[] = [
                        'id' => Uuid::randomHex(),
                        'productId' => $cart->getId(),
                        'token' => $cartProToken,
                        'stock' => $cart->getPayload()['stock'],
                        'currencyId' => $event->getContext()->getCurrencyId(),
                        'salesChannelId' => $salesChannelId,
                        'customerWishlistId' => null,
                        'customerId' => $customerId,
                    ];

                    $this->customRestockRepository->upsert($restockData, $event->getContext());
                }
            }
        }
    }

    public function onCartProductRemove(AfterLineItemRemovedEvent $event): void
    {
        if ($event->getsalesChannelContext()->getCustomerId() !== null && $event->getSalesChannelContext()->getCustomer()->getGuest() === true) {
            return;
        }
        $productId = null;
        foreach ($event->getLineItems() as $item) {
            $productId = $item->getId();
        }

        $salesChannelId = $event->getSalesChannelContext()->getSalesChannelId();
        $customerId = $event->getSalesChannelContext()->getCustomerId();
        $currentToken = $event->getCart()->getToken();

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('productId', $productId));
        $criteria->addFilter(new EqualsFilter('customerId', $customerId));
        $criteria->addFilter(new EqualsFilter('salesChannelId', $salesChannelId));

        $getCartReminderData = $this->customCartRepository->search($criteria, $event->getContext())->first();

        if ($getCartReminderData && ! empty($getCartReminderData->getId())) {
            $this->customCartRepository->delete([['id' => $getCartReminderData->getId()]], $event->getContext());
        }

        $restockCriteria = new Criteria();
        $restockCriteria->addFilter(
            new MultiFilter(
                MultiFilter::CONNECTION_AND,
                [
                    new EqualsFilter('productId', $productId),
                    new EqualsFilter('customerId', $customerId),
                    new EqualsFilter('token', $currentToken)
                ]
            )
        );

        $restockData = $this->customRestockRepository->searchIds($restockCriteria, $event->getContext());

        $restockDataIds = array_map(static function ($id) {
            return ['id' => $id];
        }, $restockData->getIds());

        if (! empty($restockDataIds)) {
            $this->customRestockRepository->delete($restockDataIds, $event->getContext());
        }
    }
}
