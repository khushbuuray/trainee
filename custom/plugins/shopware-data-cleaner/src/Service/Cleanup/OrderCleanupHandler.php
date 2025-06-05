<?php declare(strict_types=1);

namespace IctDataCleanerPro\Service\Cleanup;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;

class OrderCleanupHandler implements CleanupHandlerInterface
{
    private EntityRepository $orderRepository;
    private Connection $connection;

    public function __construct(
        EntityRepository $orderRepository,
        Connection $connection
    ) {
        $this->orderRepository = $orderRepository;
        $this->connection = $connection;
    }

    public function cleanup(array $config, bool $dryRun, Context $context): array
    {
        $results = [
            'name' => $this->getName(),
            'items' => []
        ];

        // Clean cancelled orders
        if (isset($config['orderCleanup.cancelledAgeMonths'])) {
            $cancelledResults = $this->cleanupCancelledOrders(
                (int) $config['orderCleanup.cancelledAgeMonths'],
                $dryRun,
                $context
            );
            $results['items']['cancelled_orders'] = $cancelledResults;
        }

        // Clean old transactions
        if (isset($config['transactionCleanup.ageMonths'])) {
            $transactionResults = $this->cleanupOldTransactions(
                (int) $config['transactionCleanup.ageMonths'],
                $dryRun,
                $context
            );
            $results['items']['old_transactions'] = $transactionResults;
        }

        return $results;
    }

    private function cleanupCancelledOrders(int $months, bool $dryRun, Context $context): array
    {
        $date = new \DateTime();
        $date->modify("-{$months} months");

        $sql = <<<SQL
SELECT o.id, o.order_number, o.created_at
FROM `order` o
JOIN order_transaction ot ON o.id = ot.order_id
JOIN state_machine_state sms ON ot.state_id = sms.id
WHERE sms.technical_name IN ('cancelled', 'failed')
AND o.created_at < :date
LIMIT 1000
SQL;

        $orders = $this->connection->fetchAllAssociative($sql, [
            'date' => $date->format('Y-m-d H:i:s')
        ]);

        if (!$dryRun && !empty($orders)) {
            $ids = array_map(function ($order) {
                return ['id' => $order['id']];
            }, $orders);
            
            $this->orderRepository->delete($ids, $context);
        }

        return [
            'count' => count($orders),
            'sample' => array_slice($orders, 0, 5)
        ];
    }

    private function cleanupOldTransactions(int $months, bool $dryRun, Context $context): array
    {
        $date = new \DateTime();
        $date->modify("-{$months} months");

        $countSql = <<<SQL
SELECT COUNT(*) as count
FROM order_transaction 
WHERE created_at < :date
SQL;

        $countResult = $this->connection->fetchAssociative($countSql, [
            'date' => $date->format('Y-m-d H:i:s')
        ]);

        $count = (int) $countResult['count'];

        $sampleSql = <<<SQL
SELECT id, created_at
FROM order_transaction 
WHERE created_at < :date
LIMIT 5
SQL;

        $samples = $this->connection->fetchAllAssociative($sampleSql, [
            'date' => $date->format('Y-m-d H:i:s')
        ]);

        if (!$dryRun && $count > 0) {
            $deleteSql = <<<SQL
DELETE FROM order_transaction 
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
        return 'Order Cleanup';
    }
}
