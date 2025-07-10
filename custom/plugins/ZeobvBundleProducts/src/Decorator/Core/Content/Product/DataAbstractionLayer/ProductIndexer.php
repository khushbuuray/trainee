<?php

declare(strict_types=1);

namespace Zeobv\BundleProducts\Decorator\Core\Content\Product\DataAbstractionLayer;

use Doctrine\DBAL\Connection;
use ReflectionMethod;
use Shopware\Core\Content\Product\DataAbstractionLayer\ProductIndexingMessage;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Doctrine\RetryableQuery;
use Shopware\Core\Framework\DataAbstractionLayer\Doctrine\RetryableTransaction;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Indexing\EntityIndexer;
use Shopware\Core\Framework\DataAbstractionLayer\Indexing\EntityIndexingMessage;
use Shopware\Core\Framework\DataAbstractionLayer\Indexing\InheritanceUpdater;
use Shopware\Core\Content\Product\DataAbstractionLayer\ProductIndexer as SwagProductIndexer;
use Zeobv\BundleProducts\Core\Subscriber\BundleProductStockUpdater;

class ProductIndexer extends EntityIndexer
{
    public function __construct(
        private EntityIndexer $decorated,
        private Connection $connection,
        private InheritanceUpdater $inheritanceUpdater,
        private BundleProductStockUpdater $stockUpdater
    ) {
    }

    public function update(EntityWrittenContainerEvent $event): ?EntityIndexingMessage
    {
        $updates = $event->getPrimaryKeys(ProductDefinition::ENTITY_NAME);

        if (empty($updates)) {
            return null;
        }

        $this->inheritanceUpdater->update(ProductDefinition::ENTITY_NAME, $updates, $event->getContext());

        $this->stockUpdater->update($updates, $event->getContext());

        return new ProductIndexingMessage(array_values($updates), null, $event->getContext());
    }

    public function handle(EntityIndexingMessage $message): void
    {
        $this->decorated->handle(...func_get_args());

        $ids = $message->getData();
        $ids = array_unique(array_filter($ids));

        if (empty($ids)) {
            return;
        }

        $context = $message->getContext();

        if ($this->shouldApplyFallbackFor641()) {
            $this->stockUpdater->update($ids, $context);
        } else {
            RetryableTransaction::retryable($this->connection, function () use ($message, $ids, $context): void {
                if ($message->allow(SwagProductIndexer::STOCK_UPDATER)) {
                    $this->stockUpdater->update($ids, $context);
                }
            });
        }
    }

    public function getName(): string
    {
        return $this->decorated->getName();
    }

    /**
     * @deprecated tag:v6.5.0 The parameter $offset will be native typed
     */
    public function iterate(/*?array */ $offset): ?EntityIndexingMessage
    {
        return $this->decorated->iterate(...func_get_args());
    }

    public function getTotal(): int
    {
        return $this->decorated->getTotal();
    }

    public function getDecorated(): EntityIndexer
    {
        return $this->decorated;
    }

    public function getOptions(): array
    {
        return $this->decorated->getOptions();
    }

    private function shouldApplyFallbackFor641(): bool
    {
        $method = new ReflectionMethod(RetryableQuery::class, 'retryable');
        return $method->getNumberOfParameters() < 2;
    }
}
