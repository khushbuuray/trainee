<?php declare(strict_types=1);

namespace IctDataCleanerPro\Service\Cleanup;

use Shopware\Core\System\Customer\CustomerEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\NotFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;

class CustomerCleanupHandler implements CleanupHandlerInterface
{
    private EntityRepository $customerRepository;

    public function __construct(EntityRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function cleanup(array $config, bool $dryRun, Context $context): array
    {
        $results = [
            'name' => $this->getName(),
            'items' => []
        ];

        if (isset($config['customerCleanup.guestMonths'])) {
            $guestResults = $this->cleanupGuestCustomers((int) $config['customerCleanup.guestMonths'], $dryRun, $context);
            $results['items']['guest_customers'] = $guestResults;
        }

        if (isset($config['customerCleanup.inactiveMonths'])) {
            $inactiveResults = $this->cleanupInactiveCustomers((int) $config['customerCleanup.inactiveMonths'], $dryRun, $context);
            $results['items']['inactive_customers'] = $inactiveResults;
        }

        return $results;
    }

    public function cleanupGuestCustomers(int $months, bool $dryRun, Context $context): array
    {
        $date = new \DateTimeImmutable("-{$months} months");

        $criteria = new Criteria();
        $criteria->addFilter(
            new MultiFilter(MultiFilter::CONNECTION_AND, [
                new EqualsFilter('guest', true),
                new EqualsFilter('active', false),
                new RangeFilter('createdAt', ['lt' => $date->format(DATE_ATOM)])
            ])
        );
        $criteria->addAssociation('orderCustomers');
        $criteria->setLimit(1000);
        $criteria->addSorting(new FieldSorting('createdAt'));

        $result = $this->customerRepository->search($criteria, $context);

        $customers = [];
        /** @var CustomerEntity $customer */
        foreach ($result->getEntities() as $customer) {
            if ($customer->getOrderCustomers()->count() === 0) {
                $customers[] = [
                    'id' => $customer->getId(),
                    'email' => $customer->getEmail(),
                    'first_name' => $customer->getFirstName(),
                    'last_name' => $customer->getLastName(),
                ];
            }
        }

        if (!$dryRun && !empty($customers)) {
            $ids = array_map(fn($c) => ['id' => $c['id']], $customers);
            $this->customerRepository->delete($ids, $context);
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
        $criteria->addFilter(
            new MultiFilter(MultiFilter::CONNECTION_AND, [
                new EqualsFilter('guest', false),
                new RangeFilter('createdAt', ['lt' => $date->format(DATE_ATOM)]),
                new MultiFilter(MultiFilter::CONNECTION_OR, [
                    new RangeFilter('lastLogin', ['lt' => $date->format(DATE_ATOM)]),
                    new EqualsFilter('lastLogin', null),
                ]),
            ])
        );
        $criteria->addAssociation('orderCustomers');
        $criteria->setLimit(1000);

        $result = $this->customerRepository->search($criteria, $context);

        $customers = [];
        /** @var CustomerEntity $customer */
        foreach ($result->getEntities() as $customer) {
            if ($customer->getOrderCustomers()->count() === 0) {
                $customers[] = [
                    'id' => $customer->getId(),
                    'email' => $customer->getEmail(),
                    'first_name' => $customer->getFirstName(),
                    'last_name' => $customer->getLastName(),
                ];
            }
        }

        if (!$dryRun && !empty($customers)) {
            $ids = array_map(fn($c) => ['id' => $c['id']], $customers);
            $this->customerRepository->delete($ids, $context);
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
