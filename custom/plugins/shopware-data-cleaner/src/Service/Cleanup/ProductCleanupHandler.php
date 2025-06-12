<?php declare(strict_types=1);

namespace IctDataCleanerPro\Service\Cleanup;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\NotFilter;
use Shopware\Core\Framework\Uuid\Uuid;

class ProductCleanupHandler implements CleanupHandlerInterface
{
    private EntityRepository $productRepository;
    private Connection $connection;

    public function __construct(
        EntityRepository $productRepository,
        Connection $connection
    ) {
        $this->productRepository = $productRepository;
        $this->connection = $connection;
    }

    public function cleanup(array $config, bool $dryRun, Context $context): array
    {

        $results = [
            'name' => $this->getName(),
            'items' => []
        ];

        // Clean products not sold in X months
        if (isset($config['productCleanup.monthsNotSold'])) {
            $notSoldResults = $this->cleanupProductsNotSold(
                (int) $config['productCleanup.monthsNotSold'],
                $dryRun,
                $context
            );
            $results['items']['products_not_sold_months'] = $notSoldResults;
        }

        // Clean products never sold
        // if ($config['productCleanup.deleteNeverSold'] ?? false) {
        if (isset($config['productCleanup.deleteNeverSold']) && $config['productCleanup.deleteNeverSold'] == false) {
            $neverSoldResults = $this->cleanupProductsNeverSold($dryRun, $context);
            $results['items']['products_never_sold'] = $neverSoldResults;
        }

        // Clean inactive products
        if (isset($config['productCleanup.monthsDisabled'])) {
            $inactiveResults = $this->cleanupInactiveProducts(
                (int) $config['productCleanup.monthsDisabled'],
                $dryRun,
                $context
            );
            $results['items']['inactive_products'] = $inactiveResults;
        }

        // Clean product variants with zero stock
        if (isset($config['productVariantCleanup.zeroStockMonths'])) {
            $variantResults = $this->cleanupZeroStockVariants(
                (int) $config['productVariantCleanup.zeroStockMonths'],
                $dryRun,
                $context
            );
            $results['items']['zero_stock_variants'] = $variantResults;
        }
        return $results;
    }

    private function cleanupProductsNotSold(int $months, bool $dryRun, Context $context): array
    {
        $date = new \DateTime();
        $date->modify("-{$months} months");

        $sql = <<<SQL
SELECT DISTINCT p.id, p.product_number, pt.name
FROM product p
LEFT JOIN product_translation pt ON p.id = pt.product_id AND pt.language_id = :languageId
LEFT JOIN order_line_item oli ON p.id = oli.product_id AND oli.type = 'product'
LEFT JOIN `order` o ON oli.order_id = o.id AND o.created_at > :date
WHERE o.id IS NULL
AND p.created_at < :date
LIMIT 1000
SQL;
        $products = $this->connection->fetchAllAssociative($sql, [
            'date' => $date->format('Y-m-d H:i:s'),
            'languageId' => Uuid::fromHexToBytes($context->getLanguageId())
        ]);


        $products = array_map(function ($product) {
           return [
               'id' => Uuid::fromBytesToHex($product['id']),
               'product_number' => $product['product_number'],
               'name' => $product['name'],
           ];
        }, $products);


        if (!$dryRun && !empty($products)) {
            $ids = array_map(function ($product) {
                return ['id' => Uuid::fromHexToBytes($product['id'])];
            }, $products);
            
            // $this->productRepository->delete($ids, $context);
        }
        return [
            'count' => count($products),
            'sample' => $products
        ];
    }

    private function cleanupProductsNeverSold(bool $dryRun, Context $context): array
    {
        $sql = <<<SQL
SELECT p.id, p.product_number, pt.name
FROM product p
LEFT JOIN product_translation pt ON p.id = pt.product_id AND pt.language_id = :languageId
LEFT JOIN order_line_item oli ON p.id = oli.product_id AND oli.type = 'product'
WHERE oli.id IS NULL 
LIMIT 1000
-- SELECT p.id, p.product_number, pt.name
-- FROM product p
-- LEFT JOIN product_translation pt 
--     ON p.id = pt.product_id AND pt.language_id = :languageId
-- LEFT JOIN order_line_item oli 
--     ON p.id = oli.product_id AND oli.type = 'product'
-- WHERE p.parent_id IS NULL
--   AND oli.id IS NULL 
-- LIMIT 1000

SQL;

        $products = $this->connection->fetchAllAssociative($sql, [
            'languageId' => Uuid::fromHexToBytes($context->getLanguageId())
        ]);

         // Convert binary UUIDs to hex for JSON safety
        $products = array_map(function ($product) {
           return [
               'id' => Uuid::fromBytesToHex($product['id']),
               'product_number' => $product['product_number'],
               'name' => $product['name'],
           ];
        }, $products);


        if (!$dryRun && !empty($products)) {
            $ids = array_map(function ($product) {
                return ['id' => Uuid::fromHexToBytes($product['id'])];
            }, $products);
            
            // $this->productRepository->delete($ids, $context);
        }
        return [
            'count' => count($products),
            // 'sample' => array_slice($products, 0, 10)
            'sample' => $products

        ];
    }

    private function cleanupInactiveProducts(int $months, bool $dryRun, Context $context): array
    {
        $date = new \DateTime();
        $date->modify("-{$months} months");

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('active', false));
        $criteria->addFilter(new RangeFilter('updatedAt', [
            RangeFilter::LTE => $date->format('Y-m-d H:i:s')
        ]));
        $criteria->setLimit(1000);

        $products = $this->productRepository->search($criteria, $context);
        if (!$dryRun && $products->count() > 0) {
            $ids = array_map(function ($product) {
                return ['id' => $product->getId()];
            }, $products->getElements());
            
            // $this->productRepository->delete($ids, $context);
        }

        $sample = [];
        foreach
        ($products->getElements() as $product) {
            $sample[] = [
                'id' => $product->getId(),
                'product_number' => $product->getProductNumber(),
                'name' => $product->getTranslated()['name'] ?? 'N/A'
            ];
        }
        return [
            'count' => $products->count(),
            'sample' => $sample
        ];
    }

    private function cleanupZeroStockVariants(int $months, bool $dryRun, Context $context): array
    {
        $date = new \DateTime();
        $date->modify("-{$months} months");

        $sql = <<<SQL
SELECT DISTINCT p.id, p.product_number, pt.name, p.stock
FROM product p
LEFT JOIN product_translation pt ON p.id = pt.product_id AND pt.language_id = :languageId
LEFT JOIN order_line_item oli ON p.id = oli.product_id AND oli.type = 'product'
LEFT JOIN `order` o ON oli.order_id = o.id AND o.created_at > :date
WHERE p.parent_id IS NOT NULL
AND p.stock = 0
AND p.created_at < :date
AND o.id IS NULL
LIMIT 1000
SQL;

        $variants = $this->connection->fetchAllAssociative($sql, [
            'date' => $date->format('Y-m-d H:i:s'),
            'languageId' => Uuid::fromHexToBytes($context->getLanguageId())
        ]);

        if (!$dryRun && !empty($variants)) {
            $ids = array_map(function ($variant) {
                return ['id' => $variant['id']];
            }, $variants);
            
            $this->productRepository->delete($ids, $context);
        }

        return [
            'count' => count($variants),
            'sample' => array_slice($variants, 0, 5)
        ];
    }

    public function getName(): string
    {
        return 'Product Cleanup';
    }
}
