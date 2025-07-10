<?php

declare(strict_types=1);

namespace Zeobv\BundleProducts\Core\Subscriber;

use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Content\Product\ProductEvents;
use Shopware\Core\Framework\Api\Context\AdminSalesChannelApiSource;
use Shopware\Core\Framework\Api\Context\SalesChannelApiSource;
use Zeobv\BundleProducts\Factory\ProductBundleFactory;
use Zeobv\BundleProducts\Repository\BundleProductRepository;
use Zeobv\BundleProducts\Transformer\BundleProductTransformer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntitySearchedEvent;

/**
 * Subscriber that handles the loading and transformation of bundle products.
 *
 * This subscriber performs two main functions:
 * 1. Prevents infinite recursion when loading bundle products by stopping event propagation
 *    when the special bundle product load state is present
 * 2. Enriches loaded products that are bundles with their bundle-specific data by:
 *    - Loading the bundle product contents (child products)
 *    - Creating ProductBundle structs with the bundle configuration
 *    - Transforming the product entities to include bundle information
 *
 * The subscriber operates at a very high priority (9999) to ensure it runs before other
 * product subscribers that might need the bundle information.
 */
class ProductSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly BundleProductRepository  $bundleProductRepository,
        private readonly ProductBundleFactory     $productBundleFactory,
        private readonly BundleProductTransformer $bundleProductTransformer
    )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProductEvents::PRODUCT_LOADED_EVENT => [
                ['onProductLoaded', 9999],
            ],
            EntitySearchedEvent::class => [
                ['onProductSearched', 9999],
            ],
        ];
    }

    /**
     * Prevents infinite recursion when searching for bundle products.
     *
     * If the context contains the bundle product load state, stops event propagation
     * to prevent recursive loading of bundle products.
     */
    public function onProductSearched(EntitySearchedEvent $event): void
    {
        if (
            in_array(
                BundleProductRepository::CONTEXT_STATE_BUNDLE_PRODUCT_LOAD,
                $event->getContext()->getStates()
            )
        ) {
            $event->stopPropagation();
            return;
        }
    }

    /**
     * Enriches loaded products with bundle information.
     *
     * This method:
     * 1. Checks for and prevents recursive loading
     * 2. Creates an index of loaded products for quick lookup
     * 3. Loads bundle product contents for any bundle products
     * 4. Creates ProductBundle structs with configuration
     * 5. Transforms product entities to include bundle data
     *
     * The transformation adds bundle-specific data like:
     * - Child products in the bundle
     * - Bundle configuration from custom fields
     * - Bundle pricing information
     */
    public function onProductLoaded(EntityLoadedEvent $event): void
    {
        $salesChannelId = null;
        $source = $event->getContext()->getSource();
        if ($source instanceof SalesChannelApiSource) {
            $salesChannelId = $source->getSalesChannelId();
        }

        if (
            in_array(
                BundleProductRepository::CONTEXT_STATE_BUNDLE_PRODUCT_LOAD,
                $event->getContext()->getStates()
            )
        ) {
            $event->stopPropagation();
            return;
        }

        # Create an index to be able to quickly reference products later from the ProductCollection using the product ID
        $index = array_flip($event->getIds());

        # Get products of bundles grouped by product id, we can call these "Bundles"
        $bundles = $this->bundleProductRepository->getBundleProductContents(
            $event->getIds(),
            $event->getContext()
        );

        if (count($bundles) < 1) {
            return;
        }

        # Loop over the bundles to determine availability of the so-called "bundle product"
        # and create a ProductBundle struct for later use.
        foreach ($bundles as $bundleProductId => $productsInBundle) {
            $entityIndex = key_exists($index[$bundleProductId], $event->getEntities())
                ? $index[$bundleProductId]
                : $bundleProductId;

            /** @var ProductEntity $productEntity */
            $productEntity = $event->getEntities()[$entityIndex];

            $productConfig = $productEntity->getCustomFields() ?: [];
            $productBundle = $this->productBundleFactory->create(
                $bundleProductId,
                $productsInBundle,
                $productConfig
            );

            $this->bundleProductTransformer->transform($productEntity, $productBundle, $salesChannelId);
        }
    }
}
