<?php declare(strict_types=1);

namespace IctDataCleanerPro\Service\Cleanup;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Context;

class LogCleanupHandler implements CleanupHandlerInterface
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function cleanup(array $config, bool $dryRun, Context $context): array
    {
        $results = [
            'name' => $this->getName(),
            'items' => []
        ];
        // Clean system logs
        if (isset($config['systemLogCleanup.months'])) {
            $logResults = $this->cleanupSystemLogs(
                (int) $config['systemLogCleanup.months'],
                $dryRun,
                $context
            );
            $results['items']['system_logs'] = $logResults;
        }

        // Clean orphaned custom field sets
        if ($config['systemLogCleanup.orphaned'] ?? false) {
            $customFieldResults = $this->cleanupOrphanedCustomFieldSets($dryRun, $context);
            $results['items']['orphaned_custom_field_sets'] = $customFieldResults;
        }

        return $results;
    }

    private function cleanupSystemLogs(int $months, bool $dryRun, Context $context): array
    {
        $date = new \DateTime();
        $date->modify("-{$months} months");

        $tables = [
            'log_entry',
            'dead_message',
            'messenger_messages'
        ];

        $totalCount = 0;
        $samples = [];

        foreach ($tables as $table) {
            // Check if table exists
            $tableExists = $this->connection->fetchOne(
                "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?",
                [$table]
            );

            if (!$tableExists) {
                continue;
            }

            $countSql = "SELECT COUNT(*) as count FROM `{$table}` WHERE created_at < :date";
            
            try {
                $countResult = $this->connection->fetchAssociative($countSql, [
                    'date' => $date->format('Y-m-d H:i:s')
                ]);
                
                $count = (int) $countResult['count'];
                $totalCount += $count;

                if ($count > 0) {
                    $samples[] = [
                        'table' => $table,
                        'count' => $count
                    ];

                    if (!$dryRun) {
                        $deleteSql = "DELETE FROM `{$table}` WHERE created_at < :date";
                        $this->connection->executeStatement($deleteSql, [
                            'date' => $date->format('Y-m-d H:i:s')
                        ]);
                    }
                }
            } catch (\Exception $e) {
                // Skip tables that don't have created_at column
                continue;
            }
        }

        return [
            'count' => $totalCount,
            'sample' => array_slice($samples, 0, 5)
        ];
    }

    private function cleanupOrphanedCustomFieldSets(bool $dryRun, Context $context): array
    {
        // This is a complex query and might need adjustment based on your specific custom field setup
        $sql = <<<SQL
SELECT cfs.id, cfs.name
FROM custom_field_set cfs
LEFT JOIN custom_field_set_relation cfsr ON cfs.id = cfsr.set_id
WHERE cfsr.set_id IS NULL
LIMIT 1000
SQL;

        $customFieldSets = $this->connection->fetchAllAssociative($sql);

        if (!$dryRun && !empty($customFieldSets)) {
            $ids = array_map(function ($set) {
                return ['id' => $set['id']];
            }, $customFieldSets);
            
            // Assuming you have a custom_field_set.repository service
            // If not, you'll need to use direct DBAL delete
            // $this->customFieldSetRepository->delete($ids, $context);
            
            // Direct DBAL delete example:
            $idBytes = array_map(fn($id) => \Shopware\Core\Framework\Uuid\Uuid::fromHexToBytes($id['id']), $customFieldSets);
            if (!empty($idBytes)) {
                $this->connection->executeStatement(
                    'DELETE FROM custom_field_set WHERE id IN (:ids)',
                    ['ids' => $idBytes],
                    ['ids' => Connection::PARAM_STR_ARRAY]
                );
            }
        }

        return [
            'count' => count($customFieldSets),
            'sample' => array_slice($customFieldSets, 0, 5)
        ];
    }

    public function getName(): string
    {
        return 'System Log & Technical Data Cleanup';
    }
}
