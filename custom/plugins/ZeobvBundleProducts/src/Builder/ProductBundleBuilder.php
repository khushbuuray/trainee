<?php

declare(strict_types=1);

namespace Zeobv\BundleProducts\Builder;

use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\System\DeliveryTime\DeliveryTimeEntity;
use Shopware\Core\Content\Product\Aggregate\ProductPrice\ProductPriceCollection;
use Shopware\Core\Content\Product\Aggregate\ProductPrice\ProductPriceEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Pricing\Price;
use Shopware\Core\Framework\DataAbstractionLayer\Pricing\PriceCollection;
use Zeobv\BundleProducts\Struct\BundleProduct\BundleConnectionData;
use Zeobv\BundleProducts\Struct\BundleProduct\BundlePrice;
use Zeobv\BundleProducts\Struct\BundleProduct\ProductBundle;
use Zeobv\BundleProducts\Struct\BundleProduct\ProductBundleConfig;

class ProductBundleBuilder
{
    private array $advancedPricesMap = [];
    private ProductBundle $productBundle;

    public function __construct(
        private readonly string $bundleProductId,
        private readonly ProductBundleConfig $config
    ) {
        $this->reset();
    }

    public function reset(): void
    {
        $this->productBundle = new ProductBundle($this->bundleProductId);

        $this->productBundle->setConfig($this->config);
    }

    public function output(): ProductBundle
    {
        if ($this->productBundle->getProducts() === []) {
            throw new \RuntimeException('No products were added to the bundle');
        }

        /*
         * It might be the case during the calculation of advanced prices that
         * certain products don't have advanced prices while others do. It can
         * also happen that certain advanced prices don't exactly overlap.
         * Once all the advanced prices are processed we need to apply some
         * corrections to ensure all products are included in the advanced pricing.
         */
        if ($this->config->getPriceMode() !== BundlePrice::BUNDLE_PRICE_MODE_SUM) {
            $this->finaliseAdvancedPricing();
        }

        # apply discounts and price modifiers
        $this->applyDiscounts();

        $bundle = $this->productBundle;

        $this->reset();

        return $bundle;
    }

    public function addProduct(ProductEntity $productEntity): void
    {
        /** @var BundleConnectionData */
        $bundleConnectionData = $productEntity->getExtension(BundleConnectionData::EXTENSION_NAME);

        if (
            $bundleConnectionData === null
            || key_exists($bundleConnectionData->getId(), $this->productBundle->getProducts())
        ) {
            return;
        }

        if (
            $productEntity->getChildren() !== null
            && $productEntity->getChildren()->count() > 0
        ) {
            $this->applyValuesFromDefaultVariant($productEntity);
        }

        $this->determineProductAvailability($productEntity, $bundleConnectionData);

        $considerProductInBundleValues = $this->shouldConsiderProductInBundleValues($productEntity);

        $this->determineBundlePrice($productEntity, $bundleConnectionData);
        $this->determineBundlePurchasePrice($productEntity, $bundleConnectionData);

        if ($considerProductInBundleValues) {
            # Skip advanced prices if price mode is SUM
            if ($this->config->getPriceMode() !== BundlePrice::BUNDLE_PRICE_MODE_SUM) {
                $this->determineAdvancedPricing($productEntity, $bundleConnectionData);
            } else {
                $productEntity->setPrices(new ProductPriceCollection());

                if ($productEntity->getChildren() !== null) {
                    foreach ($productEntity->getChildren() as $child) {
                        $child->setPrices(new ProductPriceCollection());
                    }
                }
            }

            $this->determineWeight($productEntity, $bundleConnectionData);
            $this->determineStock($productEntity, $bundleConnectionData);
            $this->determineMaxPurchase($productEntity, $bundleConnectionData);
            $this->determineDeliveryTime($productEntity);
            $this->determineBundleAvailability($productEntity, $bundleConnectionData);

            $this->determineIfBundleIsConfigurable($productEntity);
        }

        $this->productBundle->addProductToBundle($productEntity);
    }

