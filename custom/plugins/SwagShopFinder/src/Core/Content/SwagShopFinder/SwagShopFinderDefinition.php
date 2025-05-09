<?php declare(strict_types=1);

namespace SwagShopFinder\Core\Content\SwagShopFinder;

use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\DateTimeField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TextField;
use Shopware\Core\System\Country\CountryDefinition;

class SwagShopFinderDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'swag_shop_finder';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return SwagShopFinderEntity::class;
    }

    public function getCollectionClass(): string
    {
        return SwagShopFinderCollection::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),
            (new StringField('name', 'name')),
            (new StringField('description', 'description')),
            (new BoolField('active', 'active')),
            (new StringField('street', 'street')),
            (new StringField('postal_code', 'postal_code')),
            (new StringField('city', 'city')),
            (new StringField('url', 'url')),
            (new StringField('telephone', 'telephone')),
            (new StringField('open_times', 'open_times')),
            (new FkField('country_id', 'countryId',CountryDefinition::class)),
            new ManyToOneAssociationField('country', 'country_id', CountryDefinition::class, 'id', false),
            new DateTimeField('created_at', 'created_at'),
            new DateTimeField('updated_at', 'updated_at'),
        ]);
    }
}
