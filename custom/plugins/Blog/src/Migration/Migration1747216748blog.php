<?php declare(strict_types=1);

namespace Blog\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 */
class Migration1747216748blog extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1747216748;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL

        CREATE TABLE `blog` (
    `id` BINARY(16) NOT NULL,
    `release_date` DATE NOT NULL,
    `active` TINYINT(1) NULL DEFAULT '0',
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `blog_category` (
    `id` BINARY(16) NOT NULL,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `blog_category_mapping` (
    `blog_id` BINARY(16) NOT NULL,
    `blog_category_id` BINARY(16) NOT NULL,
    PRIMARY KEY (`blog_id`,`blog_category_id`),
    KEY `fk.blog_category_mapping.blog_id` (`blog_id`),
    KEY `fk.blog_category_mapping.blog_category_id` (`blog_category_id`),
    CONSTRAINT `fk.blog_category_mapping.blog_id` FOREIGN KEY (`blog_id`) REFERENCES `blog` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk.blog_category_mapping.blog_category_id` FOREIGN KEY (`blog_category_id`) REFERENCES `blog_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `blog_category_translation` (
    `name` VARCHAR(255) NOT NULL,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NULL,
    `blog_category_id` BINARY(16) NOT NULL,
    `language_id` BINARY(16) NOT NULL,
    PRIMARY KEY (`blog_category_id`,`language_id`),
    KEY `fk.blog_category_translation.blog_category_id` (`blog_category_id`),
    KEY `fk.blog_category_translation.language_id` (`language_id`),
    CONSTRAINT `fk.blog_category_translation.blog_category_id` FOREIGN KEY (`blog_category_id`) REFERENCES `blog_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk.blog_category_translation.language_id` FOREIGN KEY (`language_id`) REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `blog_translation` (
    `name` VARCHAR(255) NOT NULL,
    `description` LONGTEXT NOT NULL,
    `author` VARCHAR(255) NOT NULL,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NULL,
    `blog_id` BINARY(16) NOT NULL,
    `language_id` BINARY(16) NOT NULL,
    PRIMARY KEY (`blog_id`,`language_id`),
    KEY `fk.blog_translation.blog_id` (`blog_id`),
    KEY `fk.blog_translation.language_id` (`language_id`),
    CONSTRAINT `fk.blog_translation.blog_id` FOREIGN KEY (`blog_id`) REFERENCES `blog` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk.blog_translation.language_id` FOREIGN KEY (`language_id`) REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    CREATE TABLE `blog_product_mapping` (
    `blog_id` BINARY(16) NOT NULL,
    `product_id` BINARY(16) NOT NULL,
    `product_version_id` BINARY(16) NULL,
    PRIMARY KEY (`blog_id`,`product_id`),
    KEY `fk.blog_product_mapping.blog_id` (`blog_id`),
    KEY `fk.blog_product_mapping.product_id` (`product_id`,`product_version_id`),
    CONSTRAINT `fk.blog_product_mapping.blog_id` FOREIGN KEY (`blog_id`) REFERENCES `blog` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk.blog_product_mapping.product_id` FOREIGN KEY (`product_id`,`product_version_id`) REFERENCES `product` (`id`,`version_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;
        $connection->executeStatement($sql);    
          
    }

}
