<?php

declare(strict_types=1);

namespace Zeobv\BundleProducts\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1727943683AddOptionalColumn extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1727943683;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('ALTER TABLE `zeobv_product_bundle_connection` ADD `optional` TINYINT DEFAULT 1 AFTER `modifiable`;');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
