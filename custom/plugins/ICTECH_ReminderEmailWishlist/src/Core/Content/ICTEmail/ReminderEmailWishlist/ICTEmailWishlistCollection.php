<?php

declare(strict_types=1);

namespace ICTECH_ReminderEmailWishlist\Core\Content\ICTEmail\ReminderEmailWishlist;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @package core
 * @method void                add(ICTEmailWishlistEntity $entity)
 * @method void                set(string $key, ICTEmailWishlistEntity $entity)
 * @method ICTEmailWishlistEntity[]    getIterator()
 * @method ICTEmailWishlistEntity[]    getElements()
 * @method ICTEmailWishlistEntity|null get(string $key)
 * @method ICTEmailWishlistEntity|null first()
 * @method ICTEmailWishlistEntity|null last()
 */
class ICTEmailWishlistCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return ICTEmailWishlistEntity::class;
    }
}
