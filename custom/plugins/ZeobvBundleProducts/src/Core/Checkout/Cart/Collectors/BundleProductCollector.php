<?php

declare(strict_types=1);

namespace Zeobv\BundleProducts\Core\Checkout\Cart\Collectors;

use Throwable;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\CartBehavior;
use Zeobv\BundleProducts\Service\ConfigService;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Content\Product\ProductCollection;
use Shopware\Core\Checkout\Cart\Error\GenericCartError;
use Zeobv\BundleProducts\Service\BundlePriceCalculator;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Zeobv\BundleProducts\Struct\BundleProduct\BundlePrice;
use Shopware\Core\Checkout\Cart\CartDataCollectorInterface;
use Shopware\Core\Checkout\Cart\LineItem\CartDataCollection;
use Shopware\Core\Checkout\Cart\LineItem\LineItemCollection;
use Zeobv\BundleProducts\Struct\BundleProduct\ProductBundle;
use Shopware\Core\Checkout\Cart\Delivery\Struct\DeliveryTime;
use Shopware\Core\Checkout\Cart\LineItem\QuantityInformation;
use Shopware\Core\Checkout\Cart\Price\QuantityPriceCalculator;
use Zeobv\BundleProducts\Struct\BundleProduct\BundleConnectionData;
use Shopware\Core\Checkout\Cart\Delivery\Struct\DeliveryInformation;
use Zeobv\BundleProducts\Struct\BundleProduct\CalculatedBundlePrice;
use Shopware\Core\Checkout\Cart\Price\Struct\QuantityPriceDefinition;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Pricing\CashRoundingConfig;
use Zeobv\BundleProducts\Struct\BundleProduct\LineItem as BundleProductLineItem;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Context;

class BundleProductCollector implements CartDataCollectorInterface
{
    public const BUNDLE_PRODUCT_DATA_KEY = 'zeobv_bundle_product-';
    public const DISCOUNT_DATA_KEY = 'zeobv_bundle_product-discount-';
    public const IS_CUSTOM_BUNDLE_CONFIGURATION_KEY = 'zeobvIsCustomBundleConfiguration';

    protected ?CashRoundingConfig $cachedRoundingConfig = null;

    private EntityRepository $productRepository;

    public function __construct(
        private QuantityPriceCalculator $quantityPriceCalculator,
        private BundlePriceCalculator $bundlePriceCalculator,
        private ConfigService $configService,
        EntityRepository $productRepository,
    ) {
        $this->productRepository = $productRepository;
    }

    public function collect(CartDataCollection $data, Cart $original, SalesChannelContext $context, CartBehavior $behavior): void
    {
        $isActive = $this->configService->isActive($context->getSalesChannelId());

        if ($isActive === true){
            // get all bundle products of current cart
            $productLineItems = $original->getLineItems()->filterType(LineItem::PRODUCT_LINE_ITEM_TYPE);

            if (count($productLineItems) > 0) {
                $this->setDecimalPrecisionToHighAccuracy($context);

                try {
                    $this->collectBundleItems(
                        $productLineItems,
                        $data,
                        $context,
                    );
                } catch (Throwable $e) {
                    $this->resetDecimalPrecision($context);

                    if (!$e instanceof \Shopware\Core\Checkout\Cart\Error\Error) {
                        throw $e;
                    }

                    $original->getLineItems()->remove($e->getId());

                    $original->addErrors($e);
                }

                $this->resetDecimalPrecision($context);
            }
        }
    }

    public function collectBundleItems(LineItemCollection $productLineItems, CartDataCollection $data, SalesChannelContext $context): void
    {
        /** @var LineItem $productLineItem */
        foreach ($productLineItems as $productLineItem) {
            if ($productLineItem->getType() !== LineItem::PRODUCT_LINE_ITEM_TYPE) {
                continue;
            }

            /** @var SalesChannelProductEntity|null $bundleProduct */
            $bundleProduct = $data->get('product-' . $productLineItem->getReferencedId());

            if (
                !$bundleProduct
                || !$bundleProduct->hasExtension(ProductBundle::EXTENSION_NAME)
            ) {
                continue;
            }

            $bundleDiscount = $this->bundlePriceCalculator->createForBundleProduct(
                $bundleProduct,
                $context,
                $productLineItem->getQuantity(),
            );

            $data->set(
                self::DISCOUNT_DATA_KEY . $productLineItem->getReferencedId(),
                $bundleDiscount,
            );

            $data->set(
                self::BUNDLE_PRODUCT_DATA_KEY . $productLineItem->getReferencedId(),
                $bundleProduct,
            );

            $customFields = $productLineItem->getPayload()['customFields'];
            $productLineItem->setPayloadValue(
                'zeobvBundleProductsShowOnStorefront',
                $customFields['zeobvBundleProductsShowOnStorefront'] ?? false
            );

            $productLineItem->setPayloadValue(
                'zeobvBundleProductsOfferBundleOptionally',
                $customFields['zeobvBundleProductsOfferBundleOptionally'] ?? false
            );

            $productLineItem->setPayloadValue(
                'zeobvBundleProductsShowItemPricesOnStorefront',
                $customFields['zeobvBundleProductsShowItemPricesOnStorefront'] ?? false
            );

            $productLineItem->setPayloadValue(
                'zeobvBundleProductsShowItemsTotalPriceOnStorefront',
                $customFields['zeobvBundleProductsShowItemsTotalPriceOnStorefront'] ?? false
            );
            
            $customFields = $this->getProductCustomField($productLineItem->getId(), Context::createDefaultContext());

            if ($customFields) {
                if (isset($customFields['zeobvBundleProductPriceMode']) && $customFields['zeobvBundleProductPriceMode'] == "sumProductPrices") {
                    if ($productLineItem->getPrice() !== null) {
                        $productLineItem->setPriceDefinition(
                            new QuantityPriceDefinition(
                                $productLineItem->getPrice()->getUnitPrice(),
                                $context->buildTaxRules($bundleProduct->getTaxId()),
                                $productLineItem->getQuantity(),
                            ),
                        );
                        /** @var QuantityPriceDefinition $qtyPriceDefinition */
                        $qtyPriceDefinition = $productLineItem->getPriceDefinition();
                        $productLineItem->setPrice($this->quantityPriceCalculator->calculate($qtyPriceDefinition, $context));
                    }
                }
            }

            if ($productLineItem->getChildren()->count() > 0) {
                continue;
            }

            $this->enrichBundleLineItem($productLineItem, $bundleProduct, $bundleDiscount, $context);

            if (
                isset($productLineItem->getPayload()['customFields']['zeobvBundleProductsOfferBundleOptionally'])
                && $productLineItem->getPayload()['customFields']['zeobvBundleProductsOfferBundleOptionally'] === true
                && $productLineItem->getPayloadValue('includeBundleContent') === false
            ) {
                continue;
            }

            /** @var ProductBundle $productBundle */
            $productBundle = $bundleProduct->getExtension(ProductBundle::EXTENSION_NAME);
            $bundleContent = $productLineItem->getPayloadValue('bundleContent');

            $productsInBundle = is_array($bundleContent) ? $this->getProductsInBundleBasedOnBundleContent(
                $productBundle->getProducts(),
                $bundleContent,
            ) : $productBundle->getProducts();

            $productLineItem->setPayloadValue(
                'zeobvProductsInBundle',
                $productsInBundle,
            );

            if ($productsInBundle === []) {
                throw new GenericCartError(
                    $productLineItem->getId(),
                    'no_bundle_items',
                    [],
                    \Shopware\Core\Checkout\Cart\Error\Error::LEVEL_ERROR,
                    true,
                    true,
                    true,
                );
            }

            $originalHash = md5(json_encode($productBundle->getProducts()) ?: '0');
            $currentHash = md5(json_encode($productsInBundle) ?: '1');
            if (
                $productBundle->getConfig()->getPriceMode() === BundlePrice::BUNDLE_PRICE_MODE_SUM
                && $originalHash !== $currentHash
            ) {
                $productLineItem->setPayloadValue(self::IS_CUSTOM_BUNDLE_CONFIGURATION_KEY, true);
            }

            $this->addMissingProducts($productLineItem, $productsInBundle, $bundleDiscount, $context);
        }
    }

    private function enrichBundleLineItem(LineItem $bundleLineItem, ProductEntity $bundleProduct, CalculatedBundlePrice $bundleDiscount, SalesChannelContext $context): void
    {
        /** @var ProductBundle $productBundle */
        $productBundle = $bundleProduct->getExtension(ProductBundle::EXTENSION_NAME);
        $productsInBundle = $productBundle->getProducts();

        if (!$bundleLineItem->getLabel()) {
            $bundleLineItem->setLabel($bundleProduct->getTranslation('name'));
        }

        if (count($productsInBundle) < 1) {
            throw new \RuntimeException(sprintf('Bundle "%s" has no products', $bundleLineItem->getReferencedId()));
        }

        if (empty($bundleProduct->getCustomFields()['zeobvBundleProductsShowOnStorefront'])) {
            $bundleLineItem->setPayloadValue('zeobvHideBundleProductChildrenInStorefront', true);
        } elseif ($bundleLineItem->hasPayloadValue('zeobvHideBundleProductChildrenInStorefront')) {
            $bundleLineItem->removePayloadValue('zeobvHideBundleProductChildrenInStorefront');
        }

        if (empty($bundleProduct->getCustomFields()['zeobvBundleProductsShowItemPricesOnStorefront'])) {
            $bundleLineItem->setPayloadValue('zeobvHideBundleProductPricesInStorefront', true);
        } elseif ($bundleLineItem->hasPayloadValue('zeobvHideBundleProductPricesInStorefront')) {
            $bundleLineItem->removePayloadValue('zeobvHideBundleProductPricesInStorefront');
        }

        $bundleProductDeliveryTime = $bundleProduct->getDeliveryTime();
        if ($bundleProductDeliveryTime !== null) {
            $bundleProductDeliveryTime = DeliveryTime::createFromEntity($bundleProduct->getDeliveryTime());
        }

        $bundleLineItem->setRemovable(true)->setStackable(true);
        if (!in_array('is-download', $bundleProduct->getStates())) {
            $bundleLineItem->setDeliveryInformation(
                new DeliveryInformation(
                    $bundleProduct->getStock(),
                    (float) $bundleProduct->getWeight(),
                    (bool) $bundleProduct->getShippingFree(),
                    $bundleProduct->getRestockTime(),
                    $bundleProductDeliveryTime,
                ),
            );
        }

        if (!$bundleLineItem->getPriceDefinition()) {
            $bundleLineItem->setPriceDefinition(
                new QuantityPriceDefinition(
                    $bundleDiscount->getCalculatedBundlePrice()->getUnitPrice(),
                    $context->buildTaxRules($bundleProduct->getTaxId()),
                    $bundleLineItem->getQuantity(),
                ),
            );

            if (!$bundleLineItem->getPrice()) {
                /** @var QuantityPriceDefinition $qtyPriceDefinition */
                $qtyPriceDefinition = $bundleLineItem->getPriceDefinition();
                $bundleLineItem->setPrice($this->quantityPriceCalculator->calculate($qtyPriceDefinition, $context));
            }
        }

        if (!$bundleLineItem->getQuantityInformation()) {
            $bundleLineItem->setQuantityInformation(new QuantityInformation());
        }
    }

    private function addMissingProducts(LineItem $bundleLineItem, array $productsInBundle, CalculatedBundlePrice $bundleDiscount, SalesChannelContext $context): void
    {
        $bundlePayloadData = [];

        usort($productsInBundle, static function (ProductEntity $a, ProductEntity $b) {
            $aConnectionData = $a->getExtension(BundleConnectionData::EXTENSION_NAME);
            $bConnectionData = $b->getExtension(BundleConnectionData::EXTENSION_NAME);

            if (!$aConnectionData instanceof BundleConnectionData || !$bConnectionData instanceof BundleConnectionData) {
                return 0;
            }

            return $aConnectionData->getPosition() <=> $bConnectionData->getPosition();
        });

        foreach ($productsInBundle as $product) {
            /** @var BundleConnectionData $bundleConnectionData */
            $bundleConnectionData = $product->getExtension(BundleConnectionData::EXTENSION_NAME);

            // Backwards compatiblity patch remove during update to Shopware 6.5.0
            $lineItemType = $this->configService->useCustomLineItemType() ? BundleProductLineItem::BUNDLE_PRODUCT_LINE_ITEM_TYPE : LineItem::PRODUCT_LINE_ITEM_TYPE;

            // the ProductCartProcessor will enrich the product further
            $productLineItem = new LineItem($bundleConnectionData->getId(), $lineItemType, $product->getId());
            $productLineItem->setLabel($product->getTranslation('name'));
            $productLineItem->setReferencedId($product->getId());
            $productLineItem->setCover($product->getCover() ? $product->getCover()->getMedia() : null);
            $productLineItem->setDescription($bundleConnectionData->getComment());
            $productLineItem->setStackable(true);

            $bundleItemQuantity = $bundleConnectionData->getQuantity();

            $productLineItem->setQuantity(
                $bundleItemQuantity * $bundleLineItem->getQuantity()
            );

            $productLineItem->setPrice($bundleDiscount->getCalculatedProductPrice($bundleConnectionData->getId()));

            $qtyPriceDefinition = new QuantityPriceDefinition(
                $productLineItem->getPrice()->getUnitPrice(),
                $context->buildTaxRules($product->getTaxId()),
                $bundleLineItem->getQuantity(),
            );

            $productLineItem->setPriceDefinition($qtyPriceDefinition);

            $productLineItem->setPayload([
                'zeobvCustomLineItemType' => BundleProductLineItem::BUNDLE_PRODUCT_LINE_ITEM_TYPE,
                'zeobvBundleConnectionId' => $bundleConnectionData->getId(),
                'productNumber' => $product->getProductNumber(),
                'customFields' => $product->getCustomFields() ? $product->getCustomFields() : [], 
                'taxId' => $product->getTaxId(),
                'manufacturer' => [
                    'id' => $product->getManufacturerId(),
                    'name' => $product->getManufacturer() ? $product->getManufacturer()->getTranslation('name') : null,
                ],
                'comment' => $bundleConnectionData->getComment(),
                'quantity' => $bundleItemQuantity,
            ]);

            if ($bundleLineItem->hasPayloadValue('zeobvHideBundleProductChildrenInStorefront')) {
                $productLineItem->setPayloadValue('zeobvHideBundleProductChildrenInStorefront', true);
            } elseif ($productLineItem->hasPayloadValue('zeobvHideBundleProductChildrenInStorefront')) {
                $productLineItem->removePayloadValue('zeobvHideBundleProductChildrenInStorefront');
            }

            if ($bundleLineItem->hasPayloadValue('zeobvHideBundleProductPricesInStorefront')) {
                $productLineItem->setPayloadValue('zeobvHideBundleProductPricesInStorefront', true);
            } elseif ($productLineItem->hasPayloadValue('zeobvHideBundleProductPricesInStorefront')) {
                $productLineItem->removePayloadValue('zeobvHideBundleProductPricesInStorefront');
            }

            if ($bundleLineItem->getChildren()->has($bundleConnectionData->getId())) {
                $bundleLineItem->getChildren()->set($bundleConnectionData->getId(), $productLineItem);
            } else {
                $bundleLineItem->addChild($productLineItem);
            }

            if ($bundleLineItem->getDeliveryInformation()) {
                $this->processDeliveryInformation($bundleLineItem, $productLineItem, $product);
            }

            $bundlePayloadData[] = $this->createPayloadFromProductBundleConnection($product, $bundleDiscount);
        }

        $this->resetDecimalPrecision($context);
        $payload = [];
        $payload['bundleRelations'] = $bundlePayloadData;

        $bundleLineItem->setPayload($payload);
    }

    private function createPayloadFromProductBundleConnection(ProductEntity $productInBundle, CalculatedBundlePrice $bundleDiscount): array
    {
        /** @var BundleConnectionData $bundleConnectionData */
        $bundleConnectionData = $productInBundle->getExtension(BundleConnectionData::EXTENSION_NAME);

        $price = $bundleDiscount->getDiscountedProductPrice($bundleConnectionData->getId());

        return [
            'id' => $bundleConnectionData->getId(),
            'productId' => $bundleConnectionData->getProductId(),
            'productNumber' => $productInBundle->getProductNumber(),
            'productName' => $productInBundle->getTranslation('name'),
            'productParentId' => $productInBundle->getParentId(),
            'productPrice' => [
                'currencyId' => $price->getCurrencyId(),
                'net' => $price->getNet(),
                'gross' => $price->getGross(),
                'linked' => $price->getLinked(),
                'listPrice' => $price->getListPrice() ? $price->getListPrice()->getVars() : [],
            ],
            'productCreatedAt' => $productInBundle->getCreatedAt(),
            'productUpdatedAt' => $productInBundle->getUpdatedAt(),
            'quantityInBundle' => $bundleConnectionData->getQuantity(),
            'comment' => $bundleConnectionData->getComment(),
        ];
    }

    private function getProductsInBundleBasedOnBundleContent(array $productSelection, array $bundleContent): array
    {
        $productsInBundle = [];

        foreach ($bundleContent as $connectionId => $contentData) {
            $productId = $contentData['productId'];
            /** @var ProductEntity $product */
            $product = $productSelection[$connectionId] ?? null;

            if (!$product instanceof ProductEntity) {
                continue;
            }

            if (
                $contentData['isChild'] === true
                && $product->getChildren() !== null
                && $product->getChildren()->has($productId) === true
            ) {
                /** @var ProductEntity $variantProduct */
                $variantProduct = $product->getChildren()->get($productId);
                $variantProduct->setExtensions($product->getExtensions());
                $product = $variantProduct;
            }

            /** @var BundleConnectionData $bundleConnectionData */
            $bundleConnectionData = $product->getExtension(BundleConnectionData::EXTENSION_NAME);
            $product->addExtension(
                BundleConnectionData::EXTENSION_NAME,
                new BundleConnectionData(
                    $bundleConnectionData->getId(),
                    $bundleConnectionData->getBundleProductId(),
                    $bundleConnectionData->getProductId(),
                    $bundleConnectionData->isModifiable()
                    ? $contentData['quantity']
                    : $bundleConnectionData->getQuantity(),
                    $bundleConnectionData->getPosition(),
                    $bundleConnectionData->isModifiable(),
                    $bundleConnectionData->isOptional(),
                    $bundleConnectionData->getComment(),
                ),
            );

            $product->setUniqueIdentifier($connectionId);

            $productsInBundle[] = $product;
        }

        // Add non-optional products from productSelection
        foreach ($productSelection as $connectionId => $product) {
            if (!isset($bundleContent[$connectionId])) {
                /** @var BundleConnectionData $bundleConnectionData */
                $bundleConnectionData = $product->getExtension(BundleConnectionData::EXTENSION_NAME);

                if (!$bundleConnectionData->isOptional()) {
                    $product->setUniqueIdentifier($connectionId);
                    $productsInBundle[] = $product;
                }
            }
        }

        return $productsInBundle;
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

    private function processDeliveryInformation(LineItem $bundleLineItem, LineItem $productLineItem, ProductEntity $product): void
    {
        // Return early if product delivery information already exists
        if ($productLineItem->getDeliveryInformation() !== null) {
            return;
        }

        // Return early if no delivery time exists for the product
        $productDeliveryTime = $product->getDeliveryTime();
        if ($productDeliveryTime !== null) {
            $productDeliveryTime = DeliveryTime::createFromEntity($productDeliveryTime);
        }

        $productLineItem->setDeliveryInformation(
            new DeliveryInformation(
                $product->getStock(),
                $product->getWeight(),
                (bool) $product->getShippingFree(),
                $product->getRestockTime(),
                $productDeliveryTime,
            ),
        );
    }

    private function getProductCustomField(string $productId, Context $context): ?array
    {
        $criteria = new Criteria([$productId]);
        $criteria->addAssociation('customFields'); // Ensure customFields are loaded

        $product = $this->productRepository->search($criteria, $context)->first();

        return $product ? $product->getCustomFields() : null;
    }
}
