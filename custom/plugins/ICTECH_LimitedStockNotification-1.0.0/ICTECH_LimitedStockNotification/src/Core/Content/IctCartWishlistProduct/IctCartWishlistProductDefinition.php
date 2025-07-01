<?php

declare(strict_types=1);

namespace ICTECH_LimitedStockNotification\Core\Content\IctCartWishlistProduct;

use Shopware\Core\Checkout\Customer\CustomerDefinition;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\System\SalesChannel\SalesChannelDefinition;

class IctCartWishlistProductDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'ict_cart_wishlist_product';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return IctCartWishlistProductCollection::class;
    }

    public function getEntityClass(): string
    {
        return IctCartWishlistProductEntity::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            (new StringField('cart_token', 'cartToken')),
            (new StringField('wishlist_id', 'wishlistId')),

            (new FkField('customer_id', 'customerId', CustomerDefinition::class)),
            (new FkField('sales_channel_id', 'salesChannelId', SalesChannelDefinition::class)),
            (new FkField('product_id', 'productId', ProductDefinition::class))->addFlags(new Required()),
            (new BoolField('mail_sent', 'mailSent')),

            new OneToOneAssociationField('product', 'product_id', 'id', ProductDefinition::class, false),
            (new OneToOneAssociationField('customer', 'customer_id', 'id', CustomerDefinition::class, false)),
            (new OneToOneAssociationField('salesChannel', 'sales_channel_id', 'id', SalesChannelDefinition::class, false)),
        ]);
    }
}
