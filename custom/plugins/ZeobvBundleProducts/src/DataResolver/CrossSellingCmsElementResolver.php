<?php

declare(strict_types=1);

namespace Zeobv\BundleProducts\DataResolver;

use Shopware\Core\Framework\Uuid\Uuid;
use Zeobv\BundleProducts\Service\ConfigService;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Content\Product\ProductCollection;
use Shopware\Core\Framework\Adapter\Translation\Translator;
use Zeobv\BundleProducts\Repository\BundleProductRepository;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\SalesChannel\Struct\CrossSellingStruct;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Content\Product\SalesChannel\CrossSelling\CrossSellingElement;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\EntityResolverContext;
use Shopware\Core\Content\Product\Aggregate\ProductCrossSelling\ProductCrossSellingEntity;
use Shopware\Core\Content\Product\SalesChannel\CrossSelling\CrossSellingElementCollection;
use Shopware\Core\Content\Product\SalesChannel\CrossSelling\AbstractProductCrossSellingRoute;
use Shopware\Core\Content\Product\Cms\CrossSellingCmsElementResolver as ShopwareCrossSellingCmsElementResolver;
use Shopware\Core\Content\Product\Aggregate\ProductCrossSellingAssignedProducts\ProductCrossSellingAssignedProductsEntity;
use Shopware\Core\Content\Product\Aggregate\ProductCrossSellingAssignedProducts\ProductCrossSellingAssignedProductsCollection;

/**
 * Extends Shopware's CrossSellingCmsElementResolver to add bundle product cross-selling functionality.
 *
 * This resolver enhances the standard cross-selling CMS element by:
 * 1. Adding bundle product references to product detail pages
 * 2. Respecting configuration settings for bundle references display
 * 3. Creating cross-selling elements for bundle products
 *
 * Key features:
 * - Configurable display modes for bundle references
 * - Customizable maximum number of bundle references
 * - Localized cross-selling tab labels
 * - Integration with existing cross-selling structures
 *
 * The resolver only adds bundle references when:
 * - Bundle reference display is not disabled in config
 * - Display mode is set to cross-selling
 * - Bundle products exist for the current product
 */
class CrossSellingCmsElementResolver extends ShopwareCrossSellingCmsElementResolver
{
    public function __construct(
        readonly AbstractProductCrossSellingRoute $crossSellingLoader,
        private readonly Translator $translator,
        private readonly BundleProductRepository $bundleProductRepository,
        private readonly ConfigService $configService
    ) {
        parent::__construct($crossSellingLoader);
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        return parent::collect(...func_get_args());
    }

    /**
     * Enriches the CMS slot with bundle product cross-selling data.
     *
     * Process:
     * 1. Calls parent implementation for standard cross-selling
     * 2. Checks configuration to determine if bundle references should be added
     * 3. Loads bundle products related to the current product
     * 4. Creates cross-selling elements for bundle products
     * 5. Adds bundle cross-selling to the slot data
     */
    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        parent::enrich(...func_get_args());

        $salesChannelContext = $resolverContext->getSalesChannelContext();
        $disableReference = $this->configService->getDisableReferenceToBundleProductsFromProductDetailPage(
            $salesChannelContext->getSalesChannelId()
        );

        if ($disableReference) {
            return;
        }

        $bundleRefDisplayMode = $this->configService->getBundleReferencesDisplayMode(
            $salesChannelContext->getSalesChannelId()
        );

        if ($bundleRefDisplayMode !== ConfigService::DISPLAY_MODE_CROSS_SELLING) {
            return;
        }

        $product = $this->getProduct($slot, $resolverContext, $result);

        $maxReferences = $this->configService->getMaxNumberOfProductDetailBundleReferences(
            $resolverContext->getSalesChannelContext()->getSalesChannelId()
        );

        $result = $this->bundleProductRepository->getBundleProductsForProductId(
            $product->getId(),
            $maxReferences,
            $resolverContext->getSalesChannelContext()
        );

        if ($result === null) {
            return;
        }

        $locale = $this->resolveLocaleFromContext($resolverContext);
        $productCrossSellingEntity = $this->createProductCrossSellingEntity($product, $maxReferences, $locale);
        $crossSellingElement = $this->createCrossSellingElementFromEntitySearchResult($productCrossSellingEntity, $result);
        $this->addCrossSellingElementToSlot($slot, $crossSellingElement);
    }

    /**
     * Adds a cross-selling element to the CMS slot's data structure.
     * Creates new collections if they don't exist.
     */
    private function addCrossSellingElementToSlot(CmsSlotEntity $slot, CrossSellingElement $crossSellingElement): void
    {
        $struct = $slot->getData();
        if (!$struct instanceof CrossSellingStruct) {
            $struct = new CrossSellingStruct();
        }

        $crossSellingCollection = $struct->getCrossSellings();
        if (!$crossSellingCollection instanceof CrossSellingElementCollection) {
            $crossSellingCollection = new CrossSellingElementCollection();
        }

        $crossSellingCollection->add($crossSellingElement);
        $struct->setCrossSellings($crossSellingCollection);
        $slot->setData($struct);
    }

    /**
     * Creates a CrossSellingElement from search results.
     * Maps products to assigned products and sets up the cross-selling structure.
     */
    private function createCrossSellingElementFromEntitySearchResult(ProductCrossSellingEntity $productCrossSellingEntity, EntitySearchResult $result): CrossSellingElement
    {
        $crossSellingId = Uuid::randomHex();
        $crossSellingElement = new CrossSellingElement();

        /** @var  ProductCollection $productCollection */
        $productCollection = $result->getEntities();

        $assignedProductCollection = new ProductCrossSellingAssignedProductsCollection(
            $productCollection->map(
                static function (SalesChannelProductEntity $product) use ($crossSellingId) {
                    $assignedProduct = new ProductCrossSellingAssignedProductsEntity();
                    $assignedProduct->setId(Uuid::randomHex());
                    $assignedProduct->setProductId($product->getId());
                    $assignedProduct->setPosition(0);
                    $assignedProduct->setCrossSellingId($crossSellingId);

                    return $assignedProduct;
                }
            )
        );

        $productCrossSellingEntity->setId($crossSellingId);
        $productCrossSellingEntity->setAssignedProducts($assignedProductCollection);
        $crossSellingElement->setCrossSelling($productCrossSellingEntity);
        $crossSellingElement->setProducts($productCollection);
        $crossSellingElement->setTotal($result->getTotal());

        return $crossSellingElement;
    }

    /**
     * Creates a ProductCrossSellingEntity with translated name and default settings.
     */
    private function createProductCrossSellingEntity(ProductEntity $product, int $maxReferences, ?string $locale): ProductCrossSellingEntity
    {
        $name = $this->translator->trans(
            'zeobv-bundle-products.detail.bundleCrossSellingTabLabel',
            [],
            null,
            $locale
        );

        $crossSellingEntity = new ProductCrossSellingEntity();
        $crossSellingEntity->assign([
            'id' => Uuid::randomHex(),
            '_entityName' => 'product_cross_selling',
            'translated' => [
                'name' => $name,
            ],
            'name' => $name,
            'position' => 100,
            'sortBy' => 'name',
            'sortDirection' => 'ASC',
            'limit' => $maxReferences,
            'active' => true,
            'productId' => $product->getId(),
            'type' => 'productList',
        ]);

        return $crossSellingEntity;
    }

    /**
     * Retrieves the product entity from the CMS slot configuration.
     * Handles both mapped and static product configurations.
     */
    private function getProduct(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): ?SalesChannelProductEntity
    {
        $config = $slot->getFieldConfig();
        $productConfig = $config->get('product');

        if ($productConfig === null || $productConfig->getValue() === null) {
            return null;
        }

        $product = null;

        if ($productConfig->isMapped() && $resolverContext instanceof EntityResolverContext) {
            $product = $this->resolveEntityValue(
                $resolverContext->getEntity(),
                $productConfig->getStringValue()
            );
        }

        if ($productConfig->isStatic()) {
            $product = $this->getSlotProduct(
                $slot,
                $result,
                $productConfig->getStringValue()
            );
        }

        if (!$product instanceof SalesChannelProductEntity) {
            return null;
        }

        return $product;
    }

    /**
     * Extracts the locale from the resolver context's request.
     */
    private function resolveLocaleFromContext(ResolverContext $context): string
    {
        return $context->getRequest()->getLocale();
    }
}
