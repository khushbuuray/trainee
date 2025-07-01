<?php declare(strict_types=1);

namespace Example\Core\Content\Demo\Aggregate;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @package framework
 * @method void                add(DemoTranslationEntity $entity)
 * @method void                set(string $key, DemoTranslationEntity $entity)
 * @method DemoTranslationEntity[]    getIterator()
 * @method DemoTranslationEntity[]    getElements()
 * @method DemoTranslationEntity|null get(string $key)
 * @method DemoTranslationEntity|null first()
 * @method DemoTranslationEntity|null last()
 */
class DemoTranslationCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return DemoTranslationEntity::class;
    }
}