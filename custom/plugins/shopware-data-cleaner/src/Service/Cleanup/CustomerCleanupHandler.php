<?php

declare(strict_types=1);

namespace IctDataCleanerPro\Service\Cleanup;

use Shopware\Core\System\Customer\CustomerEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use IctDataCleanerPro\Service\CleanupLoggerService;
use Shopware\Core\Defaults;
class CustomerCleanupHandler implements CleanupHandlerInterface
{
    private EntityRepository $customerRepository;
    private EntityRepository $customerAddressRepository;

    private CleanupLoggerService $logger;

    public function __construct(EntityRepository $customerRepository, EntityRepository $customerAddressRepository,
    CleanupLoggerService $logger)
    {
        $this->customerRepository = $customerRepository;
        $this->customerAddressRepository = $customerAddressRepository;
        $this->logger = $logger;
    }

    public function cleanup(array $config, bool $dryRun, Context $context): array
    {
        $results = [
            'name' => $this->getName(),
            'items' => []
        ];

        if (isset($config['customerCleanup.guestMonths'])) {
            $results['items']['guest_customers'] = $this->cleanupGuestCustomers(
                (int) $config['customerCleanup.guestMonths'],
                $dryRun,
                $context
            );
        }

        if (isset($config['customerCleanup.inactiveMonths'])) {
            $results['items']['inactive_customers'] = $this->cleanupInactiveCustomers(
                (int) $config['customerCleanup.inactiveMonths'],
                $dryRun,
                $context
            );
        }

        return $results;
    }


    public function cleanupGuestCustomers(int $months, bool $dryRun, Context $context): array
    {
        $date = new \DateTimeImmutable("-{$months} months");

        $criteria = new Criteria();
        $criteria->addFilter(new MultiFilter(MultiFilter::CONNECTION_AND, [
            new EqualsFilter('guest', true),
            new EqualsFilter('active', false),
            new RangeFilter('createdAt', ['lt' => $date->format(DATE_ATOM)])
        ]));
        $criteria->addAssociation('orderCustomers');
        $criteria->addSorting(new FieldSorting('createdAt'));
        $criteria->setLimit(1000);

        $result = $this->customerRepository->search($criteria, $context);
        $customers = [];

        /** @var CustomerEntity $customer */
        foreach ($result->getEntities() as $customer) {
            if ($customer->getOrderCustomers() === null || $customer->getOrderCustomers()->count() === 0) {
                $customers[] = [
                    'id' => $customer->getId(),
                    'email' => $customer->getEmail(),
                    'first_name' => $customer->getFirstName(),
                    'last_name' => $customer->getLastName(),
                ];
            }
        }

        if (empty($customers)) {
            $this->logger->logToFile('customers', 'info', [
                'message' => 'No guest customers eligible for deletion',
                'date_cutoff' => $date->format(DATE_ATOM)
            ]);
        }

        if (!$dryRun && !empty($customers)) {
            $ids = [];
            foreach ($customers as $customer) {
                $ids[] = ['id' => $customer['id']];
            }

            try {
                $this->customerRepository->delete($ids, $context);

                $this->logger->logSuccess('customers', [
                    'action' => 'delete',
                    'count' => count($ids),
                    'ids' => array_column($ids, 'id'),
                ]);
            } catch (\Exception $e) {
                $this->logger->logError('customers', $e, [
                    'action' => 'delete',
                    'ids' => array_column($ids, 'id'),
                ]);
            }
        }

        return [
            'count' => count($customers),
            'sample' => $customers,
        ];
    }

  public function cleanupInactiveCustomers(int $months, bool $dryRun, Context $context): array
{
    $date = new \DateTimeImmutable("-{$months} months");

    $criteria = new Criteria();
    $criteria->addFilter(new MultiFilter(MultiFilter::CONNECTION_AND, [
        new EqualsFilter('guest', false),
        new RangeFilter('createdAt', ['lt' => $date->format(DATE_ATOM)]),
        new MultiFilter(MultiFilter::CONNECTION_OR, [
            new RangeFilter('lastLogin', ['lt' => $date->format(DATE_ATOM)]),
            new EqualsFilter('lastLogin', null),
        ]),
    ]));
    $criteria->addAssociation('addresses'); // ✅ Correct association
    $criteria->setLimit(1000);

    $result = $this->customerRepository->search($criteria, $context);
    $customers = [];

    foreach ($result->getEntities() as $customer) {
        $addressIds = $customer->getAddresses()?->getIds() ?? [];

          $customers[] = [
        'id' => $customer->getId(),
        'email' => $customer->getEmail(),
        'first_name' => $customer->getFirstName(),
        'last_name' => $customer->getLastName(),
    ];
    }

    if (!$dryRun && !empty($customers)) {
        $customerIds = [];
        $addressIds = [];

        foreach ($customers as $c) {
            $customerIds[] = ['id' => $c['id']];
            if(isset($c['addressIds']) && !empty($c['addressIds'])){
            foreach ($c['addressIds'] as $addrId) {
                $addressIds[] = ['id' => $addrId];
            }
        }
        }
        try {
            if (isset($addressIds) && !empty($addressIds)) {
                $this->customerAddressRepository->delete($addressIds, $context);
            }

            $this->customerRepository->delete($customerIds, $context);

            $this->logger->logSuccess('customers', [
                'action' => 'delete',
                'count' => count($customerIds),
                'ids' => array_column($customerIds, 'id'),
            ]);
        } catch (\Exception $e) {
            $this->logger->logError('customers', $e, [
                'action' => 'delete',
                'ids' => array_column($customerIds, 'id'),
            ]);
        }
    }

    return [
        'count' => count($customers),
        'sample' => $customers,
    ];
}

    public function getName(): string
    {
        return 'Customer Cleanup';
    }
}
