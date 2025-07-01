<?php

declare(strict_types=1);

namespace ICTECH_ReminderEmailWishlist\Core\Content\Extension;

use ICTECH_ReminderEmailWishlist\Core\Content\ICTEmail\ReminderEmailRestock\ICTEmailRestockDefinition;
use ICTECH_ReminderEmailWishlist\Core\Content\ICTEmail\ReminderEmailWishlist\ICTEmailWishlistDefinition;
use Shopware\Core\Checkout\Customer\Aggregate\CustomerWishlist\CustomerWishlistDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class CustomerWishlistExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            new OneToManyAssociationField(
                'customerWishlistsId',
                ICTEmailWishlistDefinition::class,
                'customer_wishlist_id',
                'id'
            )
        );

        $collection->add(
            new OneToManyAssociationField(
                'customerWishlistsRestockId',
                ICTEmailRestockDefinition::class,
                'customer_wishlist_id',
                'id'
            )
        );
    }

    public function getDefinitionClass(): string
    {
        return CustomerWishlistDefinition::class;
    }
}
