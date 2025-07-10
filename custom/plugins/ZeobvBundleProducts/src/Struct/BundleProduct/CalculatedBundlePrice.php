<?php

declare(strict_types=1);

namespace Zeobv\BundleProducts\Struct\BundleProduct;

use Shopware\Core\Checkout\Cart\Price\Struct\CalculatedPrice;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Pricing\Price;
use Shopware\Core\Framework\Struct\Struct;
use Zeobv\BundleProducts\Exception\ProductNotInBundleException;

/**
 * Represents the calculated price structure for a bundle product.
 * This class manages various price aspects of a bundle including:
 * - Original and discounted prices for the entire bundle
 * - Individual product prices within the bundle
 * - Price calculations with quantity considerations
 * - Discount tracking for both gross and net amounts
 */
class CalculatedBundlePrice extends Struct
{
    /**
     * Total discount amount applied to the gross price of the bundle
     * Calculated as: realBundlePrice->getGross() - discountedBundlePrice->getGross()
     */
    protected ?float $totalGrossDiscount = null;

    /**
     * Total discount amount applied to the net price of the bundle
     * Calculated as: realBundlePrice->getNet() - discountedBundlePrice->getNet()
     */
    protected ?float $totalNetDiscount = null;

    /**
     * The final price of the bundle after all discounts have been applied
     * This price is used for cart calculations and checkout
     */
    protected ?Price $discountedBundlePrice = null;

    /**
     * The original price of the bundle before any discounts
     * Used as a reference for discount calculations and price comparisons
     */
    protected ?Price $realBundlePrice = null;

    /**
     * Array of products in the bundle, indexed by their bundle connection ID
     * @var array<string, SalesChannelProductEntity>
     */
    protected array $products = [];

    /**
     * The final calculated price including all calculations and adjustments
     * Contains additional information like tax calculations and currency conversions
     */
    protected ?CalculatedPrice $calculatedBundlePrice = null;

    /**
     * Original calculated prices for each product in the bundle before discounts
     * Indexed by bundle connection ID
     * @var array<string, CalculatedPrice>
     */
    protected array $calculatedProductPrices = [];

    /**
     * Discounted prices for each product after applying bundle-level discounts
     * Indexed by bundle connection ID
     * @var array<string, Price>
     */
    protected array $discountedProductPrices = [];

    public function __construct(Price $price, CalculatedPrice $calculatedBundlePrice)
    {
        $this->discountedBundlePrice = $price;
        $this->calculatedBundlePrice = $calculatedBundlePrice;
    }

    /**
     * Adds provided product to the bundle price calculation
     * This method:
     * 1. Validates and extracts bundle connection data
     * 2. Handles advanced pricing rules if applicable
     * 3. Updates the bundle's real price based on the product's price and quantity
     */
    public function addProduct(SalesChannelProductEntity $product): void
    {
        /** @var BundleConnectionData|null $bundleConnectionData */
        $bundleConnectionData = $product->getExtension(BundleConnectionData::EXTENSION_NAME);

        // Skip if product is invalid or already added
        if (
            $bundleConnectionData === null
            || key_exists($bundleConnectionData->getId(), $this->products)
        ) {
            return;
        }

        $this->products[$bundleConnectionData->getId()] = $product;

        // Handle advanced pricing rules
        $advancedCalculatedPrice = $product->getCalculatedPrices()->first();

        if ($advancedCalculatedPrice !== null) {
            // Find matching advanced price rule
            foreach ($product->getPrices() as $advancedPrice) {
                foreach ($advancedPrice->getPrice() as $price) {
                    // Compare gross and net prices rounded to 2 decimal places
                    // If either matches the unit price from advanced calculation, we found the right price rule
                    if (
                        round($price->getGross(), 2) === round($advancedCalculatedPrice->getUnitPrice(), 2)
                        || round($price->getNet(), 2) === round($advancedCalculatedPrice->getUnitPrice(), 2)
                    ) {
                        break 2; // Break out of both nested loops once we find a match
                    }
                }
            }
        } else {
            $price = $product->getPrice()->first();
        }

        if (empty($price)) {
            return;
        }

        $this->incrementBundlePriceWithPrice($price, $bundleConnectionData->getQuantity());
    }

    /**
     * Recalculates the total discount and updates discount-related properties
     * Called after price modifications to ensure discount values are current
     */
    public function recalculate(): void
    {
        $realBundlePrice = $this->getRealBundlePrice();
        $discountedBundlePrice = $this->getDiscountedBundlePrice();

        $this->totalGrossDiscount = $realBundlePrice->getGross() - $discountedBundlePrice->getGross();
        $this->totalNetDiscount = $realBundlePrice->getNet() - $discountedBundlePrice->getNet();
    }

    /**
     * Returns the total number of unique products in the bundle
     */
    public function getBundleSize(): int
    {
        return count($this->products);
    }

    /**
     * Retrieves the calculated price for a specific bundle connection
     */
    public function getCalculatedProductPrice(string $bundleConnectionEntityId): ?CalculatedPrice
    {
        return $this->calculatedProductPrices[$bundleConnectionEntityId] ?? null;
    }

    /**
     * Sets the calculated price for a specific bundle connection
     */
    public function setCalculatedProductPrice(string $bundleConnectionEntityId, CalculatedPrice $price): void
    {
        if (!key_exists($bundleConnectionEntityId, $this->products)) {
            throw new ProductNotInBundleException(sprintf('Try to set calculated price for a none existing connection with id %s in bundle', $bundleConnectionEntityId));
        }

        $this->calculatedProductPrices[$bundleConnectionEntityId] = $price;
    }

    public function getDiscountedProductPrice(string $bundleConnectionId): Price
    {
        return $this->discountedProductPrices[$bundleConnectionId];
    }

    public function setDiscountedProductPrice(string $bundleConnectionEntityId, Price $price): void
    {
        if (!key_exists($bundleConnectionEntityId, $this->products)) {
            throw new ProductNotInBundleException(sprintf('Try to set discounted price for a none existing connection with id %s in bundle', $bundleConnectionEntityId));
        }

        $this->discountedProductPrices[$bundleConnectionEntityId] = $price;
    }

    public function getTotalGrossDiscount(): ?float
    {
        return $this->totalGrossDiscount;
    }

    public function getTotalNetDiscount(): ?float
    {
        return $this->totalNetDiscount;
    }

    public function getDiscountedBundlePrice(): ?Price
    {
        return $this->discountedBundlePrice;
    }

    public function setDiscountedBundlePrice(?Price $discountedBundlePrice): void
    {
        $this->discountedBundlePrice = $discountedBundlePrice;
    }

    public function getRealBundlePrice(): ?Price
    {
        return $this->realBundlePrice;
    }

    public function setRealBundlePrice(?Price $realBundlePrice): void
    {
        $this->realBundlePrice = $realBundlePrice;
    }

    public function getProducts(): array
    {
        return $this->products;
    }

    public function getCalculatedBundlePrice(): ?CalculatedPrice
    {
        return $this->calculatedBundlePrice;
    }

    public function setCalculatedBundlePrice(?CalculatedPrice $calculatedBundlePrice): void
    {
        $this->calculatedBundlePrice = $calculatedBundlePrice;
    }

    public function getCalculatedProductPrices(): array
    {
        return $this->calculatedProductPrices;
    }

    public function setCalculatedProductPrices(array $calculatedProductPrices): void
    {
        $this->calculatedProductPrices = $calculatedProductPrices;
    }

    public function getDiscountedProductPrices(): array
    {
        return $this->discountedProductPrices;
    }

    public function setDiscountedProductPrices(array $discountedProductPrices): void
    {
        $this->discountedProductPrices = $discountedProductPrices;
    }

    /**
     * Increments the bundle's real price by adding a product's price multiplied by its quantity
     * Creates a new price object if one doesn't exist, otherwise updates the existing one
     */
    private function incrementBundlePriceWithPrice(Price $price, int $quantity): void
    {
        $realBundlePrice = $this->getRealBundlePrice();

        if ($realBundlePrice === null) {
            $this->setRealBundlePrice(new Price(
                $price->getCurrencyId(),
                $price->getNet() * $quantity,
                $price->getGross() * $quantity,
                false
            ));
            return;
        }

        $this->setRealBundlePrice(new Price(
            $realBundlePrice->getCurrencyId(),
            $realBundlePrice->getNet() + ($price->getNet() * $quantity),
            $realBundlePrice->getGross() + ($price->getGross() * $quantity),
            false
        ));
    }
}
