<?php

declare(strict_types=1);

namespace ICTECH_LimitedStockNotification\Core\Content\Extension;

use ICTECH_LimitedStockNotification\Core\Content\IctCartWishlist\IctCartWishlistDefinition;

use ICTECH_LimitedStockNotification\Core\Content\IctCartWishlistProduct\IctCartWishlistProductDefinition;
use Shopware\Core\Checkout\Customer\CustomerDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class CustomerExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            (new OneToOneAssociationField('customer', 'id', 'customer_id', IctCartWishlistDefinition::class))
        );
        $collection->add(
            (new OneToOneAssociationField('customer', 'id', 'customer_id', IctCartWishlistProductDefinition::class))
        );
    }

    public function getDefinitionClass(): string
    {
        return CustomerDefinition::class;
    }
}
