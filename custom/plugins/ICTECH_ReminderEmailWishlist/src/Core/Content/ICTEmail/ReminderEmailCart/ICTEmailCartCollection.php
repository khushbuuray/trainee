<?php

declare(strict_types=1);

namespace ICTECH_ReminderEmailWishlist\Core\Content\ICTEmail\ReminderEmailCart;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @package core
 * @method void                add(ICTEmailCartEntity $entity)
 * @method void                set(string $key, ICTEmailCartEntity $entity)
 * @method ICTEmailCartEntity[]    getIterator()
 * @method ICTEmailCartEntity[]    getElements()
 * @method ICTEmailCartEntity|null get(string $key)
 * @method ICTEmailCartEntity|null first()
 * @method ICTEmailCartEntity|null last()
 */
class ICTEmailCartCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return ICTEmailCartEntity::class;
    }
}
