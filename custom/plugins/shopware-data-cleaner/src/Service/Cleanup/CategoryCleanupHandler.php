<?php declare(strict_types=1);

namespace IctDataCleanerPro\Service\Cleanup;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;

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
        $sql = <<<SQL
SELECT c.id, ct.name
FROM category c
LEFT JOIN category_translation ct ON c.id = ct.category_id
LEFT JOIN product_category pc ON c.id = pc.category_id
WHERE pc.category_id IS NULL
AND c.type = 'page'
LIMIT 1000
SQL;

        $categories = $this->connection->fetchAllAssociative($sql);

        if (!$dryRun && !empty($categories)) {
            $ids = array_map(function ($category) {
                return ['id' => $category['id']];
            }, $categories);
            
            $this->categoryRepository->delete($ids, $context);
        }

        return [
            'count' => count($categories),
            'sample' => array_slice($categories, 0, 5)
        ];
    }

    private function cleanupCategoriesWithNoSales(int $months, bool $dryRun, Context $context): array
    {
        $date = new \DateTime();
        $date->modify("-{$months} months");

        $sql = <<<SQL
SELECT DISTINCT c.id, ct.name
FROM category c
LEFT JOIN category_translation ct ON c.id = ct.category_id
LEFT JOIN product_category pc ON c.id = pc.category_id
LEFT JOIN product p ON pc.product_id = p.id
LEFT JOIN order_line_item oli ON p.id = oli.product_id
LEFT JOIN `order` o ON oli.order_id = o.id AND o.created_at > :date
WHERE c.type = 'page'
AND o.id IS NULL
LIMIT 1000
SQL;

        $categories = $this->connection->fetchAllAssociative($sql, [
            'date' => $date->format('Y-m-d H:i:s')
        ]);

        if (!$dryRun && !empty($categories)) {
            $ids = array_map(function ($category) {
                return ['id' => $category['id']];
            }, $categories);
            
            $this->categoryRepository->delete($ids, $context);
        }

        return [
            'count' => count($categories),
            'sample' => array_slice($categories, 0, 5)
        ];
    }

    public function getName(): string
    {
        return 'Category Cleanup';
    }
}
