<?php declare(strict_types=1);

namespace zenit\PlatformGravity\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1689337188RenameCMSType extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1689337188;
    }

    public function update(Connection $connection): void
    {
        $this->renameColumnType($connection, 'cms_block', 'zenit-features', 'zen-features');
        $this->renameColumnType($connection, 'cms_slot', 'zenit-features', 'zen-features');
        $this->renameColumnType($connection, 'cms_block', 'zenit-breadcrumb', 'zen-breadcrumb');
        $this->renameColumnType($connection, 'cms_slot', 'zenit-breadcrumb', 'zen-breadcrumb');
    }

    private function renameColumnType(Connection $connection, string $table, string $oldType, string $newType): void
    {
        $connection->executeStatement("
            UPDATE `$table`
            SET `type` = REPLACE(`type`, '$oldType', '$newType')
            WHERE `type` = '$oldType'
        ");
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
