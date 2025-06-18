<?php

declare(strict_types=1);

namespace ICTECH_ReminderEmailWishlist\Core\Content\Extension;

use ICTECH_ReminderEmailWishlist\Core\Content\ICTEmail\ReminderEmailCart\ICTEmailCartDefinition;
use ICTECH_ReminderEmailWishlist\Core\Content\ICTEmail\ReminderEmailRestock\ICTEmailRestockDefinition;
use ICTECH_ReminderEmailWishlist\Core\Content\ICTEmail\ReminderEmailWishlist\ICTEmailWishlistDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\System\SalesChannel\SalesChannelDefinition;

class SalesChannelExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            new OneToManyAssociationField(
                'salesChannelsCartId',
                ICTEmailCartDefinition::class,
                'sales_channel_id',
                'id'
            )
        );

        $collection->add(
            new OneToManyAssociationField(
                'salesChannelsWishlistId',
                ICTEmailWishlistDefinition::class,
                'sales_channel_id',
                'id'
            )
        );

        $collection->add(
            new OneToManyAssociationField(
                'salesChannelsRestockId',
                ICTEmailRestockDefinition::class,
                'sales_channel_id',
                'id'
            )
        );
    }
    public function getDefinitionClass(): string
    {
        return SalesChannelDefinition::class;
    }
}
