<?php

declare(strict_types=1);

namespace IctDataCleanerPro\Service;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Uuid\Uuid;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class CleanupLoggerService
{
    private EntityRepository $cleanupLogRepository;
    private LoggerInterface $logger;
    private string $logBaseDir;

    public function __construct(
        EntityRepository $cleanupLogRepository,
        LoggerInterface $logger,
        KernelInterface $kernel
    ) {
        $this->cleanupLogRepository = $cleanupLogRepository;
        $this->logger = $logger;
        $this->logBaseDir = $kernel->getLogDir() . '/ict-data-cleaner';

       if (!is_dir($this->logBaseDir)) {
        if (!@mkdir($this->logBaseDir, 0775, true) && !is_dir($this->logBaseDir)) {
            $this->logger->warning('Could not create log directory', [
                'path' => $this->logBaseDir
            ]);
            $this->logBaseDir = $kernel->getLogDir(); // fallback to standard log dir
        }
    }
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

public function logToFile(string $module, string $level, array $data): void
{
    $timestamp = (new \DateTime())->format('Y-m-d H:i:s');
    $filePath = sprintf('%s/%s.log', $this->logBaseDir, strtolower($module));
    $entry = sprintf("[%s] [%s] %s\n", $timestamp, strtoupper($level), json_encode($data, JSON_UNESCAPED_UNICODE));

    try {
        // Check if the directory exists and is writable
        if (!is_dir(dirname($filePath)) || !is_writable(dirname($filePath))) {
            throw new \RuntimeException("Log directory is not writable: " . dirname($filePath));
        }

        // Attempt to write the log entry
        if (@file_put_contents($filePath, $entry, FILE_APPEND) === false) {
            throw new \RuntimeException("Failed to write to log file: {$filePath}");
        }
    } catch (\Throwable $e) {
        // Fallback to Shopware logger
        $this->logger->error('Plugin file logging failed', [
            'module' => $module,
            'file' => $filePath,
            'error' => $e->getMessage(),
            'fallbackEntry' => $entry,
        ]);
    }
}

    public function logSuccess(string $module, array $data): void
    {
        $this->logToFile($module, 'info', ['status' => 'success'] + $data);
    }

    public function logError(string $module, \Throwable $e, array $context = []): void
    {
        $this->logToFile($module, 'error', [
            'message' => $e->getMessage(),
            'context' => $context,
            'trace' => $e->getTraceAsString(),
        ]);
    }
}
