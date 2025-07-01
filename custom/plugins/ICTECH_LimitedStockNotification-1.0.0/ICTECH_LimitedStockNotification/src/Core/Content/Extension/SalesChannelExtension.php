<?php

declare(strict_types=1);

namespace ICTECH_LimitedStockNotification\Core\Content\Extension;

use ICTECH_LimitedStockNotification\Core\Content\IctCartWishlist\IctCartWishlistDefinition;
use ICTECH_LimitedStockNotification\Core\Content\IctCartWishlistProduct\IctCartWishlistProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\System\SalesChannel\SalesChannelDefinition;

class SalesChannelExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            (new OneToOneAssociationField('salesChannel', 'id', 'sales_channel_id', IctCartWishlistDefinition::class))
        );
        $collection->add(
            (new OneToOneAssociationField('salesChannel', 'id', 'sales_channel_id', IctCartWishlistProductDefinition::class))
        );
    }

    public function getDefinitionClass(): string
    {
        return SalesChannelDefinition::class;
    }
}
