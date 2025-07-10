<?php

declare(strict_types=1);

namespace Zeobv\BundleProducts\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1725435252AddModifiableColumn extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1725435252;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('ALTER TABLE `zeobv_product_bundle_connection` ADD `modifiable` TINYINT DEFAULT 1 AFTER `quantity`;');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
