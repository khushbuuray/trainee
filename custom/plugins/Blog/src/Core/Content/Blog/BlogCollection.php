<?php declare(strict_types=1);

namespace Blog\Core\Content\Blog;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @package framework
 * @method void                add(BlogEntity $entity)
 * @method void                set(string $key, BlogEntity $entity)
 * @method BlogEntity[]    getIterator()
 * @method BlogEntity[]    getElements()
 * @method BlogEntity|null get(string $key)
 * @method BlogEntity|null first()
 * @method BlogEntity|null last()
 */
class BlogCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return BlogEntity::class;
    }
}