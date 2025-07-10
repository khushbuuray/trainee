<?php

declare(strict_types=1);

namespace Zeobv\BundleProducts\Decorator;

use Cbax\ModulOrderImportEbay\Components\OrderConverter;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\CartBehavior;
use Shopware\Core\Checkout\Cart\Processor;
use Shopware\Core\Content\Product\Cart\ProductCartProcessor;
use Shopware\Core\Checkout\Cart\LineItemFactoryHandler\ProductLineItemFactory;
use Shopware\Core\Framework\Struct\ArrayStruct;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SalesChannel\Context\AbstractSalesChannelContextFactory;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class CbaxOrderConverterDecorator extends OrderConverter
{
    public function __construct(
        private $decorated,
        private AbstractSalesChannelContextFactory $salesChannelContextFactory,
        private ProductLineItemFactory $lineItemFactory,
        private Processor $processor
    ) {
    }

    // We don't use strict types here because the parent method doesn't use them either
    public function convertOrderDataForImport($getOrder, $settings, $context): array
    {

        $order = $this->decorated->convertOrderDataForImport($getOrder, $settings, $context);

        if (count($order) > 0) {

            $salesChannelContext = $this->getSalesChannelContext($settings['sales_channel_id']);
            $tmpCart = new Cart($settings['sales_channel_id']);

            $tmpCart->addExtension('zeobvBundleProductTmpCart', new ArrayStruct(['temp' => true]));

            foreach ($order['associations']['externalOrderLineItems'] as $orderLineItem) {
                $processedCart = $this->processTempCart(
                    $tmpCart,
                    $orderLineItem['quantity'],
                    $orderLineItem['productId'],
                    $salesChannelContext
                );

                foreach ($processedCart->getLineItems()->getElements() as $lineItem) {
                    foreach ($lineItem->getChildren() as $lineItemBundleProduct) {
                        $importBundleDetail = [];
                        $label = $lineItemBundleProduct->getLabel();

                        if (!empty($orderLineItem['externalOrderNumber'])) {
                            $importBundleDetail['externalOrderNumber'] = $orderLineItem['externalOrderNumber'];
                        }

                        $importBundleDetail['externalProductNumber'] = $orderLineItem['externalProductNumber'];
                        $importBundleDetail['productNumber'] = $lineItemBundleProduct->getPayloadValue('productNumber');
                        $importBundleDetail['quantity'] = $lineItemBundleProduct->getQuantity();
                        $importBundleDetail['price'] = $lineItemBundleProduct->getPrice()->getTotalPrice();
                        $importBundleDetail['type'] = 'bundle_product_item';
                        $importBundleDetail['productId'] = $lineItemBundleProduct->getReferencedId();
                        $importBundleDetail['label'] = $label;
                        $importBundleDetail['taxId'] = $lineItemBundleProduct->getPayloadValue('taxId');
                        $importBundleDetail['parentId'] = $lineItem->getId();
                        $importBundleDetail['payload'] = ['options' => []];

                        foreach ($lineItemBundleProduct->getPrice()->getCalculatedTaxes()->getElements() as $taxData) {
                            $importBundleDetail['taxRate'] = $taxData->getTaxRate();
                        }

                        $order['associations']['externalOrderLineItems'][] = $importBundleDetail;
                    }
                }
            }
        }

        return $order;
    }

    private function processTempCart(Cart $tmpCart, int $quantity, string $productId, SalesChannelContext $salesChannelContext): Cart
    {
        $productLineItem = $this->lineItemFactory->create(
            [
                'id' => Uuid::randomHex(),
                'referencedId' => $productId,
                'quantity' => $quantity > 1
                    ? $quantity
                    : 1,
            ],
            $salesChannelContext
        );

        $tmpCart->add($productLineItem);

        try {
            # We need to add some permissions to include inactive and out of stock products in discount calculations
            $permissions = $salesChannelContext->getPermissions();
            $permissions[ProductCartProcessor::SKIP_PRODUCT_STOCK_VALIDATION] = true;
            $permissions[ProductCartProcessor::KEEP_INACTIVE_PRODUCT] = true;

            $processedCart = $this->processor->process(
                $tmpCart,
                $salesChannelContext,
                new CartBehavior($permissions)
            );
        } catch (\Throwable $e) {
            $processedCart = $this->processor->process(
                $tmpCart,
                $salesChannelContext,
                new CartBehavior($salesChannelContext->getPermissions())
            );
        }

        return $processedCart;
    }

    private function getSalesChannelContext(string $salesChannel): SalesChannelContext
    {
        return $this->salesChannelContextFactory->create(
            Uuid::randomHex(),
            $salesChannel
        );
    }
}
