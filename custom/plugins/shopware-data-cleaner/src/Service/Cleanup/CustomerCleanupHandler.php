<?php

declare(strict_types=1);

namespace IctDataCleanerPro\Service\Cleanup;

use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Checkout\Customer\CustomerCollection;
use Shopware\Core\Checkout\Customer\Aggregate\CustomerAddress\CustomerAddressEntity;
use Shopware\Core\Checkout\Customer\Aggregate\CustomerAddress\CustomerAddressCollection;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use IctDataCleanerPro\Service\CleanupLoggerService;

class CustomerCleanupHandler implements CleanupHandlerInterface
{
    /** @var EntityRepository<CustomerCollection> */
    private EntityRepository $customerRepository;

    /** @var EntityRepository<CustomerAddressCollection> */
    private EntityRepository $customerAddressRepository;

    private CleanupLoggerService $logger;

    /**
     * @param EntityRepository<CustomerCollection> $customerRepository
     * @param EntityRepository<CustomerAddressCollection> $customerAddressRepository
     */
    public function __construct(
        EntityRepository $customerRepository,
        EntityRepository $customerAddressRepository,
        CleanupLoggerService $logger
    ) {
        $this->customerRepository = $customerRepository;
        $this->customerAddressRepository = $customerAddressRepository;
        $this->logger = $logger;
    }

    /**
     * @param array<string, mixed> $config
     * @return array{name: string, items: array<string, array{count: int, sample: list<array{id: string, name: string}>}>}
     */
    public function cleanup(array $config, bool $dryRun, Context $context): array
    {
        $results = [
            'name' => $this->getName(),
            'items' => [],
        ];

        if (
            isset($config['customerCleanup.guestMonths']) &&
            is_numeric($config['customerCleanup.guestMonths'])
        ) {
            $guestMonths = (int) $config['customerCleanup.guestMonths'];
            $results['items']['guest_customers'] = $this->cleanupGuestCustomers(
                $guestMonths,
                $dryRun,
                $context
            );
        }

        if (
            isset($config['customerCleanup.inactiveMonths']) &&
            is_numeric($config['customerCleanup.inactiveMonths'])
        ) {
            $inactiveMonths = (int) $config['customerCleanup.inactiveMonths'];
            $results['items']['inactive_customers'] = $this->cleanupInactiveCustomers(
                $inactiveMonths,
                $dryRun,
                $context
            );
        }

        return $results;
    }


    /**
     * @return array{count: int, sample: list<array{id: string, name: string}>}
     */
    public function cleanupGuestCustomers(int $months, bool $dryRun, Context $context): array
    {
        $date = new \DateTimeImmutable("-{$months} months");

        $criteria = new Criteria();
        $criteria->addFilter(new MultiFilter(MultiFilter::CONNECTION_AND, [
            new EqualsFilter('guest', true),
            new EqualsFilter('active', false),
            new RangeFilter('createdAt', ['lt' => $date->format(DATE_ATOM)]),
        ]));
        $criteria->addAssociation('orderCustomers');
        $criteria->addSorting(new FieldSorting('createdAt'));
        $criteria->setLimit(1000);

        /** @var EntitySearchResult<CustomerCollection> $result */
        $result = $this->customerRepository->search($criteria, $context);

        /** @var list<array{id: string, name: string}> $customers */
        $customers = [];

        foreach ($result->getEntities() as $customer) {
            if ($customer->getOrderCustomers()?->count() === 0) {
                $customers[] = [
                    'id' => $customer->getId(),
                    'name' => $customer->getFirstName() . ' ' . $customer->getLastName(),
                ];
            }
        }

        if (!$dryRun && $customers !== []) {
            $ids = array_map(
                static fn(array $c): array => ['id' => $c['id']],
                $customers
            );

            try {
                $this->customerRepository->delete($ids, $context);
                $this->logger->logSuccess('customers', [
                    'action' => 'delete_guest_customers',
                    'count' => count($ids),
                    'ids' => array_column($ids, 'id'),
                ]);
            } catch (\Throwable $e) {
                $this->logger->logError('customers', $e, [
                    'action' => 'delete_guest_customers',
                ]);
            }
        }

        return [
            'count' => count($customers),
            'sample' => $customers,
        ];
    }

    /**
     * @return array{count: int, sample: list<array{id: string, name: string}>}
     */
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
        $criteria->addAssociation('addresses');
        $criteria->setLimit(1000);

        /** @var EntitySearchResult<CustomerCollection> $result */
        $result = $this->customerRepository->search($criteria, $context);

        /** @var list<array{id: string, name: string, addressIds: list<string>}> $customers */
        $customers = [];

        foreach ($result->getEntities() as $customer) {
            $addressIds = $customer->getAddresses()?->getIds() ?? [];
            $customers[] = [
                'id' => $customer->getId(),
                'name' => $customer->getFirstName() . ' ' . $customer->getLastName(),
                'addressIds' => array_values($addressIds),
            ];
        }
        if (!$dryRun && $customers !== []) {
            $customerIds = array_map(
                static fn(array $c): array => ['id' => $c['id']],
                $customers
            );
            $addressIds = [];
            try {
               $this->customerRepository->delete($customerIds, $context);
                $this->logger->logSuccess('customers', [
                    'action' => 'delete_inactive_customers',
                    'count' => count($customerIds),
                    'ids' => array_column($customerIds, 'id'),
                ]);
            } catch (\Throwable $e) {
                $this->logger->logError('customers', $e, [
                    'action' => 'delete_inactive_customers',
                ]);
            }
        }

        $sample = array_map(static fn(array $c): array => [
            'id' => $c['id'],
            'name' => $c['name'],
        ], $customers);

        return [
            'count' => count($customers),
            'sample' => $sample,
        ];
    }

    public function getName(): string
    {
        return 'Customer Cleanup';
    }

    public function getKey(): string
    {
        return 'customerCleanup';
    }
}
