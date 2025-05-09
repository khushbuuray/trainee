<?php declare(strict_types=1);

namespace SwagShopFinder\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1746708148CreateSwagShopFinderTable extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1746708148;
    }

    public function update(Connection $connection): void
{
    $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `swag_shop_finder` (
    `id` BINARY(16) NOT NULL,
    `name` VARCHAR(255) COLLATE utf8mb4_unicode_ci,
    `description` VARCHAR(255) COLLATE utf8mb4_unicode_ci,
    `active` TINYINT(1) NOT NULL,
    `street` VARCHAR(255) COLLATE utf8mb4_unicode_ci,
    `postal_code` VARCHAR(255) COLLATE utf8mb4_unicode_ci,
    `city` VARCHAR(255) COLLATE utf8mb4_unicode_ci,
    `url` VARCHAR(255) COLLATE utf8mb4_unicode_ci,
    `telephone` VARCHAR(255) COLLATE utf8mb4_unicode_ci,
    `open_times` VARCHAR(255) COLLATE utf8mb4_unicode_ci,
    `country_id` BINARY(16),
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3),
    PRIMARY KEY (`id`),
    CONSTRAINT `fk.swag_shop_finder.country_id` FOREIGN KEY (`country_id`)
    REFERENCES `country` (`id`) ON DELETE SET NULL ON UPDATE CASCADE

)
ENGINE = InnoDB
DEFAULT CHARSET = utf8mb4
COLLATE = utf8mb4_unicode_ci;
SQL;

    $connection->executeStatement($sql);
}


    public function updateDestructive(Connection $connection): void
    {
    }
}
