<?php

declare(strict_types=1);

namespace Zeobv\BundleProducts\Service;

use Shopware\Core\Framework\Context;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Content\Product\ProductCollection;
use Zeobv\BundleProducts\Factory\ProductBundleFactory;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Zeobv\BundleProducts\Struct\BundleProduct\ProductBundle;
use Zeobv\BundleProducts\Transformer\BundleProductTransformer;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Zeobv\BundleProducts\Struct\BundleProduct\BundleConnectionData;
use Zeobv\BundleProducts\Struct\BundleProduct\CalculatedBundlePrice;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;
use Shopware\Core\Content\Product\SalesChannel\Price\AbstractProductPriceCalculator;

class BundleProductReconfigurator
{
    public function __construct(
        private readonly EntityRepository $productRepository,
        private readonly ProductBundleFactory $productBundleFactory,
        private readonly BundleProductTransformer $bundleProductTransformer,
        private readonly BundlePriceCalculator $bundlePriceCalculator,
        private readonly AbstractProductPriceCalculator $productPriceCalculator,
    ) {
    }

    /** $itemSelection [[connectionId: string] => [productId: string, qty: int]] */
    public function determineCalculatedBundlePrice(
        string $bundleProductId,
        array $itemSelection,
        SalesChannelContext $context,
        int $bundleQty = 1
    ): CalculatedBundlePrice {
        $bundleProductEntity = $this->getBundleProduct($bundleProductId, $context->getContext());

        /** @var ProductBundle $productBundle */
        $productBundle = $bundleProductEntity->getExtension(ProductBundle::EXTENSION_NAME);

        $productsInBundle = $this->getProductsOrRespectiveChildrenByProductIds(
            $productBundle->getProducts(),
            $itemSelection
        );

        # Set the quantities for the products in the bundle
        foreach ($productsInBundle as $connectionId => $productStillInBundle) {
            $currentQuantity = $itemSelection[$connectionId]['qty'];

            # We need to set the unique identifier to the connection Id
            # to ensure it doesn't get filtered out when passing the
            # productsInBundle to a ProductCollection when we have
            # seemingly duplicate products in the array
            $productStillInBundle->setUniqueIdentifier($connectionId);

            # We update the quantity of the bundle connection data to
            # recalculate the price based on this quantity
            $bundleConnectionData = $productStillInBundle->getExtension(BundleConnectionData::EXTENSION_NAME);
            $productStillInBundle->addExtension(
                BundleConnectionData::EXTENSION_NAME,
                new BundleConnectionData(
                    $bundleConnectionData->getId(),
                    $bundleConnectionData->getBundleProductId(),
                    $bundleConnectionData->getProductId(),
                    $currentQuantity,
                    $bundleConnectionData->getPosition(),
                    $bundleConnectionData->isModifiable(),
                    $bundleConnectionData->isOptional(),
                    $bundleConnectionData->getComment()
                )
            );
        }

        $productConfig = $bundleProductEntity->getCustomFields() ?: [];
        $productBundle = $this->productBundleFactory->create(
            $bundleProductId,
            new ProductCollection($productsInBundle),
            $productConfig
        );

        $salesChannelId = $context->getSalesChannelId();
        # This will make the $bundleProductEntity adopt all applicable changes
        # from the productBundle. Things like, price, availability, stock, etc.
        $this->bundleProductTransformer->transform($bundleProductEntity, $productBundle, $salesChannelId);

        # We have been working with a ProductEntity so far to ensure price integrity
        # Now we need to convert it to a SalesChannelProductEntity to calculate the price
        # After calculate() the product will contain its $calculatedPrice and $calculatedPrices
        $salesChannelBundleProduct = SalesChannelProductEntity::createFrom($bundleProductEntity);
        $this->productPriceCalculator->calculate(
            [$salesChannelBundleProduct],
            $context
        );

        return $this->bundlePriceCalculator->createForBundleProduct(
            $salesChannelBundleProduct,
            $context,
            $bundleQty
        );
    }

    private function getProductsOrRespectiveChildrenByProductIds(array $productFromBundle, array $itemSelection): array
    {
        $productsInBundle = [];

        /** [[connectionId: string] => [productId: string, qty: int]] */
        foreach ($itemSelection as $connectionId => $data) {
            $selectedProductId = $data['productId'];
            $product = $productFromBundle[$connectionId] ?? null;

            if (!$product instanceof ProductEntity) {
                continue;
            }

            if ($product->getId() === $selectedProductId) {
                $productsInBundle[$connectionId] = $product;
                continue;
            }

            if (
                !$product->getChildren()
                || !$product->getChildren()->has($selectedProductId)
            ) {
                continue;
            }

            /** @var ProductEntity $child */
            $child = $product->getChildren()->get($selectedProductId);

            # We need to copy the extensions from the parent to the child
            # to ensure the bundle connection data is passed on
            $child->setExtensions($product->getExtensions());

            $productsInBundle[$connectionId] = $child;
        }

        return $productsInBundle;
    }

    private function getBundleProduct(string $bundleProductId, Context $context): ProductEntity
    {
        $criteria = new Criteria([$bundleProductId]);

        /** @var ProductEntity|null $product */
        $product = $this->productRepository->search($criteria, $context)->first();

        if (!$product instanceof ProductEntity) {
            throw new \RuntimeException('Bundle product not found');
        }

        return $product;
    }
}
