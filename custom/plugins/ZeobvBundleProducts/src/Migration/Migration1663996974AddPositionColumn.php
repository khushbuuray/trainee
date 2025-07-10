<?php

declare(strict_types=1);

namespace Zeobv\BundleProducts\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1663996974AddPositionColumn extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1663996974;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('ALTER TABLE `zeobv_product_bundle_connection` ADD `position` TINYINT DEFAULT 0 AFTER `quantity`;');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
