<?php declare(strict_types=1);

namespace IctDataCleanerPro\Service\Cleanup;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\NotFilter;


class CategoryCleanupHandler implements CleanupHandlerInterface
{
    private EntityRepository $categoryRepository;
    private Connection $connection;

    public function __construct(
        EntityRepository $categoryRepository,
        Connection $connection
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->connection = $connection;
    }

    public function cleanup(array $config, bool $dryRun, Context $context): array
    {
        $results = [
            'name' => $this->getName(),
            'items' => []
        ];
        // Clean empty categories
        if ($config['categoryCleanup.emptyCategories'] ?? false) {
            $emptyResults = $this->cleanupEmptyCategories($dryRun, $context);
            $results['items']['empty_categories'] = $emptyResults;
        }

        // Clean categories with no sales
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

     // Category is CMS page type, has no CMS layout assigned
    $criteria->addFilter(new EqualsFilter('type', 'page'));
    $criteria->addFilter(new EqualsFilter('cmsPageId', null));

    // Not used in any sales channel
    $criteria->addFilter(new EqualsFilter('navigationSalesChannels.id', null));
    $criteria->addFilter(new EqualsFilter('footerSalesChannels.id', null));
    $criteria->addFilter(new EqualsFilter('serviceSalesChannels.id', null));

    // No products assigned to this category (via `products` association)
    $criteria->addFilter(new EqualsFilter('products.id', null));

    // Include translations for name
    $criteria->addAssociation('translations');

    $categories = $this->categoryRepository->search($criteria, $context);

    $sample = [];
    foreach ($categories->getEntities() as $category) {
        $sample[] = [
            'id' => $category->getId(),
            'name' => $category->getTranslated()['name'] ?? 'N/A',
        ];
    }

    if (!$dryRun && !empty($sample)) {
        $ids = array_map(fn($c) => ['id' => $c['id']], $sample);
        $this->categoryRepository->delete($ids, $context);
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

    // Only CMS page-type categories
    $criteria->addFilter(new EqualsFilter('type', 'page'));

    // Not used in any sales channel
    $criteria->addFilter(new EqualsFilter('navigationSalesChannels.id', null));
    $criteria->addFilter(new EqualsFilter('footerSalesChannels.id', null));
    $criteria->addFilter(new EqualsFilter('serviceSalesChannels.id', null));

    // No recent orders for any of the products in this category
    // Note: we assume that if the products exist, they have no orders or orders are old
    $criteria->addAssociation('products.orderLineItems.order');

    // Add post-filter logic in PHP because DAL can't do `LEFT JOIN ... WHERE o.created_at IS NULL OR o.created_at < :date`
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

    if (!$dryRun && !empty($filtered)) {
        $ids = array_map(fn($cat) => ['id' => $cat['id']], $filtered);
        $this->categoryRepository->delete($ids, $context);
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
