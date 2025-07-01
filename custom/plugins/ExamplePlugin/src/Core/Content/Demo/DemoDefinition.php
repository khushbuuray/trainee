<?php declare(strict_types=1);

namespace Example\Core\Content\Demo;

use Example\Core\Content\Demo\Aggregate\DemoTranslationDefinition;
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
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslatedField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ReferenceVersionField;




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
            new TranslatedField('name'),
            new TranslatedField('city'),
            new TranslationsAssociationField(DemoTranslationDefinition::class, 'demo_id'),
            (new BoolField('active', 'active')),


            (new FkField('country_id', 'countryId', CountryDefinition::class)),
            new ManyToOneAssociationField('country', 'country_id', CountryDefinition::class, 'id', false), // false means not autolaod unless requested


            (new FkField('state_id', 'stateId', CountryStateDefinition::class)),
            new ManyToOneAssociationField('state', 'state_id', CountryStateDefinition::class, 'id', false),

            new FkField('product_id', 'productId', ProductDefinition::class),
            new ReferenceVersionField(ProductDefinition::class), // Foreign key for the product version
            new ManyToOneAssociationField('product', 'product_id', ProductDefinition::class, 'id', false),

            (new FkField('media_id', 'mediaId', MediaDefinition::class)),
            new ManyToOneAssociationField('media', 'media_id', MediaDefinition::class, 'id', false),

        ]);
    }
}
