<?php

declare(strict_types=1);

namespace Zeobv\BundleProducts\Core\Subscriber;

use Throwable;
use Psr\Log\LoggerInterface;
use Shopware\Core\Checkout\Order\OrderDefinition;
use Zeobv\BundleProducts\Service\OrderEnrichmentService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Shopware\Core\Framework\DataAbstractionLayer\EntityWriteResult;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;

/**
 * Shopware doesn't create order_delivery_positions for child line items by default. This subscriber aims to fix that.
 */
class OrderWrittenSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly OrderEnrichmentService $orderEnrichmentService,
        private readonly LoggerInterface $logger
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'order.written' => 'onOrderWritten',
        ];
    }

    public function onOrderWritten(EntityWrittenEvent $event): void
    {
        $context = $event->getContext();

        foreach ($event->getWriteResults() as $writeResult) {
            if ($writeResult->getOperation() !== EntityWriteResult::OPERATION_INSERT || $writeResult->getEntityName() !== OrderDefinition::ENTITY_NAME) {
                continue;
            }

            try {
                $orderId = $writeResult->getPrimaryKey();

                if (is_array($orderId)) {
                    $this->logger->error('Unexpected primary key received from write result. Skipping order delivery position creation. Json encoded primary key: ' . json_encode($writeResult->getPrimaryKey()));
                    continue;
                }

                $this->orderEnrichmentService->enrich($orderId, $context);
            } catch (Throwable $e) {
                $this->logger->error($e->getMessage());
            }
        }
    }
}
