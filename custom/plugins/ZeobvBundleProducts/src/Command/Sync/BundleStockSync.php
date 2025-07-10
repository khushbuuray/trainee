<?php

declare(strict_types=1);

namespace Zeobv\BundleProducts\Command\Sync;

use Generator;
use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Zeobv\BundleProducts\MessageBus\Handler\BundleStockUpdateHandler;
use Zeobv\BundleProducts\MessageBus\Message\BundleStockUpdateMessage;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;

class BundleStockSync extends Command
{
    public function __construct(
        private readonly Connection $connection,
        private readonly BundleStockUpdateHandler $bundleStockUpdateHandler,
        private readonly EntityRepository|null $pickwareProductRepository = null,
        $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setName('zeo:bundle:stock:sync');
        $this->setDescription('Sync the calculated stock for bundle products to the database.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $bundleProductResult = $this->getBundleProducts();
        $count = 0;

        foreach ($bundleProductResult as $batch) {
            if (empty($batch)) {
                if ($count === 0) {
                    $output->writeln('No bundle products found.');
                    return Command::SUCCESS;
                }
                break;
            }

            if ($this->pickwareProductRepository) {
                $output->writeln(sprintf('Disabling Pickware stock management for %d bundle products.', count($batch)));
                $this->disablePickwareStockManagementForBundleProducts(
                    array_map(static function (array $batch): string {
                        return Uuid::fromBytesToHex($batch['bundle_product_id']);
                    }, $batch)
                );
            }

            $count += count($batch);
            $output->writeln(sprintf('Processing batch of %d bundle products.', count($batch)));

            $productIds = array_map(static function (array $batch) {
                $productIds = explode(',', $batch['product_ids']);
                return current($productIds);
            }, $batch);

            $this->bundleStockUpdateHandler->__invoke(new BundleStockUpdateMessage($productIds));
        }

        $output->writeln(sprintf('Processed total of %d bundle products.', $count));
        return Command::SUCCESS;
    }

    private function disablePickwareStockManagementForBundleProducts(array $bundleProductIds): void
    {
        $context = Context::createDefaultContext();

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsAnyFilter('productId', $bundleProductIds));
        $results = $this->pickwareProductRepository->search($criteria, $context);

        if ($results->count() < 1) {
            return;
        }

        $data = array_values($results->getEntities()->map(static function ($pickwareProduct) {
            /** @var \Pickware\PickwareErpStarter\Product\Model\PickwareProductEntity */
            return [
                'id' => $pickwareProduct->getId(),
                'productId' => $pickwareProduct->getProductId(),
                'isStockManagementDisabled' => true,
            ];
        }));

        $this->pickwareProductRepository->upsert($data, $context);
    }

    private function getBundleProducts(): Generator
    {
        $offset = 0;
        $batchSize = 100;

        while (true) {
            $sql = <<<SQL
                SELECT bundle_product_id, GROUP_CONCAT(HEX(product_id) SEPARATOR ',') as product_ids
                FROM zeobv_product_bundle_connection
                GROUP BY bundle_product_id
                LIMIT {$batchSize}
                OFFSET {$offset}
            SQL;

            $result = $this->connection->fetchAllAssociative($sql);

            yield $result;

            if (count($result) < $batchSize) {
                break;
            }

            $offset += $batchSize;
        }
    }
}
