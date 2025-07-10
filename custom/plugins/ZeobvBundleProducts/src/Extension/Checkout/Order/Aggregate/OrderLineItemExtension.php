<?php

declare(strict_types=1);

namespace Zeobv\BundleProducts\Extension\Checkout\Order\Aggregate;

use Shopware\Core\Checkout\Order\Aggregate\OrderLineItem\OrderLineItemDefinition;
use Shopware\Core\Checkout\Order\OrderDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ReferenceVersionField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class OrderLineItemExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $fieldFix = (new ReferenceVersionField(OrderDefinition::class, 'parent_version_id'))->addFlags(new ApiAware(), new Required());
        $collection->add($fieldFix);
    }

    public function getDefinitionClass(): string
    {
        return OrderLineItemDefinition::class;
    }
}
