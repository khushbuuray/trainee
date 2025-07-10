<?php

declare(strict_types=1);

namespace Zeobv\BundleProducts\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;
use Zeobv\BundleProducts\Core\Content\Product\Aggregate\ProductBundleConnection\ProductBundleConnectionDefinition;

class Migration1598965635ProductBundleConnection extends MigrationStep
{
    public const PRODUCT_BUNDLE_PRODUCT_SHOW_ON_STOREFRONT_COLUMN = 'zeobv_bundle_products_show_on_storefront';

    public function getCreationTimestamp(): int
    {
        return 1598965635;
    }

    public function update(Connection $connection): void
    {
        $tableName = ProductBundleConnectionDefinition::ENTITY_NAME;

        $connection->executeStatement("
            CREATE TABLE IF NOT EXISTS `{$tableName}` (
                `id` BINARY(16) NOT NULL,
                `bundle_product_id` BINARY(16) NOT NULL,
                `product_id` BINARY(16) NOT NULL,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`id`),
                UNIQUE (`bundle_product_id`, `product_id`),
                CONSTRAINT `fk.{$tableName}.bundle_product_id`
                    FOREIGN KEY (`bundle_product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk.{$tableName}.product_id`
                    FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
