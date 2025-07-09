<?php

declare(strict_types=1);

namespace ICTECHDataCleanerPro\Service\Cleanup;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Uuid\Uuid;

class LogCleanupHandler implements CleanupHandlerInterface
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param array<string, mixed> $config
     * @return array{name: string, items: array<string, array{count: int, sample: list<array<string, mixed>>}>}
     */
    public function cleanup(array $config, bool $dryRun, Context $context): array
    {
        $results = [
            'name' => $this->getName(),
            'items' => [],
        ];

        if (
            isset($config['systemLogCleanup.months']) &&
            is_numeric($config['systemLogCleanup.months'])
        ) {
            $months = (int) $config['systemLogCleanup.months'];
            $results['items']['system_logs'] = $this->cleanupSystemLogs($months, $dryRun, $context);
        }

        if (!empty($config['systemLogCleanup.orphaned'])) {
            $results['items']['orphaned_custom_field_sets'] = $this->cleanupOrphanedCustomFieldSets($dryRun, $context);
        }

        return $results;
    }

    /**
     * @return array{count: int, sample: list<array{table: string, count: int}>}
     */
    private function cleanupSystemLogs(int $months, bool $dryRun, Context $context): array
    {
        $date = new \DateTimeImmutable("-{$months} months");

        $tables = ['log_entry', 'dead_message', 'messenger_messages'];
        $totalCount = 0;

        /** @var list<array{table: string, count: int}> $samples */
        $samples = [];

        foreach ($tables as $table) {
            $tableExistsRaw = $this->connection->fetchOne(
                "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?",
                [$table]
            );
            $tableExists = is_numeric($tableExistsRaw) ? (int) $tableExistsRaw : 0;

            if ($tableExists === 0) {
                continue;
            }

            try {
                $countResult = $this->connection->fetchAssociative(
                    "SELECT COUNT(*) as count FROM `{$table}` WHERE created_at < :date",
                    ['date' => $date->format('Y-m-d H:i:s')]
                );

                $count = isset($countResult['count']) && is_numeric($countResult['count'])
                    ? (int) $countResult['count']
                    : 0;

                $totalCount += $count;

                if ($count > 0) {
                    $samples[] = ['table' => $table, 'count' => $count];

                    if (!$dryRun) {
                        $this->connection->executeStatement(
                            "DELETE FROM `{$table}` WHERE created_at < :date",
                            ['date' => $date->format('Y-m-d H:i:s')]
                        );
                    }
                }
            } catch (\Throwable $e) {
                // Table may not have `created_at`, skip silently
                continue;
            }
        }


        return [
            'count' => $totalCount,
            'sample' => array_slice($samples, 0, 5),
        ];
    }

    /**
     * @return array{count: int, sample: list<array{id: string, name: string}>}
     */
    private function cleanupOrphanedCustomFieldSets(bool $dryRun, Context $context): array
    {
        /** @var list<array{id: string, name: string}> $customFieldSets */
        $customFieldSets = $this->connection->fetchAllAssociative(<<<SQL
            SELECT cfs.id, cfs.name
            FROM custom_field_set cfs
            LEFT JOIN custom_field_set_relation cfsr ON cfs.id = cfsr.set_id
            WHERE cfsr.set_id IS NULL
            LIMIT 1000
        SQL);

        if (!$dryRun && $customFieldSets !== []) {
            $idBytes = [];

            foreach ($customFieldSets as $set) {
                // All fields are defined as string, but we validate UUID format for safety
                if (Uuid::isValid($set['id'])) {
                    $idBytes[] = Uuid::fromHexToBytes($set['id']);
                }
            }

            if ($idBytes !== []) {
                $this->connection->executeStatement(
                    'DELETE FROM custom_field_set WHERE id IN (:ids)',
                    ['ids' => $idBytes],
                    ['ids' => Connection::PARAM_STR_ARRAY]
                );
            }
        }

        return [
            'count' => count($customFieldSets),
            'sample' => array_slice($customFieldSets, 0, 5),
        ];
    }

    public function getName(): string
    {
        return 'System Log & Technical Data Cleanup';
    }

    public function getKey(): string
{
    return 'logCleanup';
}
}
