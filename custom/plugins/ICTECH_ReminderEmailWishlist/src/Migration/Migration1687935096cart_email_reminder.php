<?php

declare(strict_types=1);

namespace ICTECH_ReminderEmailWishlist\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1687935096cart_email_reminder extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1687935096;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement("
            CREATE TABLE `cart_email_reminder` (
    `id` BINARY(16) NOT NULL,
    `token` VARCHAR(255) NOT NULL,
    `sales_channel_id` BINARY(16) NOT NULL,
    `customer_id` BINARY(16) NULL,
    `product_id` BINARY(16) NULL,
    `currency_id` BINARY(16) NULL,
    `product_version_id` BINARY(16) NULL,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NULL,
    PRIMARY KEY (`id`),
    KEY `fk.cart_email_reminder.sales_channel_id` (`sales_channel_id`),
    KEY `fk.cart_email_reminder.customer_id` (`customer_id`),
    KEY `fk.cart_email_reminder.product_id` (`product_id`,`product_version_id`),
    KEY `fk.cart_email_reminder.currency_id` (`currency_id`),
    CONSTRAINT `fk.cart_email_reminder.sales_channel_id` FOREIGN KEY (`sales_channel_id`) REFERENCES `sales_channel` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk.cart_email_reminder.customer_id` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk.cart_email_reminder.product_id` FOREIGN KEY (`product_id`,`product_version_id`) REFERENCES `product` (`id`,`version_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk.cart_email_reminder.currency_id` FOREIGN KEY (`currency_id`) REFERENCES `currency` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
