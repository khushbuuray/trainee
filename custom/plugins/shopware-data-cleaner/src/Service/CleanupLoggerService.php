<?php

declare(strict_types=1);

namespace IctDataCleanerPro\Service;

use IctDataCleanerPro\Core\Content\CleanupLog\CleanupLogCollection;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Uuid\Uuid;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Logs cleanup actions to database and file
 */
class CleanupLoggerService
{
    /** @var EntityRepository<CleanupLogCollection> */
    private readonly EntityRepository $cleanupLogRepository;

    private readonly LoggerInterface $logger;
    private readonly string $logBaseDir;

    /**
     * @param EntityRepository<CleanupLogCollection> $cleanupLogRepository
     */
    public function __construct(
        EntityRepository $cleanupLogRepository,
        LoggerInterface $logger,
        KernelInterface $kernel
    ) {
        $this->cleanupLogRepository = $cleanupLogRepository;
        $this->logger = $logger;

        $baseDir = $kernel->getLogDir() . '/ict-data-cleaner';

        if (!is_dir($baseDir)) {
            if (!@mkdir($baseDir, 0775, true) && !is_dir($baseDir)) {
                $this->logger->warning('Could not create log directory', [
                    'path' => $baseDir,
                ]);

                $baseDir = $kernel->getLogDir(); // fallback
            }
        }

        $this->logBaseDir = $baseDir;
    }

    /**
     * @param array<string, mixed> $config
     * @param array<string, array{name: string, items: array<string, array{count: int, sample: list<array{id: string, name: string}>}>}> $results
     */
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
        } catch (\Throwable $e) {
            $this->logger->error('Failed to log cleanup run', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * @param array<string, array{name: string, items: array<string, array{count: int, sample: list<array{id: string, name: string}>}>}> $results
     * @return list<array{handler: string, items: array<string, array{count: int, sample: list<array{id: string, name: string}>}>}>
     */
    private function formatResults(array $results): array
    {
        $formatted = [];

        foreach ($results as $handlerClass => $handlerResults) {
            $formatted[] = [
                'handler' => $handlerResults['name'],
                'items' => $handlerResults['items']
            ];
        }

        return $formatted;
    }

    /**
     * @param array<string, array{name: string, items: array<string, array{count: int, sample: list<array{id: string, name: string}>}>}> $results
     */
    private function countTotalItems(array $results): int
    {
        $total = 0;

        foreach ($results as $handlerResults) {
            foreach ($handlerResults['items'] as $itemResults) {
                $total += $itemResults['count'];
            }
        }

        return $total;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function logToFile(string $module, string $level, array $data): void
    {
        $timestamp = (new \DateTime())->format('Y-m-d H:i:s');
        $filePath = sprintf('%s/%s.log', $this->logBaseDir, strtolower($module));
        $entry = sprintf("[%s] [%s] %s\n", $timestamp, strtoupper($level), json_encode($data, JSON_UNESCAPED_UNICODE));

        try {
            if (!is_dir(dirname($filePath)) || !is_writable(dirname($filePath))) {
                throw new \RuntimeException("Log directory is not writable: " . dirname($filePath));
            }

            if (@file_put_contents($filePath, $entry, FILE_APPEND) === false) {
                throw new \RuntimeException("Failed to write to log file: {$filePath}");
            }
        } catch (\Throwable $e) {
            $this->logger->error('Plugin file logging failed', [
                'module' => $module,
                'file' => $filePath,
                'error' => $e->getMessage(),
                'fallbackEntry' => $entry,
            ]);
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    public function logSuccess(string $module, array $data): void
    {
        $this->logToFile($module, 'info', ['status' => 'success'] + $data);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function logError(string $module, \Throwable $e, array $context = []): void
    {
        $this->logToFile($module, 'error', [
            'message' => $e->getMessage(),
            'context' => $context,
            'trace' => $e->getTraceAsString(),
        ]);
    }
}
