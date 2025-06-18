<?php

declare(strict_types=1);

namespace ICTECH_ReminderEmailWishlist\Core\Content\Extension;

use ICTECH_ReminderEmailWishlist\Core\Content\ICTEmail\ReminderEmailCart\ICTEmailCartDefinition;
use ICTECH_ReminderEmailWishlist\Core\Content\ICTEmail\ReminderEmailRestock\ICTEmailRestockDefinition;
use ICTECH_ReminderEmailWishlist\Core\Content\ICTEmail\ReminderEmailWishlist\ICTEmailWishlistDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\System\Currency\CurrencyDefinition;

class CurrencyExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            new OneToManyAssociationField(
                'currencyIds',
                ICTEmailCartDefinition::class,
                'currency_id',
            )
        );
        $collection->add(
            new OneToManyAssociationField(
                'restockCurrencyIds',
                ICTEmailRestockDefinition::class,
                'currency_id',
            )
        );
        $collection->add(
            new OneToManyAssociationField(
                'wishlistCurrencyIds',
                IctEmailwishlistDefinition::class,
                'currency_id',
            )
        );
    }

    public function getDefinitionClass(): string
    {
        return CurrencyDefinition::class;
    }
}
