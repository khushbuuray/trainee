<?php

declare(strict_types=1);

namespace ICTECH_ReminderEmailWishlist\Core\Content\ICTEmail\ReminderEmailRestock;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @package core
 * @method void                add(ICTEmailRestockEntity $entity)
 * @method void                set(string $key, ICTEmailRestockEntity $entity)
 * @method ICTEmailRestockEntity[]    getIterator()
 * @method ICTEmailRestockEntity[]    getElements()
 * @method ICTEmailRestockEntity|null get(string $key)
 * @method ICTEmailRestockEntity|null first()
 * @method ICTEmailRestockEntity|null last()
 */
class ICTEmailRestockCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return ICTEmailRestockEntity::class;
    }
}
