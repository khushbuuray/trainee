<?php

declare(strict_types=1);

namespace Zeobv\BundleProducts\Core\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Checkout\Order\Aggregate\OrderLineItem\OrderLineItemCollection;
use Shopware\Core\Checkout\Order\Aggregate\OrderLineItem\OrderLineItemEntity;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Checkout\Order\OrderEvents;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent;

class SortOrderLineItemsForDocumentSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            OrderEvents::ORDER_LOADED_EVENT => 'onOrderLoaded',
        ];
    }

    public function onOrderLoaded(EntityLoadedEvent $event): void
    {
        if (count($event->getIds()) > 1) {
            return;
        }

        /** @var OrderEntity $order */
        foreach ($event->getEntities() as $order) {
            if (!$order->getLineItems() instanceof OrderLineItemCollection) {
                continue;
            }

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
}
