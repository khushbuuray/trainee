<?php

declare(strict_types=1);

namespace ICTECH_ReminderEmailWishlist\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1692703377restock_email_reminder extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1692703377;
    }

    public function update(Connection $connection): void
    {
        $query = <<<SQL
                    CREATE TABLE `restock_email_reminder` (
                    `id` BINARY(16) NOT NULL,
                    `product_id` BINARY(16) NULL,
                    `product_version_id` BINARY(16) NULL,
                    `token` VARCHAR(255) NULL,
                    `customer_wishlist_id` BINARY(16) NULL,
                    `customer_id` BINARY(16) NULL,
                    `stock` INT(11) NULL,
                    `currency_id` BINARY(16) NULL,
                    `sales_channel_id` BINARY(16) NOT NULL,
                    `created_at` DATETIME(3) NOT NULL,
                    `updated_at` DATETIME(3) NULL,
                    PRIMARY KEY (`id`),
                    KEY `fk.restock_email_reminder.product_id` (`product_id`,`product_version_id`),
                    KEY `fk.restock_email_reminder.customer_wishlist_id` (`customer_wishlist_id`),
                    KEY `fk.restock_email_reminder.customer_id` (`customer_id`),
                    KEY `fk.restock_email_reminder.currency_id` (`currency_id`),
                    KEY `fk.restock_email_reminder.sales_channel_id` (`sales_channel_id`),
                    CONSTRAINT `fk.restock_email_reminder.product_id` FOREIGN KEY (`product_id`,`product_version_id`) REFERENCES `product` (`id`,`version_id`) ON DELETE CASCADE ON UPDATE CASCADE,
                    CONSTRAINT `fk.restock_email_reminder.customer_wishlist_id` FOREIGN KEY (`customer_wishlist_id`) REFERENCES `customer_wishlist` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                    CONSTRAINT `fk.restock_email_reminder.customer_id` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                    CONSTRAINT `fk.restock_email_reminder.currency_id` FOREIGN KEY (`currency_id`) REFERENCES `currency` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                    CONSTRAINT `fk.restock_email_reminder.sales_channel_id` FOREIGN KEY (`sales_channel_id`) REFERENCES `sales_channel` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                SQL;

        $connection->executeStatement($query);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
