<?php

declare(strict_types=1);

namespace Zeobv\BundleProducts\Core\Content\Product\Aggregate\ProductBundleConnection;

use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ReferenceVersionField;

class ProductBundleConnectionDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'zeobv_product_bundle_connection';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return ProductBundleConnectionEntity::class;
    }

    public function getCollectionClass(): string
    {
        return ProductBundleConnectionCollection::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),

            (new FkField('bundle_product_id', 'bundleProductId', ProductDefinition::class))->addFlags(new Required()),
            (new ReferenceVersionField(ProductDefinition::class, 'bundle_product_version_id'))->addFlags(new Required()),
            (new ManyToOneAssociationField('bundleProduct', 'bundle_product_id', ProductDefinition::class)),

            (new FkField('product_id', 'productId', ProductDefinition::class))->addFlags(new Required()),
            (new ReferenceVersionField(ProductDefinition::class, 'product_version_id'))->addFlags(new Required()),
            (new ManyToOneAssociationField('product', 'product_id', ProductDefinition::class)),

            new IntField('quantity', 'quantity'),
            new IntField('position', 'position'),
            new BoolField('modifiable', 'modifiable'),
            new BoolField('optional', 'optional'),
            new StringField('comment', 'comment'),
        ]);
    }
}
