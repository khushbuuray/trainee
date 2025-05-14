<?php declare(strict_types=1);

namespace Blog\Core\Content\BlogCategory\Aggregate;

use Blog\Core\Content\BlogCategory\BlogCategoryDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityTranslationDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;

class BlogCategoryTranslationDefinition extends EntityTranslationDefinition
{

public  const ENTITY_NAME = 'blog_category_translation';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }
        public function getEntityClass(): string
    {
        return BlogCategoryTranslationEntity::class;
    }

    public function getCollectionClass(): string
    {
        return BlogCategoryTranslationCollection::class;
    }

    public function getParentDefinitionClass(): string
    {
        return BlogCategoryDefinition::class;
    }

    public function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new StringField('name', 'name'))->addFlags(new Required()),
        ]);
    }
}