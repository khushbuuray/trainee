<?php declare(strict_types=1);

namespace Example\Core\Content\Demo;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @package framework
 * @method void                add(DemoEntity $entity)
 * @method void                set(string $key, DemoEntity $entity)
 * @method DemoEntity[]    getIterator()
 * @method DemoEntity[]    getElements()
 * @method DemoEntity|null get(string $key)
 * @method DemoEntity|null first()
 * @method DemoEntity|null last()
 */
class DemoCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return DemoEntity::class;
    }
}