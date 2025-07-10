<?php

declare(strict_types=1);

namespace Zeobv\BundleProducts\Service;

use Shopware\Core\System\SystemConfig\SystemConfigService;

class ConfigService
{
    public const DISPLAY_MODE_NOTE_WITH_MODAL = 'note_with_modal';
    public const DISPLAY_MODE_CROSS_SELLING = 'cross_selling';
    public const CONFIG_PREFIX = 'ZeobvBundleProducts.config.';

    protected SystemConfigService $configService;

    public function __construct(
        SystemConfigService $configService
    ) {

        $this->configService = $configService;
    }

    public function recalculatePricesOfProductsInBundle(?string $salesChannelId = null): bool
    {
        return $this->configService->getBool(self::CONFIG_PREFIX . 'recalculatePricesOfProductsInBundle', $salesChannelId);
    }

    public function bundleProductPriceMode(?string $salesChannelId = null): string
    {
        return $this->configService->getString(self::CONFIG_PREFIX . 'bundleProductPriceMode', $salesChannelId);
    }

    public function enableBundleProductStockManagement(?string $salesChannelId = null): bool
    {
        return $this->configService->getBool(self::CONFIG_PREFIX . 'enableBundleProductStockManagement', $salesChannelId);
    }

    public function markBundleProductUnavailable(?string $salesChannelId = null): bool
    {
        return $this->configService->getBool(self::CONFIG_PREFIX . 'markBundleProductUnavailable', $salesChannelId);
    }

    public function overrideStockFieldOfBundleProduct(?string $salesChannelId = null): bool
    {
        return $this->configService->getBool(self::CONFIG_PREFIX . 'overrideStockFieldOfBundleProduct', $salesChannelId);
    }

    public function divideStockByItemQuantity(?string $salesChannelId = null): bool
    {
        return $this->configService->getBool(self::CONFIG_PREFIX . 'divideStockByItemQuantity', $salesChannelId);
    }

    public function disableBundleProductItemPrices(?string $salesChannelId = null): bool
    {
        return $this->configService->getBool(self::CONFIG_PREFIX . 'disableBundleProductItemPrices', $salesChannelId);
    }

    public function useCustomLineItemType(?string $salesChannelId = null): bool
    {
        return $this->configService->getBool(self::CONFIG_PREFIX . 'useCustomLineItemType', $salesChannelId);
    }

    public function getOverrideMaxPurchase(?string $salesChannelId = null): bool
    {
        return $this->configService->getBool(self::CONFIG_PREFIX . 'overrideMaxPurchase', $salesChannelId);
    }

    public function getOverrideBundleWeight(?string $salesChannelId = null): bool
    {
        return $this->configService->getBool(self::CONFIG_PREFIX . 'overrideBundleWeight', $salesChannelId);
    }

    public function getBundleWeightOverride(?string $salesChannelId = null): float
    {
        return $this->configService->getFloat(self::CONFIG_PREFIX . 'bundleWeightOverride', $salesChannelId);
    }
    public function overridePurchasePrice(?string $salesChannelId = null): bool
    {
        return $this->configService->getBool(self::CONFIG_PREFIX . 'overridePurchasePrice', $salesChannelId);
    }

    public function useLegacyBundleFitPriceCalculation(?string $salesChannelId): bool
    {
        return $this->configService->getBool(self::CONFIG_PREFIX . 'useLegacyBundleFitPriceCalculation', $salesChannelId);
    }

    public function getDisableReferenceToBundleProductsFromProductDetailPage(?string $salesChannelId): bool
    {
        return $this->configService->getBool(self::CONFIG_PREFIX . 'disableReferenceToBundleProductsFromProductDetailPage', $salesChannelId);
    }

    public function getMaxNumberOfProductDetailBundleReferences(?string $salesChannelId): int
    {
        return $this->configService->getInt(self::CONFIG_PREFIX . 'maxNumberOfProductDetailBundleReferences', $salesChannelId);
    }

    public function getBundleReferencesDisplayMode(?string $salesChannelId): string
    {
        return $this->configService->getString(self::CONFIG_PREFIX . 'bundleReferencesDisplayMode', $salesChannelId);
    }

    public function getHideBundleChildlineItemsOnOrderConfirmationMail(?string $salesChannelId): int
    {
        return $this->configService->getInt(self::CONFIG_PREFIX . 'hideBundleChildlineItemsOnOrderConfirmationMail', $salesChannelId);
    }

    public function isActive(?string $salesChannelId = null): bool
    {
        return $this->configService->getBool(self::CONFIG_PREFIX . 'active', $salesChannelId);
    }

    public function getBundleChildlineItemsPriceZeroInDhlShippingLabel(?string $salesChannelId): bool
    {
        return $this->configService->getBool(self::CONFIG_PREFIX . 'bundleChildlineItemsPriceZeroInDhlShippingLabel', $salesChannelId);
    }
}
