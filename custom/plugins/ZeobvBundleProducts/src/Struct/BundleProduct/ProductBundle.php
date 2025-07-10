<?php

declare(strict_types=1);

namespace Zeobv\BundleProducts\Struct\BundleProduct;

use Shopware\Core\Content\Product\Aggregate\ProductPrice\ProductPriceCollection;
use Shopware\Core\Content\Product\Aggregate\ProductPrice\ProductPriceEntity;
use Shopware\Core\Content\Product\DataAbstractionLayer\CheapestPrice\CheapestPriceContainer;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;
use Shopware\Core\Framework\Struct\Struct;
use Shopware\Core\System\DeliveryTime\DeliveryTimeEntity;

class ProductBundle extends Struct
{
    public const EXTENSION_NAME = 'zeobvBundleProducts';

    protected ProductBundleConfig $bundleConfig;

    protected ?BundlePrice $bundlePrice = null;

    protected ?BundlePrice $bundlePurchasePrice = null;

    protected ?CheapestPriceContainer $cheapestPrice = null;

    protected bool $inStock = true;

    protected ?int $virtualStock = null;

    protected ?int $virtualAvailableStock = null;

    protected bool $backordersDisabled = false;

    protected ?DeliveryTimeEntity $deliveryTime = null;

    protected bool $available = true;

    protected bool $configurable = false;

    protected float $weight = 0.00;

    protected ?int $maxPurchase = null;

    protected array $products = [];

    protected array $salesChannelProducts = [];

    protected ?ProductPriceCollection $advancedPrices = null;

    public function __construct(
        private readonly string $bundleProductId,
    ) {
    }

    public function setConfig(ProductBundleConfig $config): void
    {
        $this->bundleConfig = $config;
    }

    public function getConfig(): ProductBundleConfig
    {
        return $this->bundleConfig;
    }

    public function getProducts(): array
    {
        return $this->products;
    }

    public function getSalesChannelProducts(): array
    {
        return $this->salesChannelProducts;
    }

    public function getBundlePrice(): BundlePrice
    {
        if ($this->bundlePrice === null) {
            $this->bundlePrice = new BundlePrice($this->bundleProductId, $this->bundleConfig->getPriceMode());
        }

        return $this->bundlePrice;
    }

    public function getBundlePurchasePrice(): BundlePrice
    {
        if ($this->bundlePurchasePrice === null) {
            $this->bundlePurchasePrice = new BundlePrice($this->bundleProductId, $this->bundleConfig->getPriceMode());
        }

        return $this->bundlePurchasePrice;
    }

    public function setInStock(bool $inStock): void
    {
        $this->inStock = $inStock;
    }

    public function isInStock(): bool
    {
        return $this->inStock;
    }

    public function setVirtualStock(int $virtualStock): void
    {
        $this->virtualStock = $virtualStock;
    }

    public function getVirtualStock(): ?int
    {
        return $this->virtualStock;
    }

    public function setVirtualAvailableStock(int $virtualAvailableStock): void
    {
        $this->virtualAvailableStock = $virtualAvailableStock;
    }

    public function getVirtualAvailableStock(): ?int
    {
        return $this->virtualAvailableStock;
    }

    public function isBackordersDisabled(): bool
    {
        return $this->backordersDisabled;
    }

    public function setBackordersDisabled(bool $disabled): void
    {
        $this->backordersDisabled = $disabled;
    }

    public function setDeliveryTime(?DeliveryTimeEntity $deliveryTime): void
    {
        $this->deliveryTime = $deliveryTime;
    }

    public function getDeliveryTime(): ?DeliveryTimeEntity
    {
        return $this->deliveryTime;
    }

    public function setMaxPurchase(?int $maxPurchase): void
    {
        $this->maxPurchase = $maxPurchase;
    }

    public function getMaxPurchase(): ?int
    {
        return $this->maxPurchase;
    }

    public function setWeight(float $weight): void
    {
        $this->weight = $weight;
    }

    public function getWeight(): float
    {
        return $this->weight;
    }

    public function isAvailable(): bool
    {
        return $this->available;
    }

    public function setAvailable(bool $available): void
    {
        $this->available = $available;
    }

    public function isConfigurable(): bool
    {
        return $this->configurable;
    }

    public function setConfigurable(bool $configurable): void
    {
        $this->configurable = $configurable;
    }

    public function setAdvancedPrices(?ProductPriceCollection $advancedPrices): void
    {
        $this->advancedPrices = $advancedPrices;
    }

    public function getAdvancedPrices(): ?ProductPriceCollection
    {
        return $this->advancedPrices;
    }

    public function addAdvancedPrice(ProductPriceEntity $advancedPrice): void
    {
        if ($this->advancedPrices === null) {
            $this->advancedPrices = new ProductPriceCollection();
        }

        $this->advancedPrices->add($advancedPrice);
    }

    public function addProductToBundle(ProductEntity $product): void
    {
        /** @var BundleConnectionData|null $bundleConnectionData */
        $bundleConnectionData = $product->getExtension(BundleConnectionData::EXTENSION_NAME);

        if (!$bundleConnectionData instanceof BundleConnectionData) {
            return;
        }

        $this->products[$bundleConnectionData->getId()] = $product;
        $this->salesChannelProducts[$bundleConnectionData->getId()] = SalesChannelProductEntity::createFrom($product);
    }
}
