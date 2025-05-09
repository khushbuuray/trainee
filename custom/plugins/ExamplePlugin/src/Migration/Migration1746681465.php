<?php declare(strict_types=1);

namespace Example\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 */
class Migration1746681465 extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1746681465;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
    CREATE TABLE IF NOT EXISTS `demo` (
        `id` BINARY(16) NOT NULL,
        `name` VARCHAR(255) COLLATE utf8mb4_unicode_ci,
        `description` VARCHAR(255) COLLATE utf8mb4_unicode_ci,
        `active` TINYINT(1),
        `created_at` DATETIME(3) NOT NULL,
        `updated_at` DATETIME(3),
        `country_id` BINARY(16) NULL,
        PRIMARY KEY (`id`),
        KEY `fk.demo.country_id` (`country_id`),
        CONSTRAINT `fk.demo.country_id` FOREIGN KEY (`country_id`) 
            REFERENCES `country` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
    )
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_unicode_ci;
    SQL;
    
        $connection->executeStatement($sql);        
    }
}
