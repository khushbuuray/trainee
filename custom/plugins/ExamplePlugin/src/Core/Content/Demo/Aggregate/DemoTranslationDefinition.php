<?php declare(strict_types=1);

namespace Example\Core\Content\Demo\Aggregate;

use Shopware\Core\Framework\DataAbstractionLayer\EntityTranslationDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Example\Core\Content\Demo\DemoDefinition;



class DemoTranslationDefinition extends EntityTranslationDefinition
{
    public const ENTITY_NAME = 'demo_translation';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getParentDefinitionClass(): string
    {
        return DemoDefinition::class;
    }

    public function defineFields(): FieldCollection
    {
        return new FieldCollection([
          
            (new StringField('name', 'name')),
            (new StringField('city', 'city')),
        ]);
    }


}