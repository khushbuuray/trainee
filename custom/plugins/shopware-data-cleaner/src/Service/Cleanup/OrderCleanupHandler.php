<?php declare(strict_types=1);

namespace IctDataCleanerPro\Service\Cleanup;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;

class OrderCleanupHandler implements CleanupHandlerInterface
{
    private EntityRepository $orderRepository;
    private EntityRepository $transactionRepository;
    private Connection $connection;

    public function __construct(
        EntityRepository $orderRepository,
        EntityRepository $transactionRepository,
        Connection $connection
    ) {
        $this->orderRepository = $orderRepository;
        $this->transactionRepository = $transactionRepository;
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

        $criteria = new Criteria();
        $criteria->addAssociation('stateMachineState');
        $criteria->addFilter(new EqualsFilter('stateMachineState.technicalName', 'cancelled'));
        $criteria->addFilter(new RangeFilter('createdAt', [
            RangeFilter::LT => $date->format(\DATE_ATOM),
        ]));
        $criteria->setLimit(1000);

        $orders = $this->orderRepository->search($criteria, $context);

        $sample = [];
        foreach ($orders->getEntities() as $order) {
            $sample[] = [
                'id' => $order->getId(),
                'order_number' => $order->getOrderNumber(),
                'created_at' => $order->getCreatedAt()?->format('Y-m-d H:i:s') ?? 'N/A',
            ];
        }

        if (!$dryRun && $orders->count() > 0) {
               $ids = array_map(fn($id) => ['id' => $id], array_keys($orders->getIds()));
               $this->orderRepository->delete($ids, $context);
        }

        return [
            'count' => $orders->count(),
            'sample' => $sample,
        ];
    }

    private function cleanupOldTransactions(int $months, bool $dryRun, Context $context): array
    {
        $date = new \DateTime();
        $date->modify("-{$months} months");
        $criteria = new Criteria();
        $criteria->addFilter(new RangeFilter('createdAt', [
            RangeFilter::LT => $date->format(\DATE_ATOM)
        ]));
        $criteria->addAssociation('order');
        $criteria->setLimit(1000);

        $transactions = $this->transactionRepository->search($criteria, $context);

        $sample = [];
        foreach ($transactions->getEntities() as $transaction) {
            $sample[] = [
                'id' => $transaction->getId(),
                'transaction_id' => $transaction->getId(),
                'transaction_created_at' => $transaction->getCreatedAt()?->format('Y-m-d H:i:s') ?? 'N/A',
                'order_id' => $transaction->getOrder()?->getId() ?? null,
                'order_number' => $transaction->getOrder()?->getOrderNumber() ?? 'N/A',
            ];
        }

        if (!$dryRun && $transactions->count() > 0) {
    $ids = [];
    foreach ($transactions->getEntities() as $transaction) {
        $ids[] = ['id' => $transaction->getId()];
    }
    $this->transactionRepository->delete($ids, $context);
}
        return [
            'count' => $transactions->count(),
            'sample' => $sample,
        ];
    }

    public function getName(): string
    {
        return 'Order Cleanup';
    }
}
