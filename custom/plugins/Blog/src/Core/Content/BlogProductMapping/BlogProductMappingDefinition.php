<?php declare(strict_types=1);

namespace Blog\Core\Content\BlogProductMapping;

use Blog\Core\Content\Blog\BlogDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;           
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CreatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\UpdatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\MappingEntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ReferenceVersionField;

class BlogProductMappingDefinition extends MappingEntityDefinition
{
    public const ENTITY_NAME = 'blog_product_mapping';  

    public function getEntityName(): string     
    {
        return self::ENTITY_NAME;
    }   

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new FkField('blog_id', 'blogId', BlogDefinition::class))->addFlags(new PrimaryKey(), new Required()),
            (new FkField('product_id', 'productId', ProductDefinition::class))->addFlags(new PrimaryKey(), new Required()),
            new ReferenceVersionField(ProductDefinition::class), 
            new ManyToOneAssociationField(
                'blog',
                'blog_id',
                BlogDefinition::class,
                'id',
                false
            ),

            new ManyToOneAssociationField(
                'product',
                'product_id',
                ProductDefinition::class,
                'id',
                false,
            ),
          
        ]);
    }  
}   