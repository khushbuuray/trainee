<?php

declare(strict_types=1);

namespace Zeobv\BundleProducts\Struct\BundleProduct;

class ProductBundleConfig
{
    public function __construct(
        private readonly string $priceMode,
        private readonly bool $disableBundleProductItemPrices,
        private readonly bool $overrideStockFieldOfBundleProduct,
        private readonly bool $overrideMaxPurchaseFieldOfBundleProduct,
        private readonly bool $divideStockByItemQuantity,
        private readonly bool $inheritAvailabilityFromChildren,
        private readonly bool $overrideWeightFieldOfBundleProduct,
        private readonly float $bundleWeightOverrideValue,
        private readonly float $productBundleDiscountPercentage,
        private readonly bool $allowQuantitySelection,
        private readonly bool $allowItemSelection,
        private readonly bool $allowVariantSelection
    ) {
    }

    public function getPriceMode(): string
    {
        return $this->priceMode;
    }

    public function isDisableBundleProductItemPrices(): bool
    {
        return $this->disableBundleProductItemPrices;
    }

    public function isOverrideStockFieldOfBundleProduct(): bool
    {
        return $this->overrideStockFieldOfBundleProduct;
    }

    public function isOverrideMaxPurchaseFieldOfBundleProduct(): bool
    {
        return $this->overrideMaxPurchaseFieldOfBundleProduct;
    }

    public function isDivideStockByItemQuantity(): bool
    {
        return $this->divideStockByItemQuantity;
    }

    public function isOverrideWeightFieldOfBundleProduct(): bool
    {
        return $this->overrideWeightFieldOfBundleProduct;
    }

    public function getBundleWeightOverrideValue(): float
    {
        return $this->bundleWeightOverrideValue;
    }

    public function isInheritAvailabilityFromChildren(): bool
    {
        return $this->inheritAvailabilityFromChildren;
    }

    public function getProductBundleDiscountPercentage(): float
    {
        return $this->productBundleDiscountPercentage;
    }

    public function isAllowQuantitySelection(): bool
    {
        return $this->allowQuantitySelection;
    }

    public function isAllowItemSelection(): bool
    {
        return $this->allowItemSelection;
    }

    public function isAllowVariantSelection(): bool
    {
        return $this->allowVariantSelection;
    }
}