    protected function determineWeight(ProductEntity $productEntity, BundleConnectionData $bundleConnectionData): void
    {
        if ($productEntity->getWeight() === null) {
            return;
        }

        if ($this->productBundle->getWeight() <= 0.0) {
            $this->productBundle->setWeight(
                floatval($productEntity->getWeight() * $bundleConnectionData->getQuantity())
            );
            return;
        }

        $this->productBundle->setWeight(
            floatval(
                $this->productBundle->getWeight() + ($productEntity->getWeight() * $bundleConnectionData->getQuantity())
            )
        );
    }

    protected function determineStock(ProductEntity $productEntity, BundleConnectionData $bundleConnectionData): void
    {
        # Determine the available stock, divide the available stock by the quantity defined in the bundle connection data for the product
        # e.g. Product A occurs 2 times in the bundle, you order it in batches of two. To make sure you can't over order, we have to divide the
        # available stock of the product by 2.
        $availableStock = intval(
            $productEntity->getAvailableStock() > 0
            ? $productEntity->getAvailableStock() / $bundleConnectionData->getQuantity()
            : $productEntity->getAvailableStock()
        );

        $stock = intval(
            $productEntity->getStock() > 0 && $this->config->isDivideStockByItemQuantity()
            ? $productEntity->getStock() / $bundleConnectionData->getQuantity()
            : $productEntity->getStock()
        );

        # The bundle product is out of stock if the stock is smaller or equal to 0 AND
        # the product is marked as closeout
        $currentProductIsOutOfStock = $availableStock <= 0 && $productEntity->getIsCloseout();

        if (!$this->productBundle->isBackordersDisabled()) {
            # Once backorders are disabled for the bundle,
            # we don't want to re-enable it since this can allow for over-ordering the
            # product which has backorders enabled.
            # We only want to disable backorders for the whole bundle
            # if the product with backorders disabled is actually out of stock
            # else the bundle would be marked as out of stock even though
            # the product is available.
            $this->productBundle->setBackordersDisabled(
                $productEntity->getIsCloseout()
                && $productEntity->getAvailableStock() <= 0
            );
        }

        # We only want to update the stock info of the bundle if no virtual stock
        # has been set or the stock is lower than any other product in the bundle
        if (
            $availableStock <= $this->productBundle->getVirtualAvailableStock()
            || $this->productBundle->getVirtualAvailableStock() === null
        ) {
            $this->productBundle->setInStock(!$currentProductIsOutOfStock);

            $this->productBundle->setVirtualStock($stock);
            $this->productBundle->setVirtualAvailableStock($availableStock);
        }
    }

    /**
     * The max purchase of the bundle is determined by the product with the lowest max purchase
     * the max purchase is influenced by the quantity of each product in the bundle
     */
    protected function determineMaxPurchase(ProductEntity $productEntity, BundleConnectionData $bundleConnectionData): void
    {
        if ($productEntity->getMaxPurchase() === null) {
            return;
        }

        if ($productEntity->getMaxPurchase() >= $bundleConnectionData->getQuantity()) {
            $maxPurchase = $productEntity->getMaxPurchase() / $bundleConnectionData->getQuantity();
        } else {
            $maxPurchase = $productEntity->getMaxPurchase();
        }

        if ($this->productBundle->getMaxPurchase() === null) {
            $this->productBundle->setMaxPurchase((int) $maxPurchase);
            return;
        }

        if ($maxPurchase < $this->productBundle->getMaxPurchase()) {
            $this->productBundle->setMaxPurchase((int) $maxPurchase);
        }
    }

    /**
     * The delivery time of the bundle is determined by the product with the longest delivery time
     */
    protected function determineDeliveryTime(ProductEntity $productEntity): void
    {
        if ($productEntity->getDeliveryTime() === null) {
            return;
        }

        if ($this->productBundle->getDeliveryTime() === null) {
            $this->productBundle->setDeliveryTime($productEntity->getDeliveryTime());
            return;
        }

        switch ($productEntity->getDeliveryTime()->getUnit()) {
            case DeliveryTimeEntity::DELIVERY_TIME_MONTH:
                if (
                    $this->productBundle->getDeliveryTime()->getUnit() === DeliveryTimeEntity::DELIVERY_TIME_HOUR
                    || $this->productBundle->getDeliveryTime()->getUnit() === DeliveryTimeEntity::DELIVERY_TIME_DAY
                    || $this->productBundle->getDeliveryTime()->getUnit() === DeliveryTimeEntity::DELIVERY_TIME_WEEK
                ) {
                    $this->productBundle->setDeliveryTime($productEntity->getDeliveryTime());
                    break;
                }

                if ($productEntity->getDeliveryTime()->getMin() < $this->productBundle->getDeliveryTime()->getMin()) {
                    break;
                }

                $this->productBundle->setDeliveryTime($productEntity->getDeliveryTime());
                break;

            case DeliveryTimeEntity::DELIVERY_TIME_WEEK:
                if (
                    $this->productBundle->getDeliveryTime()->getUnit() === DeliveryTimeEntity::DELIVERY_TIME_MONTH
                ) {
                    break;
                }

                if (
                    $this->productBundle->getDeliveryTime()->getUnit() === DeliveryTimeEntity::DELIVERY_TIME_HOUR
                    || $this->productBundle->getDeliveryTime()->getUnit() === DeliveryTimeEntity::DELIVERY_TIME_DAY
                ) {
                    $this->productBundle->setDeliveryTime($productEntity->getDeliveryTime());
                    break;
                }

                if ($productEntity->getDeliveryTime()->getMin() < $this->productBundle->getDeliveryTime()->getMin()) {
                    break;
                }

                $this->productBundle->setDeliveryTime($productEntity->getDeliveryTime());
                break;

            case DeliveryTimeEntity::DELIVERY_TIME_DAY:
                if (
                    $this->productBundle->getDeliveryTime()->getUnit() === DeliveryTimeEntity::DELIVERY_TIME_WEEK
                    || $this->productBundle->getDeliveryTime()->getUnit() === DeliveryTimeEntity::DELIVERY_TIME_MONTH
                ) {
                    break;
                }

                if ($this->productBundle->getDeliveryTime()->getUnit() === DeliveryTimeEntity::DELIVERY_TIME_HOUR) {
                    $this->productBundle->setDeliveryTime($productEntity->getDeliveryTime());
                    break;
                }

                if ($productEntity->getDeliveryTime()->getMin() < $this->productBundle->getDeliveryTime()->getMin()) {
                    break;
                }

                $this->productBundle->setDeliveryTime($productEntity->getDeliveryTime());
                break;

            case DeliveryTimeEntity::DELIVERY_TIME_HOUR:
                if (
                    $this->productBundle->getDeliveryTime()->getUnit() === DeliveryTimeEntity::DELIVERY_TIME_DAY
                    || $this->productBundle->getDeliveryTime()->getUnit() === DeliveryTimeEntity::DELIVERY_TIME_WEEK
                    || $this->productBundle->getDeliveryTime()->getUnit() === DeliveryTimeEntity::DELIVERY_TIME_MONTH
                ) {
                    break;
                }

                if ($productEntity->getDeliveryTime()->getMin() < $this->productBundle->getDeliveryTime()->getMin()) {
                    break;
                }

                $this->productBundle->setDeliveryTime($productEntity->getDeliveryTime());
                break;
        }
    }

