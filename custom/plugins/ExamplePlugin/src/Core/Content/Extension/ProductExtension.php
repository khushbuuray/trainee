<?php declare(strict_types=1);

namespace Example\Core\Content\Extension;

use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Content\Product\ProductDefinition;
use Example\Core\Content\Demo\DemoDefinition;

class ProductExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            new OneToManyAssociationField(
                'demos',                // Extension name
                DemoDefinition::class, // Related entity
                'product_id'           // Foreign key in DemoDefinition
            )
        );
    }

    public function getDefinitionClass(): string
    {
        return ProductDefinition::class;
    }
}
