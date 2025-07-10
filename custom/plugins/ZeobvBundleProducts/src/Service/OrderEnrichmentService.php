<?php

declare(strict_types=1);

namespace Zeobv\BundleProducts\Service;

use Shopware\Core\Checkout\Cart\Order\OrderConverter;
use Shopware\Core\Framework\Context;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\Checkout\Cart\Processor;
use Shopware\Core\Framework\Struct\Struct;
use Shopware\Core\Checkout\Cart\CartBehavior;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Checkout\Cart\LineItemFactoryRegistry;
use Shopware\Core\Checkout\Cart\SalesChannel\CartService;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Zeobv\BundleProducts\Struct\BundleProduct\ProductBundle;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Zeobv\BundleProducts\Struct\BundleProduct\BundleConnectionData;
use Shopware\Core\System\SalesChannel\Entity\SalesChannelRepository;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextService;
use Shopware\Core\Checkout\Order\Aggregate\OrderDelivery\OrderDeliveryEntity;
use Shopware\Core\Checkout\Order\Aggregate\OrderLineItem\OrderLineItemEntity;
use Zeobv\BundleProducts\Struct\BundleProduct\LineItem as BundleProductLineItem;
use Shopware\Core\Checkout\Order\Aggregate\OrderLineItem\OrderLineItemCollection;
use Shopware\Core\System\SalesChannel\Context\AbstractSalesChannelContextFactory;

class OrderEnrichmentService
{
    public function __construct(
        private readonly EntityRepository $orderRepository,
        private readonly SalesChannelRepository $productRepository,
        private readonly EntityRepository $orderLineItemRepository,
        private readonly EntityRepository $orderDeliveryPositionRepository,
        private readonly AbstractSalesChannelContextFactory $salesChannelContextFactory,
        private readonly CartService $cartService,
        private readonly Processor $processor,
        private readonly LineItemFactoryRegistry $lineItemFactory
    ) {
    }

    public function enrich(string $orderId, Context $context): void
    {
        $order = $this->getOrder($orderId, $context);

        if ($order === null) {
            return;
        }

        // We get only the bundle child line items
        $childLineItems = $this->filterBundleChildLineItems($order->getLineItems());

        /*
         *  If the order does not have any bundle child line items,
         *  it might mean the order was created via the API and the bundle
         *  information was not added yet. In that case we enrich the order
         *  with bundle information and filter the line items again.
        */
        if ($childLineItems->count() < 1) {
            $this->enrichOrderWithBundleInformation($order);

            $childLineItems = $this->filterBundleChildLineItems($order->getLineItems());
        }

        // If there are no bundle child line items, it means we don't need to create order delivery positions
        if ($childLineItems->count() < 1) {
            return;
        }

        /** @var OrderDeliveryEntity|null */
        $delivery = $order->getDeliveries()->first();

        if ($delivery === null) {
            return;
        }

        // We create order delivery positions for the bundle child line items
        // since shopware does not create them automatically.
        $this->createOrderDeliveryPositionsForOrderLineItems(
            $childLineItems,
            $delivery->getId(),
            $context
        );
    }

    public function enrichOrderWithBundleInformation(OrderEntity $order): void {
        $salesChannelContext = $this->createSalesChannelContextFromOrder($order);
        $bundleProductEntities = $this->getBundleProductsForOrder($order, $salesChannelContext);

        if ($bundleProductEntities === null || $bundleProductEntities->count() < 1) {
            return;
        }

        $cart = $this->createCartWithBundleProductBasedOnOrder(
            $order,
            $bundleProductEntities,
            $salesChannelContext
        );

        /*
         * The newOrderLineItems contains bundle product order line items enriched with
         * bundle information in the payload as well as newly resolved child line items from the cart.
        */
        $newOrderLineItems = $this->createNewOrderLineItemsBasedOnOrderAndCart($order, $cart);

        if ($newOrderLineItems === null) {
            return;
        }

        $this->orderLineItemRepository->upsert(
            $this->jsonSerialiseOrderLineItems($newOrderLineItems),
            $salesChannelContext->getContext()
        );

        $order->setLineItems($newOrderLineItems);
    }

    public function createOrderDeliveryPositionsForOrderLineItems(OrderLineItemCollection $orderLineItemCollection, string $orderDeliveryId, Context $context): void
    {
        $orderDeliveryPositionsData = $orderLineItemCollection->map(
            static function (OrderLineItemEntity $orderLineItem) use ($orderDeliveryId) {
                return [
                    'orderId' => $orderLineItem->getOrderId(),
                    'orderDeliveryId' => $orderDeliveryId,
                    'orderLineItemId' => $orderLineItem->getId(),
                    'price' => $orderLineItem->getPrice(),
                    'unitPrice' => $orderLineItem->getUnitPrice(),
                    'totalPrice' => $orderLineItem->getTotalPrice(),
                    'quantity' => $orderLineItem->getQuantity(),
                ];
            }
        );

        $this->orderDeliveryPositionRepository->create(array_values($orderDeliveryPositionsData), $context);
    }

