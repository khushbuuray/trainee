<?php

declare(strict_types=1);

namespace Zeobv\BundleProducts\Struct\BundleProduct;

use Shopware\Core\Framework\DataAbstractionLayer\Pricing\Price;
use Shopware\Core\Framework\DataAbstractionLayer\Pricing\PriceCollection;

class BundlePrice
{
    public const BUNDLE_PRICE_MODE_FIT = 'meetProductPrice';
    public const BUNDLE_PRICE_MODE_SUM = 'sumProductPrices';
    public const BUNDLE_PRICE_MODE_DISABLED = 'noCalculation';

    protected string $bundleProductId;

    protected string $mode;

    protected PriceCollection $bundlePrice;

    public function __construct(
        string $bundleProductId,
        string $mode
    ) {
        $this->bundleProductId = $bundleProductId;
        $this->mode = $mode;

        $this->bundlePrice = new PriceCollection([]);
    }

    public function addPrice(Price $price, int $quantity): void
    {
        $currencyId = $price->getCurrencyId();
        $currentBundlePrice = $this->bundlePrice->get($currencyId);
        if ($currentBundlePrice === null) {
            $this->bundlePrice->set($currencyId, new Price(
                $currencyId,
                0.00,
                0.00,
                false,
                null
            ));
        }
        /* Refresh after initialization */
        $currentBundlePrice = $this->bundlePrice->get($currencyId);

        $gross = $price->getGross() * $quantity;
        $net   = $price->getNet()   * $quantity;

        $newListPrice = null;
        if ($price->getListPrice() !== null) {
            $existingListPrice = $currentBundlePrice->getListPrice();

            $listGross = $price->getListPrice()->getGross() * $quantity;
            $listNet   = $price->getListPrice()->getNet()   * $quantity;

            $newListPrice = new Price(
                $currencyId,
                $existingListPrice
                    ? ($existingListPrice->getNet() + $listNet)
                    : $listNet,
                $existingListPrice
                    ? ($existingListPrice->getGross() + $listGross)
                    : $listGross,
                $price->getListPrice()->getLinked()
            );
        }

        $newBundlePrice = new Price(
            $currencyId,
            $currentBundlePrice->getNet()   + $net,
            $currentBundlePrice->getGross() + $gross,
            $currentBundlePrice->getLinked(),
            $newListPrice
        );

        $this->bundlePrice->set($currencyId, $newBundlePrice);
    }

    public function getPrice(): ?PriceCollection
    {
        return $this->bundlePrice;
    }

    public function getMode(): string
    {
        return $this->mode;
    }
}
