<?php declare(strict_types=1);

namespace IctDataCleanerPro\Service\Cleanup;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use IctDataCleanerPro\Service\CleanupLoggerService;

class CategoryCleanupHandler implements CleanupHandlerInterface
{
    private EntityRepository $categoryRepository;
    private CleanupLoggerService $logger;
    private Connection $connection;


    public function __construct(
        EntityRepository $categoryRepository,
        CleanupLoggerService $logger,
        Connection $connection,

    ) {
        $this->categoryRepository = $categoryRepository;
        $this->logger = $logger;
        $this->connection = $connection;

    }

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

        if (isset($config['categoryCleanup.noSalesMonths'])) {
            $noSalesResults = $this->cleanupCategoriesWithNoSales(
                (int) $config['categoryCleanup.noSalesMonths'],
                $dryRun,
                $context
            );
            $results['items']['no_sales_categories'] = $noSalesResults;
        }

        return $results;
    }

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

        $sample = [];
        foreach ($categories->getEntities() as $category) {
            $sample[] = [
                'id' => $category->getId(),
                'name' => $category->getTranslated()['name'] ?? 'N/A',
            ];
        }

        $this->logger->logToFile('category', 'info', [
            'function' => 'cleanupEmptyCategories',
            'count' => count($sample),
        ]);

        if (!$dryRun && !empty($sample)) {
            $ids = array_map(fn($c) => ['id' => $c['id']], $sample);
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

        $categories = $this->categoryRepository->search($criteria, $context);

        $filtered = [];
        foreach ($categories->getEntities() as $category) {
            $hasRecentOrder = false;

            foreach ($category->getProducts() as $product) {
                foreach ($product->getOrderLineItems() as $lineItem) {
                    $order = $lineItem->getOrder();
                    if ($order?->getCreatedAt() >= $cutoffDate) {
                        $hasRecentOrder = true;
                        break 2;
                    }
                }
            }

            if (!$hasRecentOrder) {
                $filtered[] = [
                    'id' => $category->getId(),
                    'name' => $category->getTranslated()['name'] ?? 'N/A',
                ];
            }
        }

        $this->logger->logToFile('category', 'info', [
            'function' => 'cleanupCategoriesWithNoSales',
            'count' => count($filtered),
            'cutoff_date' => $cutoffDate->format(DATE_ATOM),
        ]);

        if (!$dryRun && !empty($filtered)) {
            $ids = array_map(fn($cat) => ['id' => $cat['id']], $filtered);
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
}
