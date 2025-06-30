<?php declare(strict_types=1);

namespace IctDataCleanerPro\Service;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Context;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use IctDataCleanerPro\Service\Cleanup\CleanupHandlerInterface;

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

    /**
     * @param iterable<CleanupHandlerInterface> $cleanupHandlers
     */
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
        $isDryRun = (bool) ($dryRun ?? $config['dryRunMode'] ?? true); // ✅ FIXED

        $formattedConfig = $this->formatConfig($config);

        /** @var CleanupResult $results */
        $results = [];

        $this->connection->beginTransaction();
        try {
            foreach ($this->cleanupHandlers as $handler) {
                // ✅ Removed redundant instanceof check

                if ($module !== null && strpos(get_class($handler), ucfirst($module)) === false) {
                    continue;
                }

                $handlerResults = $handler->cleanup($formattedConfig, $isDryRun, $context);
                $results[get_class($handler)] = $handlerResults;
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
        $config = $this->systemConfigService->getDomain('IctDataCleanerPro.config');
        $cleanConfig = [];

        foreach ($config as $key => $value) {
            $cleanKey = str_replace('IctDataCleanerPro.config.', '', $key);
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
        $formattedConfig = [];

        $formattedConfig['productCleanup.monthsNotSold'] = $config['productCleanupMonthsNotSold'] ?? 6;
        $formattedConfig['productCleanup.deleteNeverSold'] = $config['productCleanupDeleteNeverSold'] ?? true;
        $formattedConfig['productCleanup.monthsDisabled'] = $config['productCleanupMonthsDisabled'] ?? 6;
        $formattedConfig['productVariantCleanup.zeroStockMonths'] = $config['productVariantCleanupZeroStockMonths'] ?? 6;

        $formattedConfig['customerCleanup.guestMonths'] = $config['customerCleanupGuestMonths'] ?? 12;
        $formattedConfig['customerCleanup.inactiveMonths'] = $config['customerCleanupInactiveMonths'] ?? 12;

        $formattedConfig['cartCleanup.abandonedDays'] = $config['cartCleanupAbandonedDays'] ?? 30;

        $formattedConfig['orderCleanup.cancelledAgeMonths'] = $config['orderCleanupCancelledAgeMonths'] ?? 12;
        $formattedConfig['transactionCleanup.ageMonths'] = $config['transactionCleanupAgeMonths'] ?? 12;

        $formattedConfig['categoryCleanup.emptyCategories'] = $config['categoryCleanupEmptyCategories'] ?? true;
        $formattedConfig['categoryCleanup.noSalesMonths'] = $config['categoryCleanupNoSalesMonths'] ?? 6;

        $formattedConfig['promotionCleanup.expiredMonths'] = $config['promotionCleanupExpiredMonths'] ?? 6;
        $formattedConfig['promotionCleanup.unusedVoucherMonths'] = $config['promotionCleanupUnusedVoucherMonths'] ?? 6;
        $formattedConfig['cartRuleCleanup.orphaned'] = $config['cartRuleCleanupOrphaned'] ?? true;

        $formattedConfig['cmsPageCleanup.neverViewedMonths'] = $config['cmsPageCleanupNeverViewedMonths'] ?? 6;
        $formattedConfig['cmsPageCleanup.unpublishedDraftsMonths'] = $config['cmsPageCleanupUnpublishedDraftsMonths'] ?? 6;

        $formattedConfig['reviewCleanup.unapprovedDays'] = $config['reviewCleanupUnapprovedDays'] ?? 30;

        $formattedConfig['newsletterCleanup.bouncedMonths'] = $config['newsletterCleanupBouncedMonths'] ?? 12;

        $formattedConfig['mediaCleanup.orphanAgeDays'] = $config['mediaCleanupOrphanAgeDays'] ?? 60;
        $formattedConfig['mediaThumbnailCleanup.deleteThumbnails'] = $config['mediaThumbnailCleanupDeleteThumbnails'] ?? true;

        $formattedConfig['systemLogCleanup.months'] = $config['systemLogCleanupMonths'] ?? 6;
        $formattedConfig['cacheCleanup.flush'] = $config['cacheCleanupFlush'] ?? true;
        $formattedConfig['customFieldSetCleanup.orphaned'] = $config['customFieldSetCleanupOrphaned'] ?? true;

        $formattedConfig['dryRunMode'] = $config['dryRunMode'] ?? true;
        $formattedConfig['enableLogging'] = $config['enableLogging'] ?? true;
        $formattedConfig['enableScheduler'] = $config['enableScheduler'] ?? false;
        $formattedConfig['scheduleFrequency'] = $config['scheduleFrequency'] ?? 'weekly';
        $formattedConfig['notificationEmail'] = $config['notificationEmail'] ?? '';

        return $formattedConfig;
    }
}
