<?php

declare(strict_types=1);

namespace Zeobv\BundleProducts\Core\Subscriber;

use Shopware\Core\Checkout\Order\OrderEvents;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Checkout\Order\Aggregate\OrderLineItem\OrderLineItemEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Shopware\Core\Framework\DataAbstractionLayer\EntityWriteResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;

/**
 * Subscriber that handles quantity updates for bundle product line items in orders.
 *
 * When the quantity of a bundle product line item is updated in an order, this subscriber
 * ensures that the quantities of all child line items (the individual products in the bundle)
 * are updated proportionally.
 *
 * For example:
 * - If a bundle containing 2x Product A and 3x Product B has its quantity changed from 1 to 3
 * - The child items will be updated to:
 *   - Product A: 6 units (2 * 3)
 *   - Product B: 9 units (3 * 3)
 *
 * This maintains the correct ratio of products within the bundle when order quantities change.
 */
class OrderLineItemQuantitySubscriber implements EventSubscriberInterface
{
    protected EntityRepository $orderLineItemRepository;

    public function __construct(
        EntityRepository $orderLineItemRepository
    )
    {
        $this->orderLineItemRepository = $orderLineItemRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            OrderEvents::ORDER_LINE_ITEM_WRITTEN_EVENT => 'onOrderLineItemWritten',
        ];
    }

    /**
     * Handles quantity updates for bundle line items
     *
     * When a bundle's quantity is updated, updates the quantities of all child line items
     * by multiplying their original quantities by the bundle's new quantity.
     *
     * Only processes:
     * - Update operations (not creates/deletes)
     * - Changes that include quantity updates
     * - Quantities greater than 1
     */
    public function onOrderLineItemWritten(EntityWrittenEvent $event): void
    {
        /** @var EntityWriteResult $writeResult */
        foreach ($event->getWriteResults() as $writeResult) {
            if (
                $writeResult->getOperation() !== EntityWriteResult::OPERATION_UPDATE
                || !$writeResult->hasPayload('quantity')
                || $writeResult->getProperty('quantity') <= 1
            ) {
                continue;
            }

            /** @var array|string $id */
            $id = $writeResult->getPrimaryKey();

            $id = !is_array($id) ? [$id] : $id;

            $criteria = new Criteria();
            $criteria->addFilter(new EqualsAnyFilter('parentId', $id));

            $result = $this->orderLineItemRepository->search($criteria, $event->getContext());

            if ($result->getTotal() <= 0) {
                continue;
            }

            $orderLineItemPatchData = [];

            /** @var OrderLineItemEntity $item */
            foreach ($result as $item) {
                $payload = $item->getPayload();
                if (!is_array($payload) || !key_exists('quantity', $payload)) {
                    continue 2;
                }

                $orderLineItemPatchData[] = [
                    'id' => $item->getId(),
                    'quantity' => $payload['quantity'] * $writeResult->getProperty('quantity'),
                ];
            }

            if (empty($orderLineItemPatchData)) {
                continue;
            }

            $this->orderLineItemRepository->update($orderLineItemPatchData, $event->getContext());
        }
    }
}
