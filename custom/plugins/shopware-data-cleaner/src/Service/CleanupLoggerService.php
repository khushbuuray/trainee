<?php declare(strict_types=1);

namespace IctDataCleanerPro\Service;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Uuid\Uuid;
use Psr\Log\LoggerInterface;

class CleanupLoggerService
{
    private EntityRepository $cleanupLogRepository;
    private LoggerInterface $logger;

    public function __construct(
        EntityRepository $cleanupLogRepository,
        LoggerInterface $logger
    ) {
        $this->cleanupLogRepository = $cleanupLogRepository;
        $this->logger = $logger;
    }

    public function logCleanupRun(
        string $trigger,
        string $mode,
        array $config,
        array $results,
        Context $context
    ): void {
        $data = [
            'id' => Uuid::randomHex(),
            'runAt' => new \DateTime(),
            'trigger' => $trigger,
            'mode' => $mode,
            'configSnapshot' => $config,
            'results' => $this->formatResults($results)
        ];

        try {
            $this->cleanupLogRepository->create([$data], $context);
            
            $this->logger->info('Data cleanup completed', [
                'trigger' => $trigger,
                'mode' => $mode,
                'totalItems' => $this->countTotalItems($results)
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to log cleanup run', [
                'error' => $e->getMessage()
            ]);
        }
    }

    private function formatResults(array $results): array
    {
        $formatted = [];
        
        foreach ($results as $handlerClass => $handlerResults) {
            $formatted[] = [
                'handler' => $handlerResults['name'] ?? $handlerClass,
                'items' => $handlerResults['items'] ?? []
            ];
        }
        
        return $formatted;
    }

    private function countTotalItems(array $results): int
    {
        $total = 0;
        
        foreach ($results as $handlerResults) {
            if (isset($handlerResults['items'])) {
                foreach ($handlerResults['items'] as $itemResults) {
                    $total += $itemResults['count'] ?? 0;
                }
            }
        }
        
        return $total;
    }
}
