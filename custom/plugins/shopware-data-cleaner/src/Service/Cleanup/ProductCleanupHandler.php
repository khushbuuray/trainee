<?php

declare(strict_types=1);

namespace IctDataCleanerPro\Service\Cleanup;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\NotFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\OrFilter;
use IctDataCleanerPro\Service\CleanupLoggerService;


class ProductCleanupHandler implements CleanupHandlerInterface
{
    private EntityRepository $productRepository;
    private CleanupLoggerService $logger;


    public function __construct(
        EntityRepository $productRepository,
        CleanupLoggerService $logger
    ) {
        $this->productRepository = $productRepository;
        $this->logger = $logger;
    }

    public function cleanup(array $config, bool $dryRun, Context $context): array
    {

        $results = [
            'name' => $this->getName(),
            'items' => []
        ];

        if (isset($config['productCleanup.monthsNotSold'])) {
            $results['items']['products_not_sold_months'] = $this->cleanupProductsNotSold(
                (int) $config['productCleanup.monthsNotSold'],
                $dryRun,
                $context
            );
        }

        if (isset($config['productCleanup.deleteNeverSold']) && $config['productCleanup.deleteNeverSold']) {
            $results['items']['products_never_sold'] = $this->cleanupProductsNeverSold($dryRun, $context);
        }

        if (isset($config['productCleanup.monthsDisabled'])) {
            $results['items']['inactive_products'] = $this->cleanupInactiveProducts(
                (int) $config['productCleanup.monthsDisabled'],
                $dryRun,
                $context
            );
        }

        // if (isset($config['productVariantCleanup.zeroStockMonths'])) {
        //     $results['items']['zero_stock_variants'] = $this->cleanupZeroStockVariants(
        //         (int) $config['productVariantCleanup.zeroStockMonths'],
        //         $dryRun,
        //         $context
        //     );
        // }

        return $results;
    }

    private function cleanupProductsNotSold(int $months, bool $dryRun, Context $context): array
    {
        $date = (new \DateTime())->modify("-{$months} months");

        $criteria = new Criteria();
        $criteria->addFilter(new RangeFilter('createdAt', [RangeFilter::LTE => $date->format(DATE_ATOM)]));
        $criteria->addFilter(new EqualsFilter('parentId', null)); // root products only
        $criteria->addAssociation('orderLineItems.order');
        $criteria->addFilter(new NotFilter(NotFilter::CONNECTION_AND, [
            new RangeFilter('orderLineItems.order.createdAt', [
                RangeFilter::GT => $date->format(DATE_ATOM)
            ])
        ]));
        $criteria->setLimit(1000);

        $products = $this->productRepository->search($criteria, $context);

        $ids = [];
        foreach ($products->getEntities() as $product) {
            $ids[] = ['id' => $product->getId()];
        }

        try {
            if (!$dryRun && !empty($ids)) {
                $this->productRepository->delete($ids, $context);

                $this->logger->logSuccess('product', [
                    'action' => 'products_not_sold_months',
                    'deleted_ids' => array_column($ids, 'id'),
                    'count' => count($ids),
                ]);
            }
        } catch (\Exception $e) {
            $this->logger->logError('product', $e, [
                'action' => 'products_not_sold_months',
                'attempted_ids' => array_column($ids, 'id'),
            ]);
        }

        $sample = [];
        foreach ($products->getElements() as $product) {
            $sample[] = [
                'id' => $product->getId(),
                'product_number' => $product->getProductNumber(),
                'name' => $product->getTranslated()['name'] ?? 'N/A',
            ];
        }

        return [
            'count' => $products->count(),
            'sample' => $sample,
        ];
    }

    private function cleanupProductsNeverSold(bool $dryRun, Context $context): array
    {
        $criteria = new Criteria();
        $criteria->addAssociation('orderLineItems');
        $criteria->addFilter(new EqualsFilter('orderLineItems.id', null));
        $criteria->addFilter(new EqualsFilter('parentId', null));
        $criteria->setLimit(1000);

        $products = $this->productRepository->search($criteria, $context);

        $ids = [];
        foreach ($products->getEntities() as $product) {
            $ids[] = ['id' => $product->getId()];
        }

        if (!$dryRun && !empty($ids)) {
            try {
                $this->productRepository->delete($ids, $context);

                // Log success
                $this->logger->logSuccess('product', [
                    'action' => 'delete',
                    'count' => count($ids),
                    'ids' => array_column($ids, 'id'),
                ]);
            } catch (\Exception $e) {
                // Log error
                $this->logger->logError('product', $e, [
                    'action' => 'delete',
                    'ids' => array_column($ids, 'id'),
                ]);
            }
        }


        $sample = [];
        foreach ($products->getElements() as $product) {
            $sample[] = [
                'id' => $product->getId(),
                'product_number' => $product->getProductNumber(),
                'name' => $product->getTranslated()['name'] ?? 'N/A',
            ];
        }

        return [
            'count' => $products->count(),
            'sample' => $sample,
        ];
    }

    private function cleanupInactiveProducts(int $months, bool $dryRun, Context $context): array
    {
        $date = (new \DateTime())->modify("-{$months} months");

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('active', false));
        $criteria->addFilter(new OrFilter([
            new RangeFilter('updatedAt', [RangeFilter::LTE => $date->format(DATE_ATOM)]),
            new EqualsFilter('updatedAt', null)
        ]));
        $criteria->setLimit(1000);

        $products = $this->productRepository->search($criteria, $context);

        $ids = [];
        foreach ($products->getEntities() as $product) {
            $ids[] = ['id' => $product->getId()];
        }


        if (!$dryRun && !empty($ids)) {
            try {
                $this->productRepository->delete($ids, $context);

                // Log success
                $this->logger->logSuccess('product', [
                    'action' => 'delete',
                    'count' => count($ids),
                    'ids' => array_column($ids, 'id'),
                ]);
            } catch (\Exception $e) {
                // Log error
                $this->logger->logError('product', $e, [
                    'action' => 'delete',
                    'ids' => array_column($ids, 'id'),
                ]);
            }
        }

        $sample = [];
        foreach ($products->getElements() as $product) {
            $sample[] = [
                'id' => $product->getId(),
                'product_number' => $product->getProductNumber(),
                'name' => $product->getTranslated()['name'] ?? 'N/A',
            ];
        }

        return [
            'count' => $products->count(),
            'sample' => $sample,
        ];
    }

    // private function cleanupZeroStockVariants(int $months, bool $dryRun, Context $context): array
    // {
    //     $date = (new \DateTime())->modify("-{$months} months");

    //     $criteria = new Criteria();
    //     $criteria->addFilter(new EqualsFilter('stock', 0));
    //     $criteria->addFilter(new RangeFilter('createdAt', [RangeFilter::LTE => $date->format(DATE_ATOM)]));
    //     $criteria->addFilter(new NotFilter(NotFilter::CONNECTION_AND, [
    //         new RangeFilter('orderLineItems.order.createdAt', [
    //             RangeFilter::GT => $date->format(DATE_ATOM)
    //         ])
    //     ]));
    //     $criteria->addFilter(new NotFilter(NotFilter::CONNECTION_AND, [
    //         new EqualsFilter('parentId', null)
    //     ])); // Variants only (exclude parent)

    //     $criteria->addAssociation('orderLineItems.order');
    //     $criteria->setLimit(1000);

    //     $products = $this->productRepository->search($criteria, $context);

    //     if (!$dryRun && $products->count() > 0) {
    //         $ids = array_map(fn($p) => ['id' => $p->getId()], $products->getElements());
    //         $this->productRepository->delete($ids, $context);
    //     }

    //     $sample = [];
    //     foreach ($products->getElements() as $product) {
    //         $sample[] = [
    //             'id' => $product->getId(),
    //             'product_number' => $product->getProductNumber(),
    //             'name' => $product->getTranslated()['name'] ?? 'N/A',
    //             'stock' => $product->getStock(),
    //         ];
    //     }

    //     return [
    //         'count' => $products->count(),
    //         'sample' => $sample,
    //     ];
    // }

    public function getName(): string
    {
        return 'Product Cleanup';
    }

    // Optional CLI Interface if needed
    // protected function execute(InputInterface $input, OutputInterface $output): int
    // {
    //     $context = Context::createDefaultContext();
    //     $dryRun = $input->getOption('dry-run');

    //     if ($input->getOption('neversoldinmonths')) {
    //         $months = (int) $input->getOption('neversoldinmonths');
    //         $result = $this->cleanupProductsNotSold($months, $dryRun, $context);
    //         $output->writeln("Not Sold in $months Months: " . json_encode($result, JSON_PRETTY_PRINT));
    //     } elseif ($input->getOption('productneversold')) {
    //         $result = $this->cleanupProductsNeverSold($dryRun, $context);
    //         $output->writeln("Never Sold: " . json_encode($result, JSON_PRETTY_PRINT));
    //     } elseif ($input->getOption('inactiveproductsolderthanmonths')) {
    //         $months = (int) $input->getOption('inactiveproductsolderthanmonths');
    //         $result = $this->cleanupInactiveProducts($months, $dryRun, $context);
    //         $output->writeln("Inactive Products older than $months months: " . json_encode($result, JSON_PRETTY_PRINT));
    //     } else {
    //         $output->writeln("<error>No cleanup option provided. Use one of:</error>");
    //         $output->writeln("  --neversoldinmonths=<months>");
    //         $output->writeln("  --productneversold");
    //         $output->writeln("  --inactiveproductsolderthanmonths=<months>");
    //         return Command::INVALID;
    //     }

    //     return Command::SUCCESS;
    // }
}
