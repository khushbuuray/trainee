<?php declare(strict_types=1);

namespace Example\Core\Content\Demo;

use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\DateTimeField;
use Shopware\Core\System\Country\CountryDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;


class DemoDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'demo';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return DemoEntity::class;
    }

    public function getCollectionClass(): string
    {
        return DemoCollection::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),
            (new StringField('name', 'name')),
            (new StringField('description', 'description')),
            (new BoolField('active', 'active')),
            new DateTimeField('created_at', 'createdAt'),
            new DateTimeField('updated_at', 'updatedAt'),
            (new FkField('country_id', 'countryId', CountryDefinition::class)),
            new ManyToOneAssociationField('country', 'country_id', CountryDefinition::class, 'id', false),

        ]);
    }
}
