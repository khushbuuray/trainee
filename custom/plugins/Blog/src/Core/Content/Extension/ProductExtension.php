<?php declare(strict_types=1);

namespace Blog\Core\Content\Extension;

use Blog\Core\Content\Blog\BlogDefinition;
use Blog\Core\Content\Blog\BlogProductMappingDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;

class ProductExtension extends EntityExtension
{
    /**
     * Adds the following fields to the ProductDefinition:
     * - blogs: A ManyToManyAssociationField that references the BlogDefinition
     * - blogProductMappings: A OneToManyAssociationField that references the BlogProductMappingDefinition
     *
     * @param FieldCollection $collection
     */
    public function extendFields(FieldCollection $collection):void
    {
        $collection->add(
            new ManyToManyAssociationField(
                'blogs',
                BlogDefinition::class,
                BlogProductMappingDefinition::class,
                'product_id',
                'blog_id',
                'product_version_id' 
            ),                      
        );

        $collection->add(
            new OneToManyAssociationField(
                'blogProductMappings',
                BlogProductMappingDefinition::class,
                'product_id'            
            )
        );        
    }

    public function getDefinitionClass(): string
    {
        return ProductDefinition::class;
    }
}