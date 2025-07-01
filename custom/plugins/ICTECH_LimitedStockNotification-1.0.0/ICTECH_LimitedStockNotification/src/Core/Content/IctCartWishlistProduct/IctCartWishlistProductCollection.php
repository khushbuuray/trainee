<?php declare(strict_types=1);

namespace ICTECH_LimitedStockNotification\Core\Content\IctCartWishlistProduct;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @package core
 * @method void                add(IctCartWishlistProductEntity $entity)
 * @method void                set(string $key, IctCartWishlistProductEntity $entity)
 * @method IctCartWishlistProductEntity[]    getIterator()
 * @method IctCartWishlistProductEntity[]    getElements()
 * @method IctCartWishlistProductEntity|null get(string $key)
 * @method IctCartWishlistProductEntity|null first()
 * @method IctCartWishlistProductEntity|null last()
 */

class IctCartWishlistProductCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return IctCartWishlistProductEntity::class;
    }
}
