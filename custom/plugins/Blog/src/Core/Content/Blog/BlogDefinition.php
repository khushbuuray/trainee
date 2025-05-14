<?php declare(strict_types=1);

namespace Blog\Core\Content\Blog;

use Blog\Core\Content\Blog\Aggregate\BlogTranslationDefinition;
use Blog\Core\Content\BlogCategory\BlogCategoryDefinition;
use Blog\Core\Content\BlogCategoryMapping\BlogCategoryMappingDefinition;
use Blog\Core\Content\BlogProductMapping\BlogProductMappingDefinition;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\DateField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslatedField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslationsAssociationField;


class BlogDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'blog';
   
    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }
        public function getEntityClass(): string
    {
        return BlogEntity::class;
    }

    public function getCollectionClass(): string
    {
        return BlogCollection::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(),new PrimaryKey()),
            (new TranslatedField('name', 'name')),
            (new TranslatedField('description', 'description')),
             new TranslationsAssociationField(BlogTranslationDefinition::class, 'blog_id'),
            (new DateField('release_date', 'release_date'))->addFlags(new Required()),
            (new BoolField('active', 'active')),
            (new TranslatedField('author', 'author')),
       
            new ManyToManyAssociationField(
                'blogCategories',
                BlogCategoryDefinition::class,
                BlogCategoryMappingDefinition::class,
                'blog_id',
                'blog_category_id'
            ),        
         new ManyToManyAssociationField(
            'products',
            ProductDefinition::class,
            BlogProductMappingDefinition::class,
            'blog_id',
            'product_id',
        ),       

        ]);
    }
}