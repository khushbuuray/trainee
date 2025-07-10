<?php

declare(strict_types=1);

namespace Zeobv\BundleProducts\Service;

use Shopware\Core\Checkout\Cart\Price\Struct\ListPrice;
use Shopware\Core\Checkout\Cart\Tax\AbstractTaxDetector;
use Throwable;
use Shopware\Core\Checkout\Cart\Price\QuantityPriceCalculator;
use Shopware\Core\Checkout\Cart\Price\Struct\CalculatedPrice;
use Shopware\Core\Checkout\Cart\Price\Struct\QuantityPriceDefinition;
use Shopware\Core\Checkout\Cart\Tax\TaxDetector;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Content\Product\SalesChannel\Price\AbstractProductPriceCalculator;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Pricing\CashRoundingConfig;
use Shopware\Core\Framework\DataAbstractionLayer\Pricing\Price;
use Shopware\Core\Framework\Struct\Struct;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Zeobv\BundleProducts\Exception\ProductNotInBundleException;
use Zeobv\BundleProducts\Struct\BundleProduct\BundleConnectionData;
use Zeobv\BundleProducts\Struct\BundleProduct\BundlePrice;
use Zeobv\BundleProducts\Struct\BundleProduct\CalculatedBundlePrice;
use Zeobv\BundleProducts\Struct\BundleProduct\ProductBundle;

class BundlePriceCalculator extends Struct
{
    public const NUM_OF_DECIMALS_FOR_ACCURATE_PRECISION = 13;

    private ?CashRoundingConfig $cachedRoundingConfig;

    public function __construct(
        private AbstractTaxDetector|TaxDetector $taxDetector,
        private QuantityPriceCalculator $quantityPriceCalculator,
        private AbstractProductPriceCalculator $calculator,
        private ConfigService $configService
    ) {
    }

    public function createForBundleProduct(SalesChannelProductEntity $bundleProduct, SalesChannelContext $context, int $quantity = 1): CalculatedBundlePrice
    {
        foreach ($bundleProduct->getCalculatedPrices() as $calculatedPrice) {
            if (
                !isset($advancedCalculatedPrice)
                && $calculatedPrice->getQuantity() <= $quantity
            ) {
                $advancedCalculatedPrice = $calculatedPrice;
            }

            if (
                $calculatedPrice->getQuantity() >= $quantity
            ) {
                $advancedCalculatedPrice = $calculatedPrice;
                break;
            }

            if (
                isset($advancedCalculatedPrice) &&
                $advancedCalculatedPrice->getQuantity() < $calculatedPrice->getQuantity()
            ) {
                $advancedCalculatedPrice = $calculatedPrice;
            }
        }

        $price = $bundleProduct->getPrice()->first();
        if (isset($advancedCalculatedPrice)) {
            foreach ($bundleProduct->getPrices() as $advancedPrice) {
                foreach ($advancedPrice->getPrice() as $newPrice) {
                    if (
                        round($newPrice->getGross(), 2) === round($advancedCalculatedPrice->getUnitPrice(), 2)
                        || round($newPrice->getNet(), 2) === round($advancedCalculatedPrice->getUnitPrice(), 2)
                    ) {
                        $price = $newPrice;
                        break 2;
                    }
                }
            }
        }

        $calculatedBundlePrice = $this->calculateBundlePrice(
            $price,
            $bundleProduct->getTaxId(),
            $quantity,
            $context
        );

        $bundlePrice = new CalculatedBundlePrice($price, $calculatedBundlePrice);

        /** @var ProductBundle|null $productBundle */
        $productBundle = $bundleProduct->getExtension(ProductBundle::EXTENSION_NAME);

        if ($productBundle === null) {
            return $bundlePrice;
        }

        # (Re)calculate the prices for each product inside the bundle
        $this->calculator->calculate(
            $productBundle->getSalesChannelProducts(),
            $context
        );

        foreach ($productBundle->getSalesChannelProducts() as $product) {
            $bundlePrice->addProduct($product);
        }

        $bundlePrice->recalculate();

        $discount = floatval($bundleProduct->getCustomFields()['zeobvBundleProductsDiscountPercentage'] ?? 0);
        $this->calculateNewProductPrices($bundlePrice, $productBundle, $context, $quantity, $discount);

        return $bundlePrice;
    }

    /**
     * @param CalculatedBundlePrice $bundlePrice
     * @param ProductBundle $productBundle
     * @param SalesChannelContext $context
     *
     * @throws ProductNotInBundleException
     * @throws Throwable
     */
    protected function calculateNewProductPrices(
        CalculatedBundlePrice $bundlePrice,
        ProductBundle $productBundle,
        SalesChannelContext $context,
        int $quantity = 1,
        float $discount = 0.0
    ): void {
        if ($productBundle->getConfig()->isDisableBundleProductItemPrices()) {
            foreach ($productBundle->getProducts() as $product) {
                $defaultPrice = $product->getPrice()->first();
                $price = new Price($defaultPrice->getCurrencyId(), 0.00, 0.00, false);
                $bundleConnectionData = $product->getExtension(BundleConnectionData::EXTENSION_NAME);

                $bundlePrice->setDiscountedProductPrice($bundleConnectionData->getId(), $price);
                $bundlePrice->setCalculatedProductPrice(
                    $bundleConnectionData->getId(),
                    $this->calculateProductPrice(
                        $price,
                        $product->getTaxId(),
                        $bundleConnectionData->getQuantity(),
                        $context
                    )
                );
            }

            return;
        }

        switch ($productBundle->getConfig()->getPriceMode()) {
            case BundlePrice::BUNDLE_PRICE_MODE_FIT:
                $this->fitProductPriceInBundle($bundlePrice, $context);
                break;
            case BundlePrice::BUNDLE_PRICE_MODE_SUM:
                $this->adjustProductPricesForSumPriceMode(
                    $bundlePrice,
                    $productBundle,
                    $context,
                    $quantity,
                    $discount
                );
                break;
            default:
                $this->applyRelevantProductPricesToBundlePrice(
                    $bundlePrice,
                    $productBundle,
                    $context,
                    $quantity
                );
                break;
        }
    }

    private function applyRelevantProductPricesToBundlePrice(CalculatedBundlePrice $bundlePrice, ProductBundle $productBundle, SalesChannelContext $context, int $quantity): void
    {
        /** @var SalesChannelProductEntity $product */
        foreach ($productBundle->getSalesChannelProducts() as $product) {
            /** @var BundleConnectionData $bundleConnectionData */
            $bundleConnectionData = $product->getExtension(BundleConnectionData::EXTENSION_NAME);

            $prices = $this->getRelevantProductPrices($product, $quantity, $context);

            $price = $prices['price'];
            $calculatedPrice = $prices['calculatedPrice'];

            $bundlePrice->setDiscountedProductPrice(
                $bundleConnectionData->getId(),
                $price
            );

            $bundlePrice->setCalculatedProductPrice(
                $bundleConnectionData->getId(),
                $calculatedPrice
            );
        }
    }

    private function adjustProductPricesForSumPriceMode(CalculatedBundlePrice $bundlePrice, ProductBundle $productBundle, SalesChannelContext $context, int $quantity, float $discount): void
    {
        /** @var SalesChannelProductEntity $product */
        foreach ($productBundle->getSalesChannelProducts() as $product) {
            /** @var BundleConnectionData $bundleConnectionData */
            $bundleConnectionData = $product->getExtension(BundleConnectionData::EXTENSION_NAME);

            $prices = $this->getRelevantProductPrices(
                $product,
                $quantity * $bundleConnectionData->getQuantity(),
                $context
            );

            $price = $prices['price'];
            $calculatedPrice = $prices['calculatedPrice'];

            if ($discount <= 0) {
                $bundlePrice->setDiscountedProductPrice(
                    $bundleConnectionData->getId(),
                    $price
                );

                $bundlePrice->setCalculatedProductPrice(
                    $bundleConnectionData->getId(),
                    $calculatedPrice
                );
                continue;
            }

            $price->setGross($price->getGross() * (1 - $discount / 100));
            $price->setNet($price->getNet() * (1 - $discount / 100));

            $discountedCalculatedPrice = $this->quantityPriceCalculator->calculate(
                new QuantityPriceDefinition(
                    $this->taxDetector->useGross($context)
                    ? $price->getGross()
                    : $price->getNet(),
                    $context->buildTaxRules($product->getTaxId()),
                    $quantity
                ),
                $context
            );

            $bundlePrice->setDiscountedProductPrice(
                $bundleConnectionData->getId(),
                $price
            );

            $listPrice = null;
            if (
                $discountedCalculatedPrice->getUnitPrice() > 0
                && $calculatedPrice->getUnitPrice() > 0
            ) {
                $listPrice = ListPrice::createFromUnitPrice(
                    $discountedCalculatedPrice->getUnitPrice(),
                    $calculatedPrice->getUnitPrice()
                );
            }

            $bundlePrice->setCalculatedProductPrice(
                $bundleConnectionData->getId(),
                new CalculatedPrice(
                    $discountedCalculatedPrice->getUnitPrice(),
                    $discountedCalculatedPrice->getTotalPrice(),
                    $discountedCalculatedPrice->getCalculatedTaxes(),
                    $discountedCalculatedPrice->getTaxRules(),
                    $discountedCalculatedPrice->getQuantity(),
                    $discountedCalculatedPrice->getReferencePrice(),
                    $listPrice,
                    $discountedCalculatedPrice->getRegulationPrice()
                )
            );
        }
    }

    private function fitProductPriceInBundle(CalculatedBundlePrice $bundlePrice, SalesChannelContext $context): void
    {
        $salesChannelId = $context->getSalesChannel()->getId();
        if ($this->configService->useLegacyBundleFitPriceCalculation($salesChannelId)) {
            $this->fitProductPriceInBundleLegacy($bundlePrice, $context);
            return;
        }

        $bundleSize = $bundlePrice->getBundleSize();
        $index = 1;
        $totalGross = 0;
        $totalNet = 0;

        $discountedBundlePrice = $bundlePrice->getDiscountedBundlePrice();
        $realBundlePrice = $bundlePrice->getRealBundlePrice();

        $this->setDecimalPrecisionToHighAccuracy($context);

        try {
            /** @var SalesChannelProductEntity $product */
            foreach ($bundlePrice->getProducts() as $relId => $product) {
                $bundleConnectionData = $product->getExtension(BundleConnectionData::EXTENSION_NAME);
                $itemQty = $bundleConnectionData instanceof BundleConnectionData ? $bundleConnectionData->getQuantity() : 1;

                $prices = $this->getRelevantProductPrices($product, $itemQty, $context);
                $price = $prices['price'];

                if ($index < $bundleSize) {
                    // Calculate the proportional factor based on initial values
                    $grossFactor = $discountedBundlePrice->getGross() / (isset($realBundlePrice) ? $realBundlePrice->getGross() : 1);
                    $netFactor = $discountedBundlePrice->getNet() / (isset($realBundlePrice) ? $realBundlePrice->getNet() : 1);

                    // Calculate new prices proportionately
                    $newGrossPrice = $price->getGross() * $grossFactor * $itemQty;
                    $newNetPrice = $price->getNet() * $netFactor * $itemQty;
                } else {
                    $newGrossPrice = $discountedBundlePrice->getGross() - $totalGross;
                    $newNetPrice = $discountedBundlePrice->getNet() - $totalNet;
                }

                // Update the total gross and net prices
                $totalGross += $newGrossPrice;
                $totalNet += $newNetPrice;

                // Set the new prices for the product
                $price->setGross($newGrossPrice / $itemQty);
                $price->setNet($newNetPrice / $itemQty);

                // Update the bundle and calculated product prices
                $bundlePrice->setDiscountedProductPrice($relId, $price);
                $bundlePrice->setCalculatedProductPrice(
                    $relId,
                    $this->calculateProductPrice(
                        $price,
                        $product->getTaxId(),
                        $itemQty,
                        $context
                    )
                );

                $index++;
            }
        } catch (Throwable $e) {
            $this->resetDecimalPrecision($context);
            throw $e;
        }

        $this->resetDecimalPrecision($context);
    }

    private function fitProductPriceInBundleLegacy(CalculatedBundlePrice $bundlePrice, SalesChannelContext $context): void
    {
        $bundleSize = $bundlePrice->getBundleSize();
        $index = 1;
        $totalGross = 0;
        $totalNet = 0;

        $discountedBundlePrice = $bundlePrice->getDiscountedBundlePrice();
        $realBundlePrice = $bundlePrice->getRealBundlePrice();

        $this->setDecimalPrecisionToHighAccuracy($context);

        try {
            /** @var SalesChannelProductEntity $product */
            foreach ($bundlePrice->getProducts() as $relId => $product) {
                $bundleConnectionData = $product->getExtension(BundleConnectionData::EXTENSION_NAME);
                $itemQty = $bundleConnectionData instanceof BundleConnectionData ? $bundleConnectionData->getQuantity() : 1;

                $prices = $this->getRelevantProductPrices($product, $itemQty, $context);

                $price = $prices['price'];

                if ($index < $bundleSize) {
                    $newGrossPrice = $discountedBundlePrice->getGross() * $price->getGross() * $itemQty / (isset($realBundlePrice) ? $realBundlePrice->getGross() : 1);
                    $newNetPrice = $discountedBundlePrice->getNet() * $price->getNet() * $itemQty / (isset($realBundlePrice) ? $realBundlePrice->getNet() : 1);
                } else {
                    $newGrossPrice = $discountedBundlePrice->getGross() - $totalGross;
                    $newNetPrice = $discountedBundlePrice->getNet() - $totalNet;
                }

                # Round to two decimals to avoid rounding issues
                $newGrossPrice = floatval(number_format($newGrossPrice, 2));
                $newNetPrice = floatval(number_format($newNetPrice, 2));

                $totalGross += $newGrossPrice;
                $totalNet += $newNetPrice;

                if ($index === $bundleSize) {
                    # Fix rounding issues
                    $newGrossPrice += $totalGross < $discountedBundlePrice->getGross()
                        ? $discountedBundlePrice->getGross() - $totalGross
                        : 0;

                    $newNetPrice += $totalNet < $discountedBundlePrice->getNet()
                        ? $discountedBundlePrice->getNet() - $totalNet
                        : 0;
                }

                $price->setGross($newGrossPrice / $itemQty);
                $price->setNet($newNetPrice / $itemQty);

                $bundlePrice->setDiscountedProductPrice($relId, $price);
                $bundlePrice->setCalculatedProductPrice(
                    $relId,
                    $this->calculateProductPrice(
                        $price,
                        $product->getTaxId(),
                        $itemQty,
                        $context
                    )
                );

                $index++;
            }
        } catch (Throwable $e) {
            $this->resetDecimalPrecision($context);
            throw $e;
        }

        $this->resetDecimalPrecision($context);
    }

    private function calculateBundlePrice(Price $price, string $taxId, int $quantity, SalesChannelContext $context): CalculatedPrice
    {
        /** @var CalculatedPrice $calculatedPrice */
        $calculatedPrice = $this->quantityPriceCalculator->calculate(
            new QuantityPriceDefinition(
                $this->taxDetector->useGross($context) ? $price->getGross() : $price->getNet(),
                $context->buildTaxRules($taxId),
                $quantity
            ),
            $context
        );

        if ($price->getListPrice() !== null) {
            $calculatedListPrice = $this->quantityPriceCalculator->calculate(
                new QuantityPriceDefinition(
                    $this->taxDetector->useGross($context)
                    ? $price->getListPrice()->getGross()
                    : $price->getListPrice()->getNet(),
                    $context->buildTaxRules($taxId),
                    $quantity
                ),
                $context
            );

            $calculatedPrice->assign([
                'listPrice' => ListPrice::createFromUnitPrice(
                    $calculatedPrice->getUnitPrice(),
                    $calculatedListPrice->getTotalPrice(),
                ),
            ]);
        }

        return $calculatedPrice;
    }

    private function calculateProductPrice(Price $price, string $taxId, int $itemQty, SalesChannelContext $context): CalculatedPrice
    {
        $qtyPriceDefinition = new QuantityPriceDefinition(
            $this->taxDetector->useGross($context) ? $price->getGross() : $price->getNet(),
            $context->buildTaxRules($taxId),
            $itemQty
        );

        $qtyPriceDefinition->setIsCalculated(true);

        $currentRoundingConfig = $context->getItemRounding();
        $accurateRoundingConfig = clone $currentRoundingConfig;
        $accurateRoundingConfig->setDecimals(13);
        $context->setItemRounding($accurateRoundingConfig);

        $calculatedPrice = $this->quantityPriceCalculator->calculate(
            $qtyPriceDefinition,
            $context
        );

        $context->setItemRounding($currentRoundingConfig);

        return $calculatedPrice;
    }

