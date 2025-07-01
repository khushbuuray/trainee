<?php declare(strict_types=1);

namespace Example\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 */
class Migration1747054836DemoExample extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1747054836;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
     CREATE TABLE IF NOT EXISTS `demo`  (
    `id` BINARY(16) NOT NULL,
    `active` TINYINT(1) NULL DEFAULT 0,
    `country_id` BINARY(16) NULL,
    `state_id` BINARY(16) NULL,
    `product_id` BINARY(16) NULL,
    `product_version_id` BINARY(16) NULL,
    `media_id` BINARY(16) NULL,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NULL,
    PRIMARY KEY (`id`),
    KEY `fk.demo.country_id` (`country_id`),
    KEY `fk.demo.state_id` (`state_id`),
    KEY `fk.demo.product_id` (`product_id`,`product_version_id`),
    KEY `fk.demo.media_id` (`media_id`),
    CONSTRAINT `fk.demo.country_id` FOREIGN KEY (`country_id`) REFERENCES `country` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk.demo.state_id` FOREIGN KEY (`state_id`) REFERENCES `country_state` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk.demo.product_id` FOREIGN KEY (`product_id`,`product_version_id`) REFERENCES `product` (`id`,`version_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk.demo.media_id` FOREIGN KEY (`media_id`) REFERENCES `media` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `demo_translation` ( 
    `name` VARCHAR(255) NULL,
    `city` VARCHAR(255) NULL,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NULL,
    `demo_id` BINARY(16) NOT NULL,
    `language_id` BINARY(16) NOT NULL,
    PRIMARY KEY (`demo_id`,`language_id`),
    KEY `fk.demo_translation.demo_id` (`demo_id`),
    KEY `fk.demo_translation.language_id` (`language_id`),
    CONSTRAINT `fk.demo_translation.demo_id` FOREIGN KEY (`demo_id`) REFERENCES `demo` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;

        $connection->executeStatement($sql);
    }
}
