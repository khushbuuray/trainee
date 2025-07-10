<?php

declare(strict_types=1);

namespace Zeobv\BundleProducts\Extension\Content\Product;

use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Extension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Zeobv\BundleProducts\Core\Content\Product\Aggregate\ProductBundleConnection\ProductBundleConnectionDefinition;

class BundleProductExtension extends EntityExtension
{
    public const BUNDLE_CONTENTS_EXTENSION_NAME = 'zeobvBundleContents';
    public const BUNDLE_CONNECTIONS_EXTENSION_NAME = 'zeobvBundleConnections';

    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            (new ManyToManyAssociationField(
                self::BUNDLE_CONTENTS_EXTENSION_NAME,
                ProductDefinition::class,
                ProductBundleConnectionDefinition::class,
                'bundle_product_id',
                'product_id',
            ))->addFlags(new Extension(), new ApiAware())
        );
        $collection->add(
            (new OneToManyAssociationField(
                self::BUNDLE_CONNECTIONS_EXTENSION_NAME,
                ProductBundleConnectionDefinition::class,
                'product_id',
            ))->addFlags(new Extension(), new ApiAware())
        );
    }

    public function getDefinitionClass(): string
    {
        return ProductDefinition::class;
    }
}
