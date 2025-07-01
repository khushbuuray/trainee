<?php

declare(strict_types=1);

namespace ICTECH_LimitedStockNotification\Core\Content\IctCartWishlist;

use Shopware\Core\Checkout\Customer\CustomerDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CreatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\DateTimeField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\JsonField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\UpdatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\System\SalesChannel\SalesChannelDefinition;

class IctCartWishlistDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'ict_cart_wishlist';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return IctCartWishlistCollection::class;
    }

    public function getEntityClass(): string
    {
        return IctCartWishlistEntity::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            (new StringField('cart_token', 'cartToken')),
            (new StringField('wishlist_id', 'wishlistId')),
            (new StringField('email', 'email')),
            (new JsonField('line_items', 'lineItems')),
            (new JsonField('line_items_wishlist', 'lineItemsWishlist')),

            (new FkField('customer_id', 'customerId', CustomerDefinition::class)),
            (new FkField('sales_channel_id', 'salesChannelId', SalesChannelDefinition::class)),
            (new IntField('schedule_index', 'scheduleIndex')),
            (new DateTimeField('last_mail_send_at', 'lastMailSendAt')),
            (new BoolField('mail_sent', 'mailSent')),
            (new BoolField('is_recovered', 'isRecovered')),
            (new CreatedAtField()),
            (new UpdatedAtField()),

            (new OneToOneAssociationField('customer', 'customer_id', 'id', CustomerDefinition::class, false)),
            (new OneToOneAssociationField('salesChannel', 'sales_channel_id', 'id', SalesChannelDefinition::class, false)),
        ]);
    }
}