    protected function determineProductAvailability(ProductEntity $productEntity, BundleConnectionData $bundleConnectionData): void
    {
        if ($productEntity->getAvailable() === false) {
            return;
        }

        # If qty selection is allowed we can reduce the available quantity
        # to the maxium possible quantity under which the product is still available
        $bundleItemQuantity = $bundleConnectionData->getQuantity();
        if ($this->productBundle->getConfig()->isAllowQuantitySelection()) {
            $minQuantity = $productEntity->getMinPurchase();
            $maxQuantity = max($productEntity->getMaxPurchase(), $productEntity->getAvailableStock());

            if ($bundleItemQuantity < $minQuantity) {
                $bundleItemQuantity = $minQuantity;
            } elseif ($bundleItemQuantity > $maxQuantity) {
                $bundleItemQuantity = $maxQuantity;
            }
        }

        $backordersDisabled = $productEntity->getIsCloseout();
        $productIsAvailable = $productEntity->getActive()
            && (($backordersDisabled
                    && $productEntity->getAvailableStock() >= $bundleItemQuantity)
                || $backordersDisabled === false);

        $productEntity->setAvailable($productIsAvailable);
    }

    protected function determineBundleAvailability(ProductEntity $productEntity, BundleConnectionData $bundleConnectionData): void
    {
        if (!$this->productBundle->isAvailable()) {
            return;
        }

        $this->productBundle->setAvailable($productEntity->getAvailable());
    }

    protected function determineIfBundleIsConfigurable(ProductEntity $productEntity): void
    {
        if ($this->productBundle->isConfigurable()) {
            return;
        }

        if ($this->config->isAllowQuantitySelection()) {
            $this->productBundle->setConfigurable(true);
            return;
        }

        if ($this->config->isAllowItemSelection()) {
            $this->productBundle->setConfigurable(true);
            return;
        }

        if ($this->config->isAllowVariantSelection()) {
            $this->productBundle->setConfigurable(
                $productEntity->getChildCount() > 0
            );
        }
    }

    protected function determineBundlePrice(ProductEntity $productEntity, BundleConnectionData $bundleConnectionData): void
    {
        if ($productEntity->getPrice() === null) {
            return;
        }

        /** @var PriceCollection|null $priceCollection */
        $priceCollection = $productEntity->getPrice();

        if ($priceCollection === null) {
            return;
        }

        $quantity = $bundleConnectionData->getQuantity();

        if ($this->config->isAllowQuantitySelection()) {
            $minPurchase = $productEntity->getMinPurchase() ?: 1;
            $maxPurchase = $productEntity->getMaxPurchase() ?: 100;

            if ($productEntity->getIsCloseout()) {
                $availableStock = $productEntity->getAvailableStock();
                $maxPurchase = min($maxPurchase, $availableStock);
            }

            $quantity = min($quantity, $maxPurchase);
            $quantity = max($quantity, $minPurchase);
        }

        /** @var Price $price */
        foreach ($priceCollection as $price) {
            $this->productBundle->getBundlePrice()->addPrice(
                $price,
                $quantity
            );
        }
    }

    protected function determineBundlePurchasePrice(ProductEntity $productEntity, BundleConnectionData $bundleConnectionData): void
    {
        if ($productEntity->getPurchasePrices() === null) {
            return;
        }

        /** @var Price $purchasePrice */
        foreach ($productEntity->getPurchasePrices() as $purchasePrice) {
            $this->productBundle->getBundlePurchasePrice()->addPrice(
                $purchasePrice,
                $bundleConnectionData->getQuantity()
            );
        }
    }

