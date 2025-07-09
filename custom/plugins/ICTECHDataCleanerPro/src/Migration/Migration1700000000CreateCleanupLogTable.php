<?php declare(strict_types=1);

namespace ICTECHDataCleanerPro\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1700000000CreateCleanupLogTable extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1700000000;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `ict_data_cleanup_log` (
    `id` BINARY(16) NOT NULL,
    `run_at` DATETIME(3) NOT NULL,
    `trigger` VARCHAR(50) NOT NULL,
    `mode` VARCHAR(20) NOT NULL,
    `config_snapshot` JSON NULL,
    `results` JSON NULL,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NULL,
    PRIMARY KEY (`id`),
    KEY `idx.run_at` (`run_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;

        $connection->executeStatement($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
        // No destructive changes
    }
}
