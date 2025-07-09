<?php declare(strict_types=1);

namespace ICTECHDataCleanerPro\Service\Cleanup;

use Shopware\Core\Content\Category\CategoryCollection;
use Shopware\Core\Content\Category\CategoryEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use ICTECHDataCleanerPro\Service\CleanupLoggerService;

class CategoryCleanupHandler implements CleanupHandlerInterface
{
    /** @var EntityRepository<CategoryCollection> */
    private EntityRepository $categoryRepository;

    private CleanupLoggerService $logger;

    /**
     * @param EntityRepository<CategoryCollection> $categoryRepository
     */
    public function __construct(
        EntityRepository $categoryRepository,
        CleanupLoggerService $logger
    ) {
        $this->categoryRepository = $categoryRepository;
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
            'items' => []
        ];

        if ($config['categoryCleanup.emptyCategories'] ?? false) {
            $emptyResults = $this->cleanupEmptyCategories($dryRun, $context);
            $results['items']['empty_categories'] = $emptyResults;
        }

        if (isset($config['categoryCleanup.noSalesMonths']) && is_numeric($config['categoryCleanup.noSalesMonths'])) {
            $noSalesResults = $this->cleanupCategoriesWithNoSales(
                (int) $config['categoryCleanup.noSalesMonths'],
                $dryRun,
                $context
            );
            $results['items']['no_sales_categories'] = $noSalesResults;
        }

        return $results;
    }

    /**
     * @return array{count: int, sample: list<array{id: string, name: string}>}
     */
    private function cleanupEmptyCategories(bool $dryRun, Context $context): array
    {
        $criteria = new Criteria();
        $criteria->setLimit(100);
        $criteria->addFilter(new EqualsFilter('type', 'page'));
        $criteria->addFilter(new EqualsFilter('cmsPageId', null));
        $criteria->addFilter(new EqualsFilter('navigationSalesChannels.id', null));
        $criteria->addFilter(new EqualsFilter('footerSalesChannels.id', null));
        $criteria->addFilter(new EqualsFilter('serviceSalesChannels.id', null));
        $criteria->addFilter(new EqualsFilter('products.id', null));
        $criteria->addAssociation('translations');

        $categories = $this->categoryRepository->search($criteria, $context);

        /** @var list<array{id: string, name: string}> $sample */
        $sample = [];

        foreach ($categories->getEntities() as $categoryEntity) {
            $translatedName = $categoryEntity->getTranslated()['name'] ?? null;
            $name = is_string($translatedName) ? $translatedName : 'N/A';


            $sample[] = [
                'id' => $categoryEntity->getId(),
                'name' => $name,
            ];
        }

        $this->logger->logToFile('category', 'info', [
            'function' => 'cleanupEmptyCategories',
            'count' => count($sample),
        ]);

        if (!$dryRun && !empty($sample)) {
            $ids = array_map(static fn(array $cat): array => ['id' => $cat['id']], $sample);
            try {
                $this->categoryRepository->delete($ids, $context);

                $this->logger->logSuccess('category', [
                    'action' => 'delete_empty_categories',
                    'count' => count($ids),
                    'ids' => array_column($ids, 'id'),
                ]);
            } catch (\Exception $e) {
                $this->logger->logError('category', $e, [
                    'action' => 'delete_empty_categories',
                    'ids' => array_column($ids, 'id'),
                ]);
            }
        }

        return [
            'count' => count($sample),
            'sample' => $sample,
        ];
    }

    /**
     * @return array{count: int, sample: list<array{id: string, name: string}>}
     */
    private function cleanupCategoriesWithNoSales(int $months, bool $dryRun, Context $context): array
    {
        $cutoffDate = new \DateTime();
        $cutoffDate->modify("-{$months} months");

        $criteria = new Criteria();
        $criteria->setLimit(1000);
        $criteria->addFilter(new EqualsFilter('type', 'page'));
        $criteria->addFilter(new EqualsFilter('navigationSalesChannels.id', null));
        $criteria->addFilter(new EqualsFilter('footerSalesChannels.id', null));
        $criteria->addFilter(new EqualsFilter('serviceSalesChannels.id', null));
        $criteria->addAssociation('products.orderLineItems.order');
        $criteria->addAssociation('translations');

        $categories = $this->categoryRepository->search($criteria, $context);

        /** @var list<array{id: string, name: string}> $filtered */
        $filtered = [];

        foreach ($categories->getEntities() as $categoryEntity) {
            $hasRecentOrder = false;

            $products = $categoryEntity->getProducts();
            if ($products === null) {
                continue;
            }

            foreach ($products as $product) {
                $lineItems = $product->getOrderLineItems();
                if ($lineItems === null) {
                    continue;
                }

                foreach ($lineItems as $lineItem) {
                    $order = $lineItem->getOrder();
                    if ($order !== null && $order->getCreatedAt() >= $cutoffDate) {
                        $hasRecentOrder = true;
                        break 2;
                    }
                }
            }

            if (!$hasRecentOrder) {
                $translatedName = $categoryEntity->getTranslated()['name'] ?? null;
                $name = is_string($translatedName) ? $translatedName : 'N/A';


                $filtered[] = [
                    'id' => $categoryEntity->getId(),
                    'name' => $name,
                ];
            }
        }

        $this->logger->logToFile('category', 'info', [
            'function' => 'cleanupCategoriesWithNoSales',
            'count' => count($filtered),
            'cutoff_date' => $cutoffDate->format(DATE_ATOM),
        ]);

        if (!$dryRun && !empty($filtered)) {
            $ids = array_map(static fn(array $cat): array => ['id' => $cat['id']], $filtered);
            try {
                $this->categoryRepository->delete($ids, $context);

                $this->logger->logSuccess('category', [
                    'action' => 'delete_no_sales_categories',
                    'count' => count($ids),
                    'ids' => array_column($ids, 'id'),
                ]);
            } catch (\Exception $e) {
                $this->logger->logError('category', $e, [
                    'action' => 'delete_no_sales_categories',
                    'ids' => array_column($ids, 'id'),
                ]);
            }
        }

        return [
            'count' => count($filtered),
            'sample' => $filtered,
        ];
    }

    public function getName(): string
    {
        return 'Category Cleanup';
    }

    public function getKey(): string
    {
        return 'categoryCleanup';
    }
}
