<?php

declare(strict_types=1);

namespace IctDataCleanerPro\Service\Cleanup;

use DateTime;
use Shopware\Core\Content\Product\ProductCollection;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\NotFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\OrFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use IctDataCleanerPro\Service\CleanupLoggerService;

class ProductCleanupHandler implements CleanupHandlerInterface
{
    /** @var EntityRepository<ProductCollection> */
    private readonly EntityRepository $productRepository;

    private readonly CleanupLoggerService $logger;

    /**
     * @param EntityRepository<ProductCollection> $productRepository
     */
    public function __construct(
        EntityRepository $productRepository,
        CleanupLoggerService $logger
    ) {
        $this->productRepository = $productRepository;
        $this->logger = $logger;
    }


    /**
     * @param array<string, mixed> $config
     * @return array{name: string, items: array<string, array{count: int, sample: list<array{id: string, name: string}>}>}
     */
    public function cleanup(array $config, bool $dryRun, Context $context): array
    {
        $items = [];

        $monthsNotSoldRaw = $config['productCleanup.monthsNotSold'] ?? null;
        $monthsNotSold = is_numeric($monthsNotSoldRaw) ? (int) $monthsNotSoldRaw : null;
        if ($monthsNotSold !== null) {
            $items['products_not_sold_months'] = $this->cleanupProductsNotSold($monthsNotSold, $dryRun, $context);
        }

        if (!empty($config['productCleanup.deleteNeverSold'])) {
            $items['products_never_sold'] = $this->cleanupProductsNeverSold($dryRun, $context);
        }

        $monthsDisabledRaw = $config['productCleanup.monthsDisabled'] ?? null;
        $monthsDisabled = is_numeric($monthsDisabledRaw) ? (int) $monthsDisabledRaw : null;
        if ($monthsDisabled !== null) {
            $items['inactive_products'] = $this->cleanupInactiveProducts($monthsDisabled, $dryRun, $context);
        }

        return [
            'name' => $this->getName(),
            'items' => $items,
        ];
    }

    /**
     * @return array{count: int, sample: list<array{id: string, name: string}>}
     */
    private function cleanupProductsNotSold(int $months, bool $dryRun, Context $context): array
    {
        $cutoff = (new DateTime())->modify("-{$months} months");

        $criteria = new Criteria();
        $criteria->addFilter(new RangeFilter('createdAt', [RangeFilter::LTE => $cutoff->format(DATE_ATOM)]));
        $criteria->addFilter(new EqualsFilter('parentId', null));
        $criteria->addAssociation('orderLineItems.order');
        $criteria->addFilter(new NotFilter(NotFilter::CONNECTION_AND, [
            new RangeFilter('orderLineItems.order.createdAt', [
                RangeFilter::GT => $cutoff->format(DATE_ATOM)
            ])
        ]));
        $criteria->setLimit(1000);

        /** @var ProductCollection $products */
        $products = $this->productRepository->search($criteria, $context)->getEntities();

        $sample = [];
        foreach ($products as $product) {
            /** @var ProductEntity $product */
            $sample[] = [
                'id' => $product->getId(),
                'productNumber' => $product->getProductNumber(),
                'name' => $product->getName(),
            ];
        }
        if (!$dryRun && $products->count() > 0) {
            $ids = array_values(
                array_map(
                    fn(string $id) => ['id' => $id],
                    $products->getIds()
                )
            );
            try {
                $data =  $this->productRepository->delete($ids, $context);
                $this->logger->logSuccess('product', [
                    'action' => 'products_not_sold_months',
                    'count' => count($ids),
                    'ids' => array_column($ids, 'id'),
                ]);
            } catch (\Throwable $e) {
                $this->logger->logError('product', $e, [
                    'action' => 'products_not_sold_months',
                    'ids' => array_column($ids, 'id'),
                ]);
            }
        }

        return [
            'count' => $products->count(),
            'sample' => $sample,
        ];
    }

    /**
     * @return array{count: int, sample: list<array{id: string, name: string}>}
     */
    private function cleanupProductsNeverSold(bool $dryRun, Context $context): array
    {
        $criteria = new Criteria();
        $criteria->addAssociation('orderLineItems');
        $criteria->addFilter(new EqualsFilter('orderLineItems.id', null));
        $criteria->addFilter(new EqualsFilter('parentId', null));
        $criteria->setLimit(1000);

        /** @var ProductCollection $products */
        $products = $this->productRepository->search($criteria, $context)->getEntities();

        $sample = [];
        foreach ($products as $product) {
            /** @var ProductEntity $product */
            $sample[] = [
                'id' => $product->getId(),
                'productNumber' => $product->getProductNumber(),
                'name' => $product->getName(),
            ];
        }

        if (!$dryRun && $products->count() > 0) {
            $ids = array_values(array_map(
                static fn(ProductEntity $e): array => ['id' => $e->getId()],
                $products->getElements()
            ));

            try {
                $this->productRepository->delete($ids, $context);
                $this->logger->logSuccess('product', [
                    'action' => 'delete_never_sold',
                    'count' => count($ids),
                    'ids' => array_column($ids, 'id'),
                ]);
            } catch (\Throwable $e) {
                $this->logger->logError('product', $e, [
                    'action' => 'delete_never_sold',
                    'ids' => array_column($ids, 'id'),
                ]);
            }
        }

        return [
            'count' => $products->count(),
            'sample' => $sample,
        ];
    }

    /**
     * @return array{count: int, sample: list<array{id: string, name: string}>}
     */
    private function cleanupInactiveProducts(int $months, bool $dryRun, Context $context): array
    {
        $cutoff = (new DateTime())->modify("-{$months} months");

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('active', false));
        $criteria->addFilter(new OrFilter([
            new RangeFilter('updatedAt', [RangeFilter::LTE => $cutoff->format(DATE_ATOM)]),
            new EqualsFilter('updatedAt', null)
        ]));
        $criteria->setLimit(1000);

        /** @var ProductCollection $products */
        $products = $this->productRepository->search($criteria, $context)->getEntities();

        $sample = [];
        foreach ($products as $product) {
            /** @var ProductEntity $product */
            $sample[] = [
                'id' => $product->getId(),
                'productNumber' => $product->getProductNumber(),
                'name' => $product->getName(),
            ];
        }

        if (!$dryRun && $products->count() > 0) {
            $ids = array_map(static fn(ProductEntity $e): array => ['id' => $e->getId()], $products->getElements());
            try {
                $data = $this->productRepository->delete($ids, $context);
                $this->logger->logSuccess('product', [
                    'action' => 'delete_inactive',
                    'count' => count($ids),
                    'ids' => array_column($ids, 'id'),
                ]);
            } catch (\Throwable $e) {
                $this->logger->logError('product', $e, [
                    'action' => 'delete_inactive',
                    'ids' => array_column($ids, 'id'),
                ]);
            }
        }

        return [
            'count' => $products->count(),
            'sample' => $sample,
        ];
    }

    public function getName(): string
    {
        return 'Product Cleanup';
    }

    public function getKey(): string
    {
        return 'productCleanup';
    }

}
