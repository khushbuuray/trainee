<?php

declare(strict_types=1);

namespace Zeobv\BundleProducts\Transformer;

use Zeobv\BundleProducts\Service\ConfigService;
use Shopware\Core\Content\Product\ProductEntity;
use Zeobv\BundleProducts\Struct\BundleProduct\BundlePrice;
use Zeobv\BundleProducts\Struct\BundleProduct\ProductBundle;
use Shopware\Core\Content\Product\Aggregate\ProductPrice\ProductPriceEntity;
use Shopware\Core\Content\Product\Aggregate\ProductPrice\ProductPriceCollection;

/**
 * Transforms a regular product entity into a bundle product by applying bundle-specific properties and behaviors.
 *
 * This transformer handles:
 * - Product availability based on bundle configuration and stock levels
 * - Price calculations and overrides for bundle pricing
 * - Stock level management and virtual stock calculations
 * - Weight calculations based on bundle configuration
 * - Delivery time inheritance
 * - Order restrictions and purchase limits
 *
 * The transformation process modifies the product entity by reference, adding bundle-specific
 * data and overriding standard product behavior where needed.
 */
class BundleProductTransformer
{
    private ConfigService $configService;
    private ProductEntity $bundleProduct;
    private ProductBundle $productBundle;

    public function __construct(
        ConfigService $configService
    ) {
        $this->configService = $configService;
    }

    /**
     * Transform a product entity into a bundle product by reference.
     * 
     * Applies all bundle-specific transformations:
     * - Availability rules
     * - Price calculations
     * - Stock management
     * - Weight calculations  
     * - Delivery time settings
     * - Order restrictions
     * 
     * Finally adds the bundle extension to the product entity.
     */
    public function transform(ProductEntity $productEntity, ProductBundle $productBundle, ?string $salesChannelId = null): void
    {
        $this->bundleProduct = $productEntity;
        $this->productBundle = $productBundle;

        $this->overrideAvailability();

        $status = $this->configService->isActive($salesChannelId);

        if ($status){
            $this->overridePrices(
                $this->configService->overridePurchasePrice()
            );
        }

        $this->overrideStock();
        $this->overrideWeight();
        $this->overrideDeliveryTime();
        $this->overrideOrderability();

        $this->bundleProduct->addExtension(
            ProductBundle::EXTENSION_NAME,
            $this->productBundle
        );
    }

    /**
     * Overrides the product availability based on bundle configuration.
     * 
     * Handles two scenarios:
     * 1. When backorders are disabled - availability depends on stock
     * 2. When inheriting from children - availability depends on child products
     */
    public function overrideAvailability(): void
    {
        if ($this->productBundle->isBackordersDisabled()) {
            $this->bundleProduct->setAvailable($this->productBundle->isInStock());
        }

        if ($this->productBundle->getConfig()->isInheritAvailabilityFromChildren()) {
            $this->bundleProduct->setAvailable($this->productBundle->isAvailable());

            # We need to ensure the bundle product is not orderable if the bundle is not available
            # to do this we enforce the virtual available stock to 0 and disable backorders
            if (!$this->bundleProduct->getAvailable()) {
                $this->productBundle->setVirtualAvailableStock(0);
                $this->productBundle->setBackordersDisabled(true);
            }
        }
    }

    /**
     * Overrides product prices based on bundle configuration.
     * 
     * Handles:
     * - Bundle price mode calculations
     * - Advanced price rules
     * - Purchase price inheritance
     */
    public function overridePrices(bool $includePurchasePrices): void
    {
        if (
            $this->productBundle->getConfig()->getPriceMode() === BundlePrice::BUNDLE_PRICE_MODE_SUM
        ) {
            $advancedPrices = $this->productBundle->getAdvancedPrices()
                ? $this->productBundle->getAdvancedPrices()
                : new ProductPriceCollection([]);

            /** @var ProductPriceEntity $advancedPrice */
            foreach ($advancedPrices as $advancedPrice) {
                $advancedPrice->setProductId($this->bundleProduct->getId());
            }

            $this->bundleProduct->setPrice($this->productBundle->getBundlePrice()->getPrice());
            $this->bundleProduct->setPrices($advancedPrices);
        }

        if ($includePurchasePrices) {
            $this->bundleProduct->setPurchasePrices($this->productBundle->getBundlePurchasePrice()->getPrice());
        }
    }

    /**
     * Overrides product stock levels based on bundle configuration.
     * 
     * Handles:
     * - Virtual stock calculations
     * - Available stock overrides
     * - Stock field overrides
     */
    public function overrideStock(): void
    {
        if ($this->productBundle->getConfig()->isOverrideStockFieldOfBundleProduct()) {
            $virtualStock = $this->productBundle->getVirtualStock();

            if ($virtualStock === null) {
                return;
            }

            $this->bundleProduct->setStock($virtualStock);
        }

        $virtualAvailableStock = $this->productBundle->getVirtualAvailableStock();

        if ($virtualAvailableStock === null) {
            return;
        }

        $this->bundleProduct->setAvailableStock($virtualAvailableStock);
    }

    /**
     * Overrides product orderability settings.
     * 
     * Handles:
     * - Maximum purchase quantity
     * - Closeout status based on backorder settings
     */
    public function overrideOrderability(): void
    {
        if ($this->productBundle->getConfig()->isOverrideMaxPurchaseFieldOfBundleProduct()) {
            $this->bundleProduct->setMaxPurchase($this->productBundle->getMaxPurchase());
        }
        $this->bundleProduct->setIsCloseout($this->productBundle->isBackordersDisabled());
    }

    /**
     * Overrides product delivery time settings.
     * 
     * Applies the bundle's delivery time settings to the product
     * if a delivery time is configured for the bundle.
     */
    public function overrideDeliveryTime(): void
    {
        if ($this->productBundle->getDeliveryTime() !== null) {
            $this->bundleProduct->setDeliveryTimeId($this->productBundle->getDeliveryTime()->getId());
            $this->bundleProduct->setDeliveryTime($this->productBundle->getDeliveryTime());
        }
    }

    /**
     * Overrides product weight based on bundle configuration.
     * 
     * Handles two scenarios:
     * 1. Using an explicit weight override value
     * 2. Using the calculated bundle weight
     */
    public function overrideWeight(): void
    {
        $config = $this->productBundle->getConfig();

        if ($config->isOverrideWeightFieldOfBundleProduct()) {
            $this->bundleProduct->setWeight($config->getBundleWeightOverrideValue());
            return;
        }

        if ($this->productBundle->getWeight() > 0) {
            $this->bundleProduct->setWeight($this->productBundle->getWeight());
        }
    }
}
