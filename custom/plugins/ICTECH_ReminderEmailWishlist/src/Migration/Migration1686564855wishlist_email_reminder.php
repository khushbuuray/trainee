<?php

declare(strict_types=1);

namespace ICTECH_ReminderEmailWishlist\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1686564855wishlist_email_reminder extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1686564855;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement("CREATE TABLE `wishlist_email_reminder` (
        `id` BINARY(16) NOT NULL,
        `customer_wishlist_id` BINARY(16) NOT NULL,
        `currency_id` BINARY(16) NOT NULL,
        `sales_channel_id` BINARY(16) NOT NULL,
        `created_at` DATETIME(3) NOT NULL,
        `updated_at` DATETIME(3) NULL,
        PRIMARY KEY (`id`),
        KEY `fk.wishlist_email_reminder.customer_wishlist_id` (`customer_wishlist_id`),
        KEY `fk.wishlist_email_reminder.currency_id` (`currency_id`),
        KEY `fk.wishlist_email_reminder.sales_channel_id` (`sales_channel_id`),
        CONSTRAINT `fk.wishlist_email_reminder.customer_wishlist_id` FOREIGN KEY (`customer_wishlist_id`) REFERENCES `customer_wishlist` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
        CONSTRAINT `fk.wishlist_email_reminder.currency_id` FOREIGN KEY (`currency_id`) REFERENCES `currency` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
        CONSTRAINT `fk.wishlist_email_reminder.sales_channel_id` FOREIGN KEY (`sales_channel_id`) REFERENCES `sales_channel` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