    private function createNewOrderLineItemsBasedOnOrderAndCart(OrderEntity $order, Cart $cart): ?OrderLineItemCollection
    {
        $lineItemsByRefId = $this->getLineItemsByRefId($cart);

        if ($lineItemsByRefId === []) {
            return null;
        }

        $newOrderLineItems = new OrderLineItemCollection();

        /** @var OrderLineItemEntity $orderLineItem */
        foreach ($order->getLineItems() as $orderLineItem) {
            // We only want to process product line items
            if ($orderLineItem->getType() !== LineItem::PRODUCT_LINE_ITEM_TYPE) {
                continue;
            }

            // If the (bundle) product is not in the cart, we skip it
            /** @var LineItem|null $cartLineItem */
            $cartLineItem = $lineItemsByRefId[$orderLineItem->getReferencedId()] ?? null;
            if ($cartLineItem === null) {
                continue;
            }

            // The bundle product in the order might miss some bundle information
            // so we merge the existing payload with the payload from the cart
            $existingPayload = $orderLineItem->getPayload() ?? [];
            $newPayload = $cartLineItem->getPayload();
            $mergedPayload = array_merge($existingPayload, $newPayload);
            $orderLineItem->setPayload($mergedPayload);

            // Finished updating the bundle product line item, add it to the new order line item collection
            $newOrderLineItems->add($orderLineItem);

            // We want the child line items to be sequential after the parent bundle product line item
            $position = $orderLineItem->getPosition();

            /*
             * In the cart the bundle items are added as children of the bundle product line item
             * in the order however they are not nested but reference to their parent via a parentId
             * here we loop over the children of the bundle product line item and create new order line items for each
            */
            foreach ($cartLineItem->getChildren() as $childLineItem) {
                /** @var LineItem $childLineItem */
                $orderChildLineItem = $this->createOrderLineItemFromCartLineItem($childLineItem);

                $orderChildLineItem->setPosition($position++);
                $orderChildLineItem->setParentId($orderLineItem->getId());
                $orderChildLineItem->setOrderId($order->getId());

                $newOrderLineItems->add($orderChildLineItem);
            }
        }

        return $newOrderLineItems;
    }

    private function getLineItemsByRefId(Cart $cart): array
    {
        $lineItemsByRefId = [];

        /** @var LineItem $lineItem */
        foreach ($cart->getLineItems() as $lineItem) {
            if ($lineItem->getType() === LineItem::PRODUCT_LINE_ITEM_TYPE) {
                $lineItemsByRefId[$lineItem->getReferencedId()] = $lineItem;
            }
        }

        return $lineItemsByRefId;
    }

    private function createOrderLineItemFromCartLineItem(LineItem $cartLineItem): OrderLineItemEntity
    {
        $orderChildLineItem = new OrderLineItemEntity();
        $orderChildLineItem->setId(Uuid::randomHex());
        $orderChildLineItem->setIdentifier($cartLineItem->getId());
        $orderChildLineItem->setReferencedId($cartLineItem->getReferencedId());
        $orderChildLineItem->setProductId($cartLineItem->getReferencedId());
        $orderChildLineItem->setQuantity($cartLineItem->getQuantity());
        $orderChildLineItem->setUnitPrice($cartLineItem->getPrice()->getUnitPrice());
        $orderChildLineItem->setTotalPrice($cartLineItem->getPrice()->getTotalPrice());
        $orderChildLineItem->setType($cartLineItem->getType());
        $orderChildLineItem->setLabel($cartLineItem->getLabel());
        $orderChildLineItem->setGood($cartLineItem->isGood());
        $orderChildLineItem->setRemovable($cartLineItem->isRemovable());
        $orderChildLineItem->setStackable($cartLineItem->isStackable());
        $orderChildLineItem->setPrice($cartLineItem->getPrice());
        $orderChildLineItem->setPayload($cartLineItem->getPayload());

        return $orderChildLineItem;
    }

    private function createCartWithBundleProductBasedOnOrder(
        OrderEntity $order,
        EntityCollection $bundleProductEntities,
        SalesChannelContext $salesChannelContext
    ): Cart {
        $salesChannelContext->setPermissions(array_merge(
            $salesChannelContext->getPermissions(),
            OrderConverter::ADMIN_EDIT_ORDER_PERMISSIONS
        ));

        // Create a cart
        /** @var Cart $cart */
        $cart = $this->cartService->createNew($order->getId());

        // Add bundle products to cart
        /** @var ProductEntity $bundleProduct */
        foreach ($bundleProductEntities as $bundleProduct) {
            $lineItem = $this->lineItemFactory->create(
                [
                    'id' => Uuid::randomHex(),
                    'type' => LineItem::PRODUCT_LINE_ITEM_TYPE,
                    'referencedId' => $bundleProduct->getId(),
                    'quantity' => 1,
                    'payload' => [
                        'bundleOptional' => false,
                    ],
                ],
                $salesChannelContext
            );

            $cart->add($lineItem);
        }

        // Run cart collectors and processors
        return $this->processor->process(
            $cart,
            $salesChannelContext,
            new CartBehavior($salesChannelContext->getPermissions())
        );
    }

