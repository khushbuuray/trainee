<?php

declare(strict_types=1);

namespace ICTECH_ReminderEmailWishlist\Subscriber;

use function array_map;

use Shopware\Core\Checkout\Customer\Event\WishlistProductAddedEvent;
use Shopware\Core\Checkout\Customer\Event\WishlistProductRemovedEvent;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\Framework\Uuid\Uuid;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class WishlistProductAddedSubscriber implements EventSubscriberInterface
{
    private EntityRepository $customerWishlistProductRepository;
    private EntityRepository $customWishlistRepository;
    private EntityRepository $productRepository;
    private EntityRepository $customRestockRepository;

    public function __construct(
        EntityRepository $customerWishlistProductRepository,
        EntityRepository $customWishlistRepository,
        EntityRepository $productRepository,
        EntityRepository $customRestockRepository
    ) {
        $this->customerWishlistProductRepository = $customerWishlistProductRepository;
        $this->customWishlistRepository = $customWishlistRepository;
        $this->productRepository = $productRepository;
        $this->customRestockRepository = $customRestockRepository;
    }

    public static function getSubscribedEvents(): array
    {
        // Return the events to listen to as an array like this: <event to listen to> => <method to execute>
        return [
            WishlistProductAddedEvent::class => 'onWishlistProductAdd',
            WishlistProductRemovedEvent::class => 'onWishlistProductRemove',
        ];
    }

    public function onWishlistProductAdd(WishlistProductAddedEvent $event): void
    {
        $cusWishlistId = $event->getWishlistId();
        $salesChannelId = $event->getSalesChannelContext()->getSalesChannelId();

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('id', $event->getProductId()));
        $product = $this->productRepository->search($criteria, $event->getContext())->first();

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('customerWishlistId', $cusWishlistId));
        $wishlistData = $this->customWishlistRepository->search($criteria, $event->getContext())->getElements();
        // store product detail in a restocked table
        $restockData = [];
        $restockData[] = [
            'id' => Uuid::randomHex(),
            'productId' => $product->getId(),
            'token' => null,
            'stock' => $product->getStock(),
            'currencyId' => $event->getSalesChannelContext()->getCurrencyId(),
            'salesChannelId' => $salesChannelId,
            'customerWishlistId' => $cusWishlistId,
            'customerId' => $event->getSalesChannelContext()->getCustomerId(),
        ];
        $this->customRestockRepository->upsert($restockData, $event->getContext());

        if (count($wishlistData) == 0) {
            $data = [];
            $data[] = [
                'id' => Uuid::randomHex(),
                'customerWishlistId' => $cusWishlistId,
                'currencyId' => $event->getSalesChannelContext()->getCurrencyId(),
                'salesChannelId' => $salesChannelId,
            ];
            $this->customWishlistRepository->upsert($data, $event->getContext());
        }
    }

    public function onWishlistProductRemove(WishlistProductRemovedEvent $event): void
    {
        $wishlistId = $event->getWishlistId();
        $customerId = $event->getSalesChannelContext()->getCustomerId();
        $productId = $event->getProductId();

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('wishlistId', $wishlistId));
        $wishlistCount = $this->customerWishlistProductRepository->search($criteria, $event->getContext())->getTotal();
        if ($wishlistCount === 0) {
            $criteria = new Criteria();
            $criteria->addFilter(new EqualsFilter('customerWishlistId', $wishlistId));
            $getWishlistReminderData = $this->customWishlistRepository->search($criteria, $event->getContext())->first();
            if ($getWishlistReminderData) {
                $this->customWishlistRepository->delete([['id' => $getWishlistReminderData->id]], $event->getContext());
            }
        }

        $restockCriteria = new Criteria();
        $restockCriteria->addFilter(
            new MultiFilter(
                MultiFilter::CONNECTION_AND,
                [
                    new EqualsFilter('customerWishlistId', $wishlistId),
                    new EqualsFilter('customerId', $customerId),
                    new EqualsFilter('productId', $productId),

                ]
            )
        );

        $restockData = $this->customRestockRepository->searchIds($restockCriteria, $event->getContext());

        $restockDataIds = array_map(static function ($id) {
            return ['id' => $id];
        }, $restockData->getIds());
        if ($restockDataIds === []) {
            return;
        }
        $this->customRestockRepository->delete($restockDataIds, $event->getContext());
    }
}
