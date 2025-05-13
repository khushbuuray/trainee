<?php declare(strict_types=1);

namespace Blog\Core\Content\Blog;

use Blog\Blog;
use Doctrine\DBAL\Types\StringType;
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

class BlogDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'blog';
   
    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(),new PrimaryKey()),
            (new StringField('name', 'name'))->addFlags(new Required()),
            (new StringField('description', 'description'))->addFlags(new Required()),
            (new DateField('release_date', 'release_date'))->addFlags(new Required()),
            (new BoolField('active', 'active')),
            (new StringField('author', 'author'))->addFlags(new Required()),
       
            new ManyToManyAssociationField(
                'categories',
                BlogCategoryDefinition::class,
                BlogCategoryMappingDefinition::class,
                'blog_id',
                'category_id'
            ),        
         new ManyToManyAssociationField(
            'products',
            ProductDefinition::class,
            BlogProductMappingDefinition::class,
            'blog_id',
            'product_id',
            'blog_product_mapping', 
            'product_version_id'
        ),
        new OneToManyAssociationField(
            'categoryMappings',
            BlogCategoryMappingDefinition::class,
            'blog_id'
        ),

        new OneToManyAssociationField(
            'productMappings',
            BlogProductMappingDefinition::class,
            'blog_id'
        ),

        ]);
    }
}