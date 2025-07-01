<?php

declare(strict_types=1);

namespace ICTECH_ReminderEmailWishlist\Core\Content\Extension;

use ICTECH_ReminderEmailWishlist\Core\Content\ICTEmail\ReminderEmailCart\ICTEmailCartDefinition;
use ICTECH_ReminderEmailWishlist\Core\Content\ICTEmail\ReminderEmailRestock\ICTEmailRestockDefinition;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class ProductExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            new OneToManyAssociationField(
                'products',
                ICTEmailRestockDefinition::class,
                'product_id',
                'id'
            )
        );

        $collection->add(
            new OneToManyAssociationField(
                'cartProducts',
                ICTEmailCartDefinition::class,
                'product_id',
                'id'
            )
        );
    }
    public function getDefinitionClass(): string
    {
        return ProductDefinition::class;
    }
}
