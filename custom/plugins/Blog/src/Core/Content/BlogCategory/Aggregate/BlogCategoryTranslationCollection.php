<?php declare(strict_types=1);

namespace Blog\Core\Content\BlogCategory\Aggregate;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @package framework
 * @method void                add(BlogCategoryTranslationEntity $entity)
 * @method void                set(string $key, BlogCategoryTranslationEntity $entity)
 * @method BlogCategoryTranslationEntity[]    getIterator()
 * @method BlogCategoryTranslationEntity[]    getElements()
 * @method BlogCategoryTranslationEntity|null get(string $key)
 * @method BlogCategoryTranslationEntity|null first()
 * @method BlogCategoryTranslationEntity|null last()
 */
class BlogCategoryTranslationCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return BlogCategoryTranslationEntity::class;
    }
}