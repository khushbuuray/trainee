<?php

declare(strict_types=1);

namespace ICTECH_ReminderEmailWishlist\Core\Content\Extension;

use ICTECH_ReminderEmailWishlist\Core\Content\ICTEmail\ReminderEmailCart\ICTEmailCartDefinition;
use ICTECH_ReminderEmailWishlist\Core\Content\ICTEmail\ReminderEmailRestock\ICTEmailRestockDefinition;
use Shopware\Core\Checkout\Customer\CustomerDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class CustomerExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            new OneToManyAssociationField(
                'customers',
                ICTEmailCartDefinition::class,
                'customer_id',
                'id'
            ),
        );
        $collection->add(
            new OneToManyAssociationField(
                'customerIds',
                ICTEmailRestockDefinition::class,
                'customer_id',
                'id'
            ),
        );
    }
    public function getDefinitionClass(): string
    {
        return CustomerDefinition::class;
    }
}
