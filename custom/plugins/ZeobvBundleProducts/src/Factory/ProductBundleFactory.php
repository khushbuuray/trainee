<?php

declare(strict_types=1);

namespace Zeobv\BundleProducts\Factory;

use Shopware\Core\Content\Product\ProductCollection;
use Zeobv\BundleProducts\Builder\ProductBundleBuilder;
use Zeobv\BundleProducts\Service\ConfigService;
use Zeobv\BundleProducts\Struct\BundleProduct\BundlePrice;
use Zeobv\BundleProducts\Struct\BundleProduct\ProductBundle;
use Zeobv\BundleProducts\Struct\BundleProduct\ProductBundleConfig;

class ProductBundleFactory
{
    protected ConfigService $configService;

    public function __construct(
        ConfigService $configService
    ) {
        $this->configService = $configService;
    }

    public function create(string $bundleProductId, ProductCollection $productsInBundle, array $productConfig = []): ProductBundle
    {
        $bundlePriceMode = $productConfig['zeobvBundleProductPriceMode'] ?? $this->configService->bundleProductPriceMode();

        $priceModeIsSum = $bundlePriceMode === BundlePrice::BUNDLE_PRICE_MODE_SUM;
        $allowVariantSelection = $priceModeIsSum;
        $allowQtySelection = $priceModeIsSum && ($productConfig['zeobvBundleProductsAllowQtySelection'] ?? false);
        $allowItemSelection = $priceModeIsSum && ($productConfig['zeobvBundleProductsAllowItemSelection'] ?? false);

        $productBundleBuilder = new ProductBundleBuilder(
            $bundleProductId,
            new ProductBundleConfig(
                $bundlePriceMode,
                $this->configService->disableBundleProductItemPrices(),
                $this->configService->overrideStockFieldOfBundleProduct(),
                $this->configService->getOverrideMaxPurchase(),
                $this->configService->divideStockByItemQuantity(),
                $this->configService->markBundleProductUnavailable(),
                $this->configService->getOverrideBundleWeight(),
                $this->configService->getBundleWeightOverride(),
                $productConfig['zeobvBundleProductsDiscountPercentage'] ?? 0,
                $allowQtySelection,
                $allowItemSelection,
                $allowVariantSelection
            )
        );

        foreach ($productsInBundle as $productEntity) {
            $productBundleBuilder->addProduct($productEntity);
        }

        return $productBundleBuilder->output();
    }
}
