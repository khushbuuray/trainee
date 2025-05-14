<?php declare(strict_types=1);  

namespace Blog\Core\Content\Blog\Aggregate;

use Blog\Core\Content\Blog\BlogDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityTranslationDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\AllowHtml;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField; 
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\LongTextField;

class BlogTranslationDefinition extends EntityTranslationDefinition
{
    public const ENTITY_NAME = 'blog_translation';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }   

    public function getParentDefinitionClass(): string
    {
        return BlogDefinition::class;
    }
    
    public function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new StringField('name', 'name'))->addFlags(new Required()),
            (new LongTextField('description', 'description'))->addFlags(new Required(),new AllowHtml()),
            (new StringField('author', 'author'))->addFlags(new Required()),
        ]);
    }
}