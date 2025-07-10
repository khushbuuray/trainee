<?php

declare(strict_types=1);

namespace Zeobv\BundleProducts\Core\Content\Product\Aggregate\ProductBundleConnection;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                               add(ProductBundleConnectionEntity $entity)
 * @method void                               set(string $key, ProductBundleConnectionEntity $entity)
 * @method ProductBundleConnectionEntity[]    getIterator()
 * @method ProductBundleConnectionEntity[]    getElements()
 * @method ProductBundleConnectionEntity|null get(string $key)
 * @method ProductBundleConnectionEntity|null first()
 * @method ProductBundleConnectionEntity|null last()
 */
class ProductBundleConnectionCollection extends EntityCollection
{
    public function getExpectedClass(): string
    {
        return ProductBundleConnectionEntity::class;
    }

    public function filterByBundle(string $bundleProductId): EntityCollection
    {
        return $this->filterByProperty('bundleProductId', $bundleProductId);
    }
}