    protected function determineAdvancedPricing(ProductEntity $productEntity, BundleConnectionData $bundleConnectionData): void
    {
        $productPrices = $productEntity->getPrices();

        if ($productPrices === null || $productPrices->count() === 0) {
            return;
        }

        // Break all references to the original advanced prices
        $advancedPrices = new ProductPriceCollection(
            $productPrices->map(
                static function (ProductPriceEntity $productPrice) {
                    $newProductPrice = ProductPriceEntity::createFrom($productPrice);
                    $newProductPrice->setPrice(
                        new PriceCollection(
                            $productPrice->getPrice()->map(
                                static function (Price $price) {
                                    if ($price->getListPrice() !== null) {
                                        $price->setListPrice(
                                            Price::createFrom($price->getListPrice())
                                        );
                                    }

                                    if ($price->getRegulationPrice() !== null) {
                                        $price->setRegulationPrice(Price::createFrom($price->getRegulationPrice()));
                                    }

                                    return Price::createFrom($price);
                                }
                            )
                        )
                    );

                    return $newProductPrice;
                }
            )
        );

        if ($this->productBundle->getAdvancedPrices() === null) {
            $this->productBundle->setAdvancedPrices($advancedPrices);

            foreach ($advancedPrices as $advancedPrice) {

                foreach ($advancedPrice->getPrice() as $currencyId => $price) {
                    # Correct advance price for the quantity of the product in the bundle
                    $price->setGross($price->getGross() * $bundleConnectionData->getQuantity());
                    $price->setNet($price->getNet() * $bundleConnectionData->getQuantity());

                    $this->mapAdvancedPriceProduct(
                        $advancedPrice->getRuleId(),
                        $advancedPrice->getQuantityStart(),
                        $currencyId,
                        $productEntity
                    );
                }
            }

            return;
        }

        foreach ($advancedPrices as $advancedProductPrice) {
            $advancedPricesForRule = $this->productBundle->getAdvancedPrices()->filterByRuleId(
                $advancedProductPrice->getRuleId()
            );

            if ($advancedPricesForRule->count() < 1) {
                $this->productBundle->addAdvancedPrice($advancedProductPrice);
                continue;
            }

            foreach ($advancedPricesForRule as $advancedPriceForRule) {
                if ($advancedPriceForRule->getQuantityStart() === $advancedProductPrice->getQuantityStart()) {
                    $advancedPriceForRuleAndQuantity = $advancedPriceForRule;
                }
            }

            if (!isset($advancedPriceForRuleAndQuantity)) {
                $this->productBundle->getAdvancedPrices()->last()->setQuantityEnd(
                    $advancedProductPrice->getQuantityStart() - 1
                );

                foreach ($advancedProductPrice->getPrice() as $productPriceCurrencyId => $productPrice) {
                    # Correct advance price for the quantity of the product in the bundle
                    $productPrice->setGross($productPrice->getGross() * $bundleConnectionData->getQuantity());
                    $productPrice->setNet($productPrice->getNet() * $bundleConnectionData->getQuantity());

                    $this->mapAdvancedPriceProduct(
                        $advancedProductPrice->getRuleId(),
                        $advancedProductPrice->getQuantityStart(),
                        $productPriceCurrencyId,
                        $productEntity
                    );

                    $this->productBundle->addAdvancedPrice($advancedProductPrice);
                }
                continue;
            }

            /** @var \Shopware\Core\Framework\DataAbstractionLayer\Pricing\PriceCollection|null $prices */
            $prices = $advancedPriceForRuleAndQuantity->getPrice();

            if ($prices === null) {
                unset($advancedPriceForRuleAndQuantity);
                continue;
            }

            foreach ($advancedProductPrice->getPrice() as $productPriceCurrencyId => $productPrice) {
                # Correct advance price for the quantity of the product in the bundle
                $productPrice->setGross($productPrice->getGross() * $bundleConnectionData->getQuantity());
                $productPrice->setNet($productPrice->getNet() * $bundleConnectionData->getQuantity());

                $price = $prices->getCurrencyPrice($productPriceCurrencyId, false);

                $this->mapAdvancedPriceProduct(
                    $advancedProductPrice->getRuleId(),
                    $advancedPriceForRuleAndQuantity->getQuantityStart(),
                    $productPriceCurrencyId,
                    $productEntity
                );

                if ($price === null) {
                    $prices->add($productPrice);
                    continue;
                }

                $this->sumTwoPrices($price, $productPrice);
            }

            unset($advancedPriceForRuleAndQuantity);
        }
    }