    private function filterBundleChildLineItems(OrderLineItemCollection $lineItems): OrderLineItemCollection
    {
        return $lineItems->filter(
            static function (OrderLineItemEntity $lineItem) {
                if ($lineItem->getParentId() === null) {
                    return false;
                }

                $payload = $lineItem->getPayload();

                if (
                    $payload === null
                    || !isset($payload['zeobvCustomLineItemType'])
                    || $payload['zeobvCustomLineItemType'] !== BundleProductLineItem::BUNDLE_PRODUCT_LINE_ITEM_TYPE
                ) {
                    return false;
                }

                return true;
            }
        );
    }

    private function getBundleProductsForOrder(OrderEntity $order, SalesChannelContext $context): ?EntityCollection
    {
        $productIds = $order->getLineItems()->filterByType(LineItem::PRODUCT_LINE_ITEM_TYPE)->map(
            fn(OrderLineItemEntity $orderLineItem) => $orderLineItem->getReferencedId()
        );

        if ($productIds === []) {
            return null;
        }

        $criteria = new Criteria();
        $criteria->setIds(array_values($productIds));
        $criteria->setLimit(count($productIds));

        return $this->productRepository->search($criteria, $context)->getEntities();
    }

    private function createSalesChannelContextFromOrder(OrderEntity $order): SalesChannelContext
    {
        $salesChannelId = $order->getSalesChannelId();
        $currencyId = $order->getCurrencyId();
        $languageId = $order->getLanguageId();
        $customerId = $order->getOrderCustomer()->getCustomerId();

        $token = $order->getId();

        $options = [
            SalesChannelContextService::CURRENCY_ID => $currencyId,
            SalesChannelContextService::LANGUAGE_ID => $languageId,
            SalesChannelContextService::CUSTOMER_ID => $customerId,
        ];

        $salesChannelContext = $this->salesChannelContextFactory->create(
            $token,
            $salesChannelId,
            $options
        );

        // Override thhe context rule ids with the order rule ids
        // if they are not null
        if (null !== $ruleIds = $order->getRuleIds()) {
            $salesChannelContext->getContext()->setRuleIds($ruleIds);
        }

        return $salesChannelContext;
    }

    private function jsonSerialiseOrderLineItems(OrderLineItemCollection $orderLineItemCollection): array
    {
        $serialisedOrderLineItems = [];

        /** @var OrderLineItemEntity $orderLineItem */
        foreach ($orderLineItemCollection as $orderLineItem) {
            $serialisedOrderLineItem = $orderLineItem->jsonSerialize();

            $this->recursivelySerializeStructs($serialisedOrderLineItem);

            unset(
                $serialisedOrderLineItem['_uniqueIdentifier'],
                $serialisedOrderLineItem['extensions'],
                $serialisedOrderLineItem['order'],
                $serialisedOrderLineItem['orderDeliveryPositions'],
                $serialisedOrderLineItem['cover'],
                $serialisedOrderLineItem['children'],
                $serialisedOrderLineItem['product'],
                $serialisedOrderLineItem['orderTransactionCaptureRefundPositions'],
                $serialisedOrderLineItem['downloads'],
                $serialisedOrderLineItem['promotion'],
                $serialisedOrderLineItem['orderVersionId'],
                $serialisedOrderLineItem['productVersionId'],
                $serialisedOrderLineItem['parentVersionId'],
                $serialisedOrderLineItem['promotionId'],
                $serialisedOrderLineItem['parent'],
            );

            $serialisedOrderLineItems[] = $serialisedOrderLineItem;
        }

        return $serialisedOrderLineItems;
    }

    private function recursivelySerializeStructs(array &$array): void
    {
        foreach ($array as $key => &$value) {
            if (is_array($value)) {
                $this->recursivelySerializeStructs($value);
            } elseif ($value instanceof Struct) {
                $array[$key] = $value->jsonSerialize();
                $this->recursivelySerializeStructs($array[$key]);
            }
        }
    }

    private function getOrder(string $orderId, Context $context): ?OrderEntity
    {
        $criteria = new Criteria([$orderId]);
        $criteria->addAssociations(['lineItems', 'deliveries']);

        return $this->orderRepository->search($criteria, $context)->first();
    }
}
