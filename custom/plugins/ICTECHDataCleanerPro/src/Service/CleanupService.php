<?php

declare(strict_types=1);

namespace ICTECHDataCleanerPro\Service;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Context;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use ICTECHDataCleanerPro\Service\Cleanup\CleanupHandlerInterface;

/**
 * @phpstan-type ConfigArray array<string, mixed>
 * @phpstan-type CleanupResult array<string, array{name: string, items: array<string, array{count: int, sample: list<array{id: string, name: string}>}>}>
 */
class CleanupService
{
    private Connection $connection;
    private SystemConfigService $systemConfigService;
    private CleanupLoggerService $loggerService;

    /** @var iterable<CleanupHandlerInterface> */
    private iterable $cleanupHandlers;

    public function __construct(
        Connection $connection,
        SystemConfigService $systemConfigService,
        CleanupLoggerService $loggerService,
        iterable $cleanupHandlers
    ) {
        $this->connection = $connection;
        $this->systemConfigService = $systemConfigService;
        $this->loggerService = $loggerService;
        $this->cleanupHandlers = $cleanupHandlers;
    }

    /**
     * @return CleanupResult
     */
    public function runCleanup(Context $context, bool $dryRun = null, string $trigger = 'manual', ?string $module = null): array
    {
        $config = $this->getConfig();
        $isDryRun = (bool) ($dryRun ?? $config['dryRunMode'] ?? true);
        $formattedConfig = $this->formatConfig($config);

        /** @var CleanupResult $results */
        $results = [];

        $this->connection->beginTransaction();
        try {
            foreach ($this->cleanupHandlers as $handler) {
                $handlerClass = get_class($handler);
                $key = $handler->getKey();

                if ($module !== null && $module !== $handler->getKey()) {
                    continue;
                }

                echo "Running cleanup for handler: {$key}" . PHP_EOL;

                $handlerResults = $handler->cleanup($formattedConfig, $isDryRun, $context);
                $results[$key] = $handlerResults;

                echo json_encode($handlerResults, JSON_PRETTY_PRINT) . PHP_EOL;
            }

            if ($isDryRun) {
                $this->connection->rollBack();
            } else {
                $this->connection->commit();
            }

            $this->loggerService->logCleanupRun(
                $trigger,
                $isDryRun ? 'dry-run' : 'real',
                $config,
                $results,
                $context
            );
        } catch (\Exception $e) {
            $this->connection->rollBack();
            throw $e;
        }

        return $results;
    }

    /**
     * @return CleanupResult
     */
    public function previewCleanup(Context $context): array
    {
        return $this->runCleanup($context, true, 'preview');
    }

    /**
     * @return ConfigArray
     */
    private function getConfig(): array
    {
        /** @var ConfigArray $config */
        $config = $this->systemConfigService->getDomain('ICTECHDataCleanerPro.config');
        $cleanConfig = [];

        foreach ($config as $key => $value) {
            $cleanKey = str_replace('ICTECHDataCleanerPro.config.', '', $key);
            $cleanConfig[$cleanKey] = $value;
        }

        return $cleanConfig;
    }

    /**
     * @param ConfigArray $config
     * @return ConfigArray
     */
    private function formatConfig(array $config): array
    {
        return [
            'productCleanup.monthsNotSold' => $config['productCleanupMonthsNotSold'] ?? 6,
            'productCleanup.deleteNeverSold' => $config['productCleanupDeleteNeverSold'] ?? true,
            'productCleanup.monthsDisabled' => $config['productCleanupMonthsDisabled'] ?? 6,
            'productVariantCleanup.zeroStockMonths' => $config['productVariantCleanupZeroStockMonths'] ?? 6,
            'customerCleanup.guestMonths' => $config['customerCleanupGuestMonths'] ?? 12,
            'customerCleanup.inactiveMonths' => $config['customerCleanupInactiveMonths'] ?? 12,
            'cartCleanup.abandonedDays' => $config['cartCleanupAbandonedDays'] ?? 30,
            'orderCleanup.cancelledAgeMonths' => $config['orderCleanupCancelledAgeMonths'] ?? 12,
            'transactionCleanup.ageMonths' => $config['transactionCleanupAgeMonths'] ?? 12,
            'categoryCleanup.emptyCategories' => $config['categoryCleanupEmptyCategories'] ?? true,
            'categoryCleanup.noSalesMonths' => $config['categoryCleanupNoSalesMonths'] ?? 6,
            'promotionCleanup.expiredMonths' => $config['promotionCleanupExpiredMonths'] ?? 6,
            'promotionCleanup.unusedVoucherMonths' => $config['promotionCleanupUnusedVoucherMonths'] ?? 6,
            'cartRuleCleanup.orphaned' => $config['cartRuleCleanupOrphaned'] ?? true,
            'cmsPageCleanup.neverViewedMonths' => $config['cmsPageCleanupNeverViewedMonths'] ?? 6,
            'cmsPageCleanup.unpublishedDraftsMonths' => $config['cmsPageCleanupUnpublishedDraftsMonths'] ?? 6,
            'reviewCleanup.unapprovedDays' => $config['reviewCleanupUnapprovedDays'] ?? 30,
            'newsletterCleanup.bouncedMonths' => $config['newsletterCleanupBouncedMonths'] ?? 12,
            'mediaCleanup.orphanAgeDays' => $config['mediaCleanupOrphanAgeDays'] ?? 60,
            'mediaThumbnailCleanup.deleteThumbnails' => $config['mediaThumbnailCleanupDeleteThumbnails'] ?? true,
            'systemLogCleanup.months' => $config['systemLogCleanupMonths'] ?? 6,
            'cacheCleanup.flush' => $config['cacheCleanupFlush'] ?? true,
            'customFieldSetCleanup.orphaned' => $config['customFieldSetCleanupOrphaned'] ?? true,
            'dryRunMode' => $config['dryRunMode'] ?? true,
            'enableLogging' => $config['enableLogging'] ?? true,
            'enableScheduler' => $config['enableScheduler'] ?? false,
            'scheduleFrequency' => $config['scheduleFrequency'] ?? 'weekly',
            'notificationEmail' => $config['notificationEmail'] ?? '',
        ];
    }
}