    private function setDecimalPrecisionToHighAccuracy(SalesChannelContext $context): void
    {
        $this->cachedRoundingConfig = $context->getItemRounding();
        $accurateRoundingConfig = clone $this->cachedRoundingConfig;
        $accurateRoundingConfig->setDecimals(BundlePriceCalculator::NUM_OF_DECIMALS_FOR_ACCURATE_PRECISION);
        $context->setItemRounding($accurateRoundingConfig);
    }

    private function resetDecimalPrecision(SalesChannelContext $context): void
    {
        if ($this->cachedRoundingConfig instanceof CashRoundingConfig) {
            $context->setItemRounding($this->cachedRoundingConfig);
        }
    }

    private function getRelevantProductPrices(SalesChannelProductEntity $product, int $quantity, SalesChannelContext $context): array
    {
        // We use the calculated price by default
        $calculatedPrice = $product->getCalculatedPrice();

        $previousCalculatedPrice = null;
        foreach ($product->getCalculatedPrices() as $calculatedPrice) {
            if ($calculatedPrice->getQuantity() > $quantity) {
                # If the quantity is higher than the current calculated price quantity
                # we want to use the previous calculated price if available
                # otherwise we use the current calculated price
                if ($previousCalculatedPrice !== null) {
                    $advancedPrice = $previousCalculatedPrice;
                } else {
                    $advancedPrice = $calculatedPrice;
                }
                break;
            }

            $previousCalculatedPrice = $calculatedPrice;
        }

        # If no advanced price was found, for the given quantity,
        # but there are advanced prices available it means that
        # the quantity is higher than the highest advanced price quantity
        # this means we should use the last advanced price since our
        # quantity qualifies for it
        if ($product->getCalculatedPrices()->count() > 0) {
            $calculatedPrice = $advancedPrice ?? $product->getCalculatedPrices()->last();

            # Advanced calculated prices are always calculated for
            # their specified quantity, so we need to calculate the
            # we want a calculated price for the actual quantity
            # we received.
            if ($calculatedPrice->getQuantity() > 1) {
                $calculatedPrice = $this->quantityPriceCalculator->calculate(
                    new QuantityPriceDefinition(
                        $calculatedPrice->getUnitPrice(),
                        $calculatedPrice->getTaxRules(),
                        $quantity
                    ),
                    $context
                );
            }
        }

        return [
            'price' => $this->getPriceFromCalculatedPrice($calculatedPrice, $product, $context),
            'calculatedPrice' => $calculatedPrice,
        ];
    }

    private function getPriceFromCalculatedPrice(CalculatedPrice $calculatedPrice, ProductEntity $product, SalesChannelContext $context): Price
    {
        $currencyId = $context->getCurrencyId();
        $priceIsGross = $context->getCurrentCustomerGroup()->getDisplayGross();

        $prices = [$product->getPrice()->getCurrencyPrice($currencyId)];

        foreach ($product->getPrices() as $advancedPrice) {
            $prices[] = $advancedPrice->getPrice()->getCurrencyPrice($currencyId);
        }

        foreach ($prices as $price) {
            $valueToCheck = $priceIsGross ? $price->getGross() : $price->getNet();

            if (round($valueToCheck, 2) === round($calculatedPrice->getUnitPrice(), 2)) {
                return $price;
            }
        }

        $taxRule = $calculatedPrice->getTaxRules()->first();
        $taxRate = $taxRule !== null ? $taxRule->getTaxRate() : 0;

        // Fallback if no price was matched, this might lead to rounding errors
        if ($priceIsGross) {
            $unitPriceGross = $calculatedPrice->getUnitPrice();
            $unitPriceNet = $calculatedPrice->getUnitPrice() / (1 + $taxRate);
        } else {
            $unitPriceGross = $calculatedPrice->getUnitPrice() * (1 + $taxRate);
            $unitPriceNet = $calculatedPrice->getUnitPrice();
        }

        return new Price(
            $context->getCurrencyId(),
            $unitPriceNet,
            $unitPriceGross,
            true
        );
    }
}
