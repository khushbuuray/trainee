<?php declare(strict_types=1);

namespace ICTECH_LimitedStockNotification\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1704697371CartWishlistProduct extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1704697371;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement("
              CREATE TABLE IF NOT EXISTS `ict_cart_wishlist_product` (
                `id` BINARY(16) NOT NULL,
                `cart_token` VARCHAR(255) NULL,
                `wishlist_id` VARCHAR(255) NULL,
                `customer_id` BINARY(16) NULL,
                `sales_channel_id` BINARY(16) NULL,
                `product_id` BINARY(16) NOT NULL,
                `mail_sent` TINYINT(1) NULL DEFAULT '0',
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
