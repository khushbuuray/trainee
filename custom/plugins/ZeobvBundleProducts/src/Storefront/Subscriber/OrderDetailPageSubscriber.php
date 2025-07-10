<?php

declare(strict_types=1);

namespace Zeobv\BundleProducts\Storefront\Subscriber;

use Shopware\Core\Checkout\Cart\Event\CartBeforeSerializationEvent;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Checkout\Order\Aggregate\OrderLineItem\OrderLineItemEntity;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Storefront\Page\Account\Order\AccountOrderPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class OrderDetailPageSubscriber
 *
 * @package Zeobv\BundleProducts\Storefront\Subscriber
 */
class OrderDetailPageSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            AccountOrderPageLoadedEvent::class => 'onOrderPageLoaded',
            CartBeforeSerializationEvent::class => 'onCartBeforeSerialization',
        ];
    }

    public function onOrderPageLoaded(AccountOrderPageLoadedEvent $event): void
    {
        $orders = $event->getPage()->getOrders();

        /** @var OrderEntity $order */
        foreach ($orders as $order) {
            # Order by position, but try to keep children under bundle products
            $order->getLineItems()->sort(static function (OrderLineItemEntity $a, OrderLineItemEntity $b) {
                return $a->getPosition() > $b->getPosition() || $a->getParentId() !== null;
            });

            # place promotions and credit line at the bottom
            $order->getLineItems()->sort(static function (OrderLineItemEntity $a, OrderLineItemEntity $b) {
                return $a->getType() === LineItem::PROMOTION_LINE_ITEM_TYPE || $a->getType() === LineItem::CREDIT_LINE_ITEM_TYPE;
            });
        }
    }
    public function onCartBeforeSerialization(CartBeforeSerializationEvent $event): void
    {
        $event->addCustomFieldToAllowList('zeobvBundleProductsShowOnStorefront');
        $event->addCustomFieldToAllowList('zeobvBundleProductsShowItemPricesOnStorefront');
        $event->addCustomFieldToAllowList('zeobvBundleProductsShowItemsTotalPriceOnStorefront');
    }
}