    protected function applyDiscounts(): void
    {
        $bundlePrice = $this->productBundle->getBundlePrice();

        if ($bundlePrice->getMode() !== BundlePrice::BUNDLE_PRICE_MODE_SUM) {
            return;
        }

        $discount = $this->config->getProductBundleDiscountPercentage();

        if ($discount <= 0) {
            return;
        }

        # Apply the discount to the default price
        $priceCollection = $bundlePrice->getPrice();
        /** @var Price $price */
        foreach ($priceCollection as $price) {
            $this->applyDiscountToPrice($price, $discount);
        }

        # Apply the discount to the advanced prices
        $advancedPrices = $this->productBundle->getAdvancedPrices();

        if ($advancedPrices === null) {
            return;
        }

        /** @var ProductPriceEntity $advancedPrice */
        foreach ($advancedPrices as $advancedPrice) {
            $priceCollection = $advancedPrice->getPrice();

            /** @var Price $price */
            foreach ($priceCollection as $price) {
                $this->applyDiscountToPrice($price, $discount);
            }
        }
    }

    protected function filterAdvancedPrices(): void
    {
        $advancedPrices = $this->productBundle->getAdvancedPrices();

        /** @var ProductPriceEntity $advancedPrice */
        foreach ($advancedPrices as $id => $advancedPrice) {
            if ($advancedPrice->getQuantityStart() === 1) {
                $advancedPrice->setQuantityEnd(null);
                continue;
            }

            $advancedPrices->remove($id);
        }
    }

    protected function finaliseAdvancedPricing(): void
    {
        foreach ($this->advancedPricesMap as $key => $products) {
            $productIds = array_keys($products);

            // Skip if all products are included in the calculation of the current advanced price
            if (count($productIds) >= count($this->productBundle->getProducts())) {
                continue;
            }
            $productsNotIncludedInAdvancedPricing = array_filter(
                $this->productBundle->getProducts(),
                static function (ProductEntity $productEntity) use ($productIds) {
                    return !in_array($productEntity->getId(), $productIds);
                }
            );

            $decontructedKey = $this->deconstructAdvancedPricesMapKey($key);

            $currentAdvancedPrice = current(
                array_filter(
                    $this->productBundle->getAdvancedPrices()->getElements(),
                    static function ($advancedPrice) use ($decontructedKey) {
                        return $advancedPrice->getQuantityStart() === $decontructedKey['quantityStart'] &&
                            $advancedPrice->getRuleId() === $decontructedKey['ruleId'];
                    }
                )
            );

            if ($currentAdvancedPrice === false) {
                continue;
            }

            $price = $currentAdvancedPrice->getPrice()->getCurrencyPrice($decontructedKey['currencyId']);

            /** @var ProductEntity $product */
            foreach ($productsNotIncludedInAdvancedPricing as $key => $product) {
                if ($product->getPrices()->count() > 0) {
                    $advancedPriceForProduct = $product->getPrices()->last();
                    $incrementPrice = clone $advancedPriceForProduct
                        ->getPrice()
                        ->getCurrencyPrice(
                            $decontructedKey['currencyId']
                        );
                } else {
                    $incrementPrice = clone $product->getPrice()->getCurrencyPrice($decontructedKey['currencyId']);
                }

                # Correct advance price for the quantity of the product in the bundle
                /** @var BundleConnectionData $bundleConnectionData */
                $bundleConnectionData = $product->getExtension(BundleConnectionData::EXTENSION_NAME);
                $incrementPrice->setGross($incrementPrice->getGross() * $bundleConnectionData->getQuantity());
                $incrementPrice->setNet($incrementPrice->getNet() * $bundleConnectionData->getQuantity());

                $this->sumTwoPrices($price, $incrementPrice);
            }
        }
    }

    private function applyDiscountToPrice(Price $price, float $discount): void
    {
        $gross = $price->getGross();
        $net = $price->getNet();

        $price->setGross(
            $price->getGross() - ($price->getGross() * $discount / 100)
        );

        $price->setNet(
            $price->getNet() - ($price->getNet() * $discount / 100)
        );

        # Set the original price as list price
        $price->setListPrice(
            new Price(
                $price->getCurrencyId(),
                $net,
                $gross,
                false,
                null
            )
        );
    }

