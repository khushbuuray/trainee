<?php declare(strict_types=1);

namespace ICTECH_LimitedStockNotification\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1704115802CartWishlist extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1704115802;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement("CREATE TABLE IF NOT EXISTS `ict_cart_wishlist` (
                `id` BINARY(16) NOT NULL,
                `cart_token` VARCHAR(255) NULL,
                `wishlist_id` VARCHAR(255) NULL,
                `email` VARCHAR(255) NULL,
                `line_items` JSON NULL,
                `line_items_wishlist` JSON NULL,
                `customer_id` BINARY(16) NULL,
                `sales_channel_id` BINARY(16) NULL,
                `schedule_index` INT(11) NULL,
                `last_mail_send_at` DATETIME(3) NULL,
                `mail_sent` TINYINT(1) NULL DEFAULT '0',
                `is_recovered` TINYINT(1) NULL DEFAULT '0',
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`id`),
                CONSTRAINT `json.ict_cart_wishlist.line_items` CHECK (JSON_VALID(`line_items`)),
                CONSTRAINT `json.ict_cart_wishlist.line_items_wishlist` CHECK (JSON_VALID(`line_items_wishlist`))
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
