<?php declare(strict_types=1);
 
namespace IctDataCleanerPro\Service\Cleanup;
 
use DateTime;

use Shopware\Core\Framework\Context;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;

use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;

use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;

use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;

use Shopware\Core\Checkout\Order\OrderEntity;

use Shopware\Core\Checkout\Order\OrderCollection;

use Shopware\Core\Checkout\Order\Aggregate\OrderTransaction\OrderTransactionEntity;

use Shopware\Core\Checkout\Order\Aggregate\OrderTransaction\OrderTransactionCollection;

use IctDataCleanerPro\Service\CleanupLoggerService;
 
class OrderCleanupHandler implements CleanupHandlerInterface

{

    /** @var EntityRepository<OrderCollection> */

    private readonly EntityRepository $orderRepository;
 
    /** @var EntityRepository<OrderTransactionCollection> */

    private readonly EntityRepository $transactionRepository;
 
    private readonly CleanupLoggerService $logger;
 
    /**

     * @param EntityRepository<OrderCollection> $orderRepository

     * @param EntityRepository<OrderTransactionCollection> $transactionRepository

     */

    public function __construct(

        EntityRepository $orderRepository,

        EntityRepository $transactionRepository,

        CleanupLoggerService $logger

    ) {

        $this->orderRepository = $orderRepository;

        $this->transactionRepository = $transactionRepository;

        $this->logger = $logger;

    }
 
    /**

     * @param array<string, mixed> $config

     * @return array{name: string, items: array<string, array{count: int, sample: list<array{id: string, name: string}>}>}

     */

    public function cleanup(array $config, bool $dryRun, Context $context): array

    {

        $items = [];
 
        $cancelledMonthsRaw = $config['orderCleanup.cancelledAgeMonths'] ?? null;

        $cancelledMonths = is_numeric($cancelledMonthsRaw) ? (int) $cancelledMonthsRaw : null;

        if ($cancelledMonths !== null) {

            $items['cancelled_orders'] = $this->cleanupCancelledOrders($cancelledMonths, $dryRun, $context);

        }
 
        $txnMonthsRaw = $config['transactionCleanup.ageMonths'] ?? null;

        $txnMonths = is_numeric($txnMonthsRaw) ? (int) $txnMonthsRaw : null;

        if ($txnMonths !== null) {

            $items['old_transactions'] = $this->cleanupOldTransactions($txnMonths, $dryRun, $context);

        }
 
        return [

            'name' => $this->getName(),

            'items' => $items,

        ];

    }
 
    /**

     * @return array{count: int, sample: list<array{id: string, name: string}>}

     */

    private function cleanupCancelledOrders(int $months, bool $dryRun, Context $context): array

    {

        $cutoff = (new DateTime())->modify("-{$months} months");
 
        $criteria = new Criteria();

        $criteria->addAssociation('stateMachineState');

        $criteria->addFilter(new EqualsFilter('stateMachineState.technicalName', 'cancelled'));

        $criteria->addFilter(new RangeFilter('createdAt', [RangeFilter::LT => $cutoff->format(DATE_ATOM)]));

        $criteria->setLimit(1000);
 
        /** @var OrderCollection $orders */

        $orders = $this->orderRepository->search($criteria, $context)->getEntities();
 
        $sample = [];

        foreach ($orders as $order) {


            /** @var OrderEntity $order */

            $sample[] = [

                'id' => $order->getId(),
                'orderNumber' => $order->getOrderNumber() ?? 'N/A',
                // 'customerName' => $order->getOrderCustomer()->getFirstName() . ' ' . $order->getOrderCustomer()->getLastName(),
                'email' => $order->getOrderCustomer()->getEmail(),
                'orderDate' => $order->getOrderDate()->format(DATE_ATOM)
            ];

        }
 
        $this->logger->logToFile('orders', 'info', [

            'function' => 'cleanupCancelledOrders',

            'count' => $orders->count(),

            'cutoff_date' => $cutoff->format(DATE_ATOM),

        ]);
 
        if (!$dryRun && $orders->count() > 0) {
 
            $ids = array_values(
 
                array_map(
 
                    static fn(OrderEntity $e): array => ['id' => $e->getId()],
 
                    $orders->getElements()
 
                )
 
            );
 
            try {
 
                $this->orderRepository->delete($ids, $context);
 
                $this->logger->logSuccess('orders', [
 
                    'action' => 'delete_cancelled',
 
                    'count' => count($ids),
 
                    'ids' => array_column($ids, 'id'),
 
                ]);
 
            } catch (\Throwable $e) {
 
                $this->logger->logError('orders', $e, [
 
                    'action' => 'delete_cancelled',
 
                    'ids' => array_column($ids, 'id'),
 
                ]);
 
            }
 
        }
 
 
        return [

            'count' => $orders->count(),

            'sample' => array_slice($sample, 0, 5),

        ];

    }
 
    /**

     * @return array{count: int, sample: list<array{id: string, name: string}>}

     */

    /**

     * @return array{count: int, sample: list<array{id: string, name: string}>}

     */

    private function cleanupOldTransactions(int $months, bool $dryRun, Context $context): array

    {

        $cutoff = (new DateTime())->modify("-{$months} months");
 
        $criteria = new Criteria();

        $criteria->addFilter(new RangeFilter('createdAt', [RangeFilter::LT => $cutoff->format(DATE_ATOM)]));

        $criteria->addAssociation('order');

        $criteria->setLimit(1000);
 
        /** @var OrderTransactionCollection $transactions */

        $transactions = $this->transactionRepository->search($criteria, $context)->getEntities();
 
        // Collect order IDs from transactions

        $orderIdsToDelete = [];

        foreach ($transactions as $txn) {

            $order = $txn->getOrder();

            if ($order !== null) {
                $orderIdsToDelete[$order->getId()] = $order->getOrderNumber() ?? 'N/A';
            }

        }
 
        // Build sample data

        $sample = [];

        foreach ($orderIdsToDelete as $id => $name) {
            $sample[] = ['id' => $id, 'name' => $name];

        }
 
        $this->logger->logToFile('orders', 'info', [

            'function' => 'cleanupOldTransactions (delete orders)',

            'count' => count($orderIdsToDelete),

            'cutoff_date' => $cutoff->format(DATE_ATOM),

            'dryRun' => $dryRun,

        ]);
 
        // Convert order IDs to delete format

        $ids = array_map(

            static fn(string $id): array => ['id' => $id],

            array_keys($orderIdsToDelete)

        );
 
        if (!$dryRun && count($ids) > 0) {

            try {

                $this->orderRepository->delete($ids, $context);

                $this->logger->logSuccess('orders', [

                    'action' => 'delete_orders_by_old_txns',

                    'count' => count($ids),

                    'ids' => array_column($ids, 'id'),

                ]);

            } catch (\Throwable $e) {

                $this->logger->logError('orders', $e, [

                    'action' => 'delete_orders_by_old_txns',

                    'ids' => array_column($ids, 'id'),

                    'error' => $e->getMessage(),

                ]);

            }

        }
 
        return [

            'count' => count($orderIdsToDelete),

            'sample' => $sample,

        ];

    }
 
 
    public function getName(): string

    {

        return 'Order Cleanup';

    }

    public function getKey(): string

    {

        return 'orderCleanup';

    }

}

 