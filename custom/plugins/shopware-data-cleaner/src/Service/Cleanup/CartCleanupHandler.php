<?php declare(strict_types=1);

namespace IctDataCleanerPro\Service\Cleanup;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Context;

class CartCleanupHandler implements CleanupHandlerInterface
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

        // Clean abandoned carts
        if (isset($config['cartCleanup.abandonedDays'])) {
            $cartResults = $this->cleanupAbandonedCarts(
                (int) $config['cartCleanup.abandonedDays'],
                $dryRun,
                $context
            );
            $results['items']['abandoned_carts'] = $cartResults;
        }

        return $results;
    }

    private function cleanupAbandonedCarts(int $days, bool $dryRun, Context $context): array
    {
        $date = new \DateTime();
        $date->modify("-{$days} days");

        // Get cart count first
        $countSql = <<<SQL
SELECT COUNT(*) as count
FROM cart 
WHERE created_at < :date
SQL;

        $countResult = $this->connection->fetchAssociative($countSql, [
            'date' => $date->format('Y-m-d H:i:s')
        ]);

        $count = (int) $countResult['count'];

        // Get sample data
        $sampleSql = <<<SQL
SELECT token, created_at
FROM cart 
WHERE created_at < :date
LIMIT 5
SQL;

        $samples = $this->connection->fetchAllAssociative($sampleSql, [
            'date' => $date->format('Y-m-d H:i:s')
        ]);

        if (!$dryRun && $count > 0) {
            $deleteSql = <<<SQL
DELETE FROM cart 
WHERE created_at < :date
SQL;
            
            $this->connection->executeStatement($deleteSql, [
                'date' => $date->format('Y-m-d H:i:s')
            ]);
        }

        return [
            'count' => $count,
            'sample' => $samples
        ];
    }

    public function getName(): string
    {
        return 'Cart Cleanup';
    }
}
