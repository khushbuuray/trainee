<?php declare(strict_types=1);

namespace Example\Core\Content\Demo;


use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\System\Country\CountryDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslationsAssociationField;
use Shopware\Core\System\Country\Aggregate\CountryState\CountryStateDefinition;


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
            (new BoolField('active', 'active')),

            (new TranslationsAssociationField(DemoDefinition::class, 'demo_id')),

            (new FkField('country_id', 'countryId', CountryDefinition::class)),
            new ManyToOneAssociationField('country', 'country_id', CountryDefinition::class, 'id', false),

            (new FkField('state_id', 'stateId', CountryStateDefinition::class)),
            new ManyToOneAssociationField('state', 'state_id', CountryStateDefinition::class, 'id', false),

            (new FkField('image_id', 'imageId', MediaDefinition::class)),
            new OneToOneAssociationField('image', 'image_id', MediaDefinition::class, 'id', false),

            new FkField('product_id', 'productId', ProductDefinition::class),
            new ManyToOneAssociationField('product', 'product_id', ProductDefinition::class, 'id', false),

        ]);
    }
}
