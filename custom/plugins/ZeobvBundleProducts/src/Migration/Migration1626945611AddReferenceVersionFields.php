<?php

declare(strict_types=1);

namespace Zeobv\BundleProducts\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1626945611AddReferenceVersionFields extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1626945611;
    }

    public function update(Connection $connection): void
    {
        try {
            $connection->executeStatement('
                ALTER TABLE `zeobv_product_bundle_connection` add `bundle_product_version_id` BINARY(16)  AFTER `bundle_product_id`;
                ALTER TABLE `zeobv_product_bundle_connection` add `product_version_id` BINARY(16) AFTER `product_id`;

                ALTER TABLE zeobv_product_bundle_connection DROP FOREIGN KEY `fk.zeobv_product_bundle_connection.bundle_product_id`;
                drop index `bundle_product_id` on zeobv_product_bundle_connection;
                ALTER TABLE `zeobv_product_bundle_connection` ADD CONSTRAINT `fk.zeobv_product_bundle_connection.bundle_product_id`
                        FOREIGN KEY (`bundle_product_id`,`bundle_product_version_id`) REFERENCES `product` (`id`,`version_id`) ON DELETE CASCADE ON UPDATE CASCADE;

                ALTER TABLE zeobv_product_bundle_connection DROP FOREIGN KEY `fk.zeobv_product_bundle_connection.product_id`;
                drop index `product_id` on zeobv_product_bundle_connection;
                ALTER TABLE `zeobv_product_bundle_connection` ADD CONSTRAINT `fk.zeobv_product_bundle_connection.product_id`
                        FOREIGN KEY (`product_id`,`bundle_product_version_id`) REFERENCES `product` (`id`,`version_id`) ON DELETE CASCADE ON UPDATE CASCADE;
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
