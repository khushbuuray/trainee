<?php declare(strict_types=1);

namespace IctDataCleanerPro\Service\Cleanup;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Uuid\Uuid;

class CustomerCleanupHandler implements CleanupHandlerInterface
{
    private EntityRepository $customerRepository;
    private Connection $connection;

    public function __construct(
        EntityRepository $customerRepository,
        Connection $connection
    ) {
        $this->customerRepository = $customerRepository;
        $this->connection = $connection;
    }

    public function cleanup(array $config, bool $dryRun, Context $context): array
    {
        $results = [
            'name' => $this->getName(),
            'items' => []
        ];

        // Clean guest customers
        if (isset($config['customerCleanup.guestMonths'])) {
            $guestResults = $this->cleanupGuestCustomers(
                (int) $config['customerCleanup.guestMonths'],
                $dryRun,
                $context
            );
            $results['items']['guest_customers'] = $guestResults;
        }

        // Clean inactive customers
        if (isset($config['customerCleanup.inactiveMonths'])) {
            $inactiveResults = $this->cleanupInactiveCustomers(
                (int) $config['customerCleanup.inactiveMonths'],
                $dryRun,
                $context
            );
            $results['items']['inactive_customers'] = $inactiveResults;
        }

        return $results;
    }

    private function cleanupGuestCustomers(int $months, bool $dryRun, Context $context): array
    {
        $date = new \DateTime();
        $date->modify("-{$months} months");
        $sql = <<<SQL
SELECT c.id, c.email, c.first_name, c.last_name
FROM customer c
LEFT JOIN `order_customer` oc ON c.id = oc.customer_id
WHERE c.guest = 1 
AND c.created_at < :date
-- AND oc.id IS NULL
LIMIT 1000
SQL;

        $customers = $this->connection->fetchAllAssociative($sql, [
            'date' => $date->format('Y-m-d H:i:s')
        ]);
  
        $customers = array_map(function ($customer) {
    return [
        'id' => Uuid::fromBytesToHex($customer['id']),
        'email' => $customer['email'],
        'first_name' => $customer['first_name'],
        'last_name' => $customer['last_name'],
    ];
}, $customers);

        if (!$dryRun && !empty($customers)) {
            $ids = array_map(function ($customer) {
                return ['id' => Uuid::fromHexToBytes($customer['id'])];
            }, $customers);
            
            // $this->customerRepository->delete($ids, $context);
        }

        return [
            'count' => count($customers),
            'sample' => $customers
        ];
    }

    private function cleanupInactiveCustomers(int $months, bool $dryRun, Context $context): array
    {
        $date = new \DateTime();
        $date->modify("-{$months} months");

        $sql = <<<SQL
SELECT c.id, c.email, c.first_name, c.last_name
FROM customer c
LEFT JOIN `order_customer` o ON c.id = o.customer_id AND o.created_at > :date
WHERE c.guest = 0 
AND c.created_at < :date
AND (c.last_login IS NULL OR c.last_login < :date)
AND o.id IS NULL
LIMIT 1000
SQL;

        $customers = $this->connection->fetchAllAssociative($sql, [
            'date' => $date->format('Y-m-d H:i:s')
        ]);

        if (!$dryRun && !empty($customers)) {
            $ids = array_map(function ($customer) {
                return ['id' => $customer['id']];
            }, $customers);
            
            $this->customerRepository->delete($ids, $context);
        }

        return [
            'count' => count($customers),
            'sample' => array_slice($customers, 0, 5)
        ];
    }

    public function getName(): string
    {
        return 'Customer Cleanup';
    }
}
