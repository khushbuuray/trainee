<?php

declare(strict_types=1);

namespace Zeobv\BundleProducts\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1606140637ExtraBundleProductData extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1606140637;
    }

    public function update(Connection $connection): void
    {
        try {
            $connection->executeStatement('
                ALTER TABLE `zeobv_product_bundle_connection` add `quantity` INTEGER DEFAULT 1 AFTER `product_id`;
                ALTER TABLE `zeobv_product_bundle_connection` add `comment` VARCHAR(255) NULL AFTER `quantity`;
                ALTER TABLE zeobv_product_bundle_connection DROP FOREIGN KEY `fk.zeobv_product_bundle_connection.bundle_product_id`;
                drop index `bundle_product_id` on zeobv_product_bundle_connection;
                ALTER TABLE `zeobv_product_bundle_connection` ADD CONSTRAINT `fk.zeobv_product_bundle_connection.bundle_product_id`
                        FOREIGN KEY (`bundle_product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
            ');
        } catch (\Throwable $e) {
            # ignore
        }
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
