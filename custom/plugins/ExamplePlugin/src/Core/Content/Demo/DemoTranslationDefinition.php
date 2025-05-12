<?php declare(strict_types=1);

namespace Example\Core\Content\Demo;

use Shopware\Core\Framework\DataAbstractionLayer\EntityTranslationDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class DemoTranslationDefinition extends EntityTranslationDefinition
{
    public function getEntityName(): string
    {
        return 'demo_translation';
    }
    public function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new StringField('name', 'name')),
            (new StringField('city', 'city')),
        ]);
    }
}