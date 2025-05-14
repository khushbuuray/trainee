<?php declare(strict_types=1);

namespace Blog\Core\Content\BlogCategory;

use Blog\Core\Content\BlogCategoryMapping\BlogCategoryMappingDefinition;
use Blog\Core\Content\Blog\BlogDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;   
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslatedField;

class BlogCategoryDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'blog_category';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }       

    public function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            (new TranslatedField('name', 'name')),
            new ManyToManyAssociationField
            (
                'blogs',
                BlogDefinition::class,
                BlogCategoryMappingDefinition::class,
                'blog_category_id',
                'blog_id'                   
            ),
           
        ]);
    }    
}