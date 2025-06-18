<?php declare(strict_types=1);

namespace IctDataCleanerPro\Service\Cleanup;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Uuid\Uuid;


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

   $orders = array_map(function ($order) {
    if (isset($order['id'])) {
        $order['id'] = Uuid::fromBytesToHex($order['id']);
    }

    return $order;
}, $orders);

if (!$dryRun && !empty($orders)) {
    $ids = array_map(function ($order) {
        return ['id' => Uuid::fromHexToBytes($order['id'])];
    }, $orders);
    
    // $this->orderRepository->delete($ids, $context);
}

        return [
            'count' => count($orders),
            'sample' => $orders
        ];
    }

    private function cleanupOldTransactions(int $months, bool $dryRun, Context $context): array
    {
        $date = new \DateTime();
        $date->modify("-{$months} months");
        // dd($date);
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
-- SELECT id, created_at
-- FROM order_transaction 
-- WHERE created_at < :date
-- LIMIT 1000
SELECT 
    ot.id AS transaction_id,
    ot.created_at AS transaction_created_at,
    o.id AS order_id,
    o.order_number,
    oli.product_id,
    oli.label AS product_name,
    oli.quantity,
    oli.total_price
FROM order_transaction ot
INNER JOIN `order` o ON ot.order_id = o.id
INNER JOIN order_line_item oli ON oli.order_id = o.id
WHERE ot.created_at < :date
LIMIT 1000
SQL;

        $rawSamples = $this->connection->fetchAllAssociative($sampleSql, [
            'date' => $date->format('Y-m-d H:i:s')
        ]);

//         if (!$dryRun && $count > 0) {
//             $deleteSql = <<<SQL
// DELETE FROM order_transaction 
// WHERE created_at < :date
// SQL;
            
            // $this->connection->executeStatement($deleteSql, [
            //     'date' => $date->format('Y-m-d H:i:s')
            // ]);
        // }
    //       $samples = array_map(function ($row) {
    //     return [
    //         'id' => Uuid::fromBytesToHex($row['id']),
    //         'created_at' => $row['created_at'],
    //     ];
    // }, $rawSamples);

      // Optional deletion
    // if (!$dryRun && $count > 0) {
    //     $ids = array_map(function ($row) {
    //         return ['id' => $row['id']];
    //     }, $samples);

    //     // $this->orderRepository->delete($ids, $context);
    // }
    $results = array_map(function ($row) {
    return [
        'id' => Uuid::fromBytesToHex($row['transaction_id']),
        'transaction_id' => Uuid::fromBytesToHex($row['transaction_id']),
        'transaction_created_at' => $row['transaction_created_at'],
        'order_id' => Uuid::fromBytesToHex($row['order_id']),
        'order_number' => $row['order_number'],
        'product_id' => $row['product_id'] ? Uuid::fromBytesToHex($row['product_id']) : null,
        'product_name' => $row['product_name'],
        'quantity' => $row['quantity'],
        'total_price' => $row['total_price'],
    ];
}, $rawSamples);
        return [
            'count' => $count,
            'sample' => $results
        ];
    }

    public function getName(): string
    {
        return 'Order Cleanup';
    }
}
