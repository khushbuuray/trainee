<?php declare(strict_types=1);

namespace ICTECH_LimitedStockNotification\Core\Content\IctCartWishlist;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @package core
 * @method void                add(IctCartWishlistEntity $entity)
 * @method void                set(string $key, IctCartWishlistEntity $entity)
 * @method IctCartWishlistEntity[]    getIterator()
 * @method IctCartWishlistEntity[]    getElements()
 * @method IctCartWishlistEntity|null get(string $key)
 * @method IctCartWishlistEntity|null first()
 * @method IctCartWishlistEntity|null last()
 */

class IctCartWishlistCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return IctCartWishlistEntity::class;
    }
}
