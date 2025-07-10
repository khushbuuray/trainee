<?php

declare(strict_types=1);

namespace Zeobv\BundleProducts\MessageBus\Handler;

use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Profiling\Profiler;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Content\Product\Events\ProductIndexerEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Doctrine\RetryableQuery;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Zeobv\BundleProducts\MessageBus\Message\BundleStockUpdateMessage;
use Zeobv\BundleProducts\Core\Content\Product\Aggregate\ProductBundleConnection\ProductBundleConnectionEntity;

#[AsMessageHandler]
final class BundleStockUpdateHandler
{
    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly EntityRepository $bundleConnectionRepository,
        private readonly EntityRepository $productRepository,
        private readonly Connection $connection,
        private readonly LoggerInterface $logger
    ) {
    }

    public function __invoke(BundleStockUpdateMessage $message): void
    {
        $this->logger->info(
            'BundleStockUpdateHandler invoked with productId: ' . json_encode($message->getProductIds())
        );

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsAnyFilter('productId', $message->getProductIds()));

        $context = Context::createDefaultContext();
        $result = $this->bundleConnectionRepository->search(
            $criteria,
            $context
        );

        if ($result->getTotal() === 0) {
            return;
        }

        /** @var array<ProductBundleConnectionEntity> $elements */
        $elements = $result->getElements();
        $productIds = array_unique(
            array_values(
                array_map(
                    static function (ProductBundleConnectionEntity $bundleConnectionEntity) {
                        return $bundleConnectionEntity->getBundleProductId();
                    },
                    $elements
                )
            )
        );

        $result = $this->productRepository->search(
            new Criteria($productIds),
            $context
        );

        if ($result->getIds() === []) {
            return;
        }

        /** @var ProductEntity $productEntity */
        foreach ($result->getEntities() as $productEntity) {
            $query = $this->connection->prepare(
                'UPDATE product SET available = :available, available_stock = :available_stock, stock = :stock, is_closeout = :is_closeout, updated_at = :now WHERE id = :id'
            );

            if ($this->shouldApplyFallbackFor641()) {
                $update = new RetryableQuery(
                    $this->connection,
                    $query
                );
            } else {
                $update = new RetryableQuery(
                    $this->connection,
                    $query
                );
            }

            $update->execute([
                'id' => Uuid::fromHexToBytes((string) $productEntity->getId()),
                'available' => $productEntity->getAvailable() ? 1 : 0,
                'available_stock' => $productEntity->getAvailableStock(),
                'is_closeout' => $productEntity->getIsCloseout() ? 1 : 0,
                'stock' => $productEntity->getStock(),
                'now' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
            ]);
        }

        Profiler::trace('product:indexer:event', function () use ($result, $context): void {
            $this->eventDispatcher->dispatch(
                new ProductIndexerEvent(
                    array_values($result->getIds()),
                    $context,
                    []
                )
            );
        });
    }

    private function shouldApplyFallbackFor641(): bool
    {
        $method = new \ReflectionMethod(RetryableQuery::class, 'retryable');
        return $method->getNumberOfParameters() < 2;
    }
}
