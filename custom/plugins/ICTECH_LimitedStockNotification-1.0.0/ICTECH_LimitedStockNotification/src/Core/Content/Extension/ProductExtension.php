<?php declare(strict_types=1);

namespace ICTECH_LimitedStockNotification\Core\Content\Extension;

use ICTECH_LimitedStockNotification\Core\Content\IctCartWishlistProduct\IctCartWishlistProductDefinition;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class ProductExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            new OneToOneAssociationField('product', 'id', 'product_id', IctCartWishlistProductDefinition::class)
        );
    }

    public function getDefinitionClass(): string
    {
        return ProductDefinition::class;
    }
}