    private function mapAdvancedPriceProduct(string $ruleId, int $quantity, string $currencyId, ProductEntity $productEntity): void
    {
        $key = $this->constructAdvancedPricesMapKey($ruleId, $quantity, $currencyId);
        $this->advancedPricesMap[$key][$productEntity->getId()] = $productEntity;
    }

    private function sumTwoPrices(Price $price, Price $incrementPrice): void
    {
        $price->setGross($incrementPrice->getGross() + $price->getGross());
        $price->setNet($incrementPrice->getNet() + $price->getNet());

        $listPrice = $price->getListPrice();
        if ($listPrice !== null) {
            $listPrice->setGross(
                ($incrementPrice->getListPrice()
                    ? $incrementPrice->getListPrice()->getGross()
                    : $incrementPrice->getGross()) + $listPrice->getGross()
            );
            $listPrice->setNet(
                ($incrementPrice->getListPrice()
                    ? $incrementPrice->getListPrice()->getNet()
                    : $incrementPrice->getNet()) + $listPrice->getNet()
            );
        }

        $cheapestPrice = $price->getRegulationPrice();
        if ($cheapestPrice !== null) {
            $cheapestPrice->setGross(
                ($incrementPrice->getRegulationPrice()
                    ? $incrementPrice->getRegulationPrice()->getGross()
                    : $incrementPrice->getGross()) + $cheapestPrice->getGross()
            );
            $cheapestPrice->setNet(
                ($incrementPrice->getRegulationPrice()
                    ? $incrementPrice->getRegulationPrice()->getNet()
                    : $incrementPrice->getNet()) + $cheapestPrice->getNet()
            );
        }
    }

    private function shouldConsiderProductInBundleValues(ProductEntity $productEntity): bool
    {
        if ($this->config->isAllowItemSelection() === false) {
            return true;
        }

        /** @var BundleConnectionData */
        $bundleConnectionData = $productEntity->getExtension(BundleConnectionData::EXTENSION_NAME);

        if (
            $productEntity->getChildren() !== null
            && $productEntity->getChildren()->count() > 0
        ) {
            $productEntity = $productEntity->getChildren()->first();
        }

        # If the product is unavailable we will simply exclude it from the bundle
        return $productEntity->getActive()
            && $productEntity->getAvailable()
            && ($productEntity->getAvailableStock() > $bundleConnectionData->getQuantity()
                || $productEntity->getIsCloseout() === false);
    }

    private function applyValuesFromDefaultVariant(ProductEntity $productEntity): void
    {
        $defaultVariant = $productEntity->getChildren()->first();

        $productEntity->setPrice($defaultVariant->getPrice());
        $productEntity->setPurchasePrices($defaultVariant->getPurchasePrices());
        $productEntity->setPrices($defaultVariant->getPrices());
        $productEntity->setMaxPurchase($defaultVariant->getMaxPurchase());
        $productEntity->setDeliveryTime($defaultVariant->getDeliveryTime());
        $productEntity->setActive($defaultVariant->getActive());
        $productEntity->setIsCloseout($defaultVariant->getIsCloseout());
        $productEntity->setStock($defaultVariant->getStock());
        $productEntity->setAvailableStock($defaultVariant->getAvailableStock());
        $productEntity->setAvailable($defaultVariant->getAvailable());
    }

    private function constructAdvancedPricesMapKey(string $ruleId, int $quantity, string $currencyId): string
    {
        return implode(',', [$ruleId, $quantity, $currencyId]);
    }

    private function deconstructAdvancedPricesMapKey(string $key): array
    {
        [$ruleId, $quantity, $currencyId] = explode(',', $key);

        return ['ruleId' => $ruleId, 'quantityStart' => (int) $quantity, 'currencyId' => $currencyId];
    }
}
