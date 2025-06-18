<?php

declare(strict_types=1);

namespace ICTECH_ReminderEmailWishlist\Core\Content\ICTEmail\ReminderEmailRestock;

use Shopware\Core\Checkout\Customer\Aggregate\CustomerWishlist\CustomerWishlistDefinition;
use Shopware\Core\Checkout\Customer\CustomerDefinition;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ReferenceVersionField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\System\Currency\CurrencyDefinition;
use Shopware\Core\System\SalesChannel\SalesChannelDefinition;

class ICTEmailRestockDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'restock_email_reminder';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return ICTEmailRestockEntity::class;
    }

    public function getCollectionClass(): string
    {
        return ICTEmailRestockCollection::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection(array(

            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            (new FkField('product_id', 'productId', ProductDefinition::class)),
            (new ReferenceVersionField(ProductDefinition::class)),
            (new StringField('token', 'token')),
            (new FkField('customer_wishlist_id', 'customerWishlistId', CustomerWishlistDefinition::class)),
            (new FkField('customer_id', 'customerId', CustomerDefinition::class)),
            (new IntField('stock', 'stock')),
            (new FkField('currency_id', 'currencyId', CurrencyDefinition::class)),
            (new FkField('sales_channel_id', 'salesChannelId', SalesChannelDefinition::class))->addFlags(new Required()),

            new ManyToOneAssociationField(
                'product',
                'product_id',
                ProductDefinition::class,
                'id',
            ),

            new ManyToOneAssociationField(
                'customerWishlists',
                'customer_wishlist_id',
                CustomerWishlistDefinition::class,
                'id',
            ),

            new ManyToOneAssociationField(
                'customer',
                'customer_id',
                CustomerDefinition::class,
                'id',
                false
            ),

            new ManyToOneAssociationField(
                'currency',
                'currency_id',
                CurrencyDefinition::class,
                'id',
                false
            ),
            new ManyToOneAssociationField(
                'salesChannel',
                'sales_channel_id',
                SalesChannelDefinition::class,
                'id',
                false
            ),
        ));
    }
}
