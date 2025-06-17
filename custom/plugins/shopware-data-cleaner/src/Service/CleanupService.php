<?php declare(strict_types=1);

namespace IctDataCleanerPro\Service;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Context;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use IctDataCleanerPro\Service\Cleanup\CleanupHandlerInterface;

class CleanupService
{
    private Connection $connection;
    private SystemConfigService $systemConfigService;
    private CleanupLoggerService $loggerService;
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

    public function runCleanup(Context $context, bool $dryRun = null, string $trigger = 'manual'): array
    {
        $config = $this->getConfig();
        $isDryRun = $dryRun ?? $config['dryRunMode'];
        
        // Convert config to the format expected by handlers
        $formattedConfig = $this->formatConfig($config);
        
        $results = [];
        
        $this->connection->beginTransaction();
        
        try {
            foreach ($this->cleanupHandlers as $handler) {
                if (!$handler instanceof CleanupHandlerInterface) {
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
            
            // Log the cleanup run
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

    public function previewCleanup(Context $context): array
    {
        return $this->runCleanup($context, true, 'preview');
    }

    private function getConfig(): array
    {
        $config = $this->systemConfigService->getDomain('IctDataCleanerPro.config');
        // Remove the prefix from keys
        $cleanConfig = [];
        foreach ($config as $key => $value) {
            $cleanKey = str_replace('IctDataCleanerPro.config.', '', $key);
            $cleanConfig[$cleanKey] = $value;
        }
        
        return $cleanConfig;
    }
    
    /**
     * Convert the flat config to the dot notation format expected by handlers
     */
    private function formatConfig(array $config): array
    {
        $formattedConfig = [];
        
        // Product cleanup
        $formattedConfig['productCleanup.monthsNotSold'] = $config['productCleanupMonthsNotSold'] ?? 6;
        $formattedConfig['productCleanup.deleteNeverSold'] = $config['productCleanupDeleteNeverSold'] ?? true;
        $formattedConfig['productCleanup.monthsDisabled'] = $config['productCleanupMonthsDisabled'] ?? 6;
        $formattedConfig['productVariantCleanup.zeroStockMonths'] = $config['productVariantCleanupZeroStockMonths'] ?? 6;
        
        // Customer cleanup
        $formattedConfig['customerCleanup.guestMonths'] = $config['customerCleanupGuestMonths'] ?? 12;
        $formattedConfig['customerCleanup.inactiveMonths'] = $config['customerCleanupInactiveMonths'] ?? 12;
        
        // Cart cleanup
        $formattedConfig['cartCleanup.abandonedDays'] = $config['cartCleanupAbandonedDays'] ?? 30;
        
        // Order cleanup
        $formattedConfig['orderCleanup.cancelledAgeMonths'] = $config['orderCleanupCancelledAgeMonths'] ?? 12;
        $formattedConfig['transactionCleanup.ageMonths'] = $config['transactionCleanupAgeMonths'] ?? 12;
        
        // Category cleanup
        $formattedConfig['categoryCleanup.emptyCategories'] = $config['categoryCleanupEmptyCategories'] ?? true;
        $formattedConfig['categoryCleanup.noSalesMonths'] = $config['categoryCleanupNoSalesMonths'] ?? 6;
        
        // Promotion cleanup
        $formattedConfig['promotionCleanup.expiredMonths'] = $config['promotionCleanupExpiredMonths'] ?? 6;
        $formattedConfig['promotionCleanup.unusedVoucherMonths'] = $config['promotionCleanupUnusedVoucherMonths'] ?? 6;
        $formattedConfig['cartRuleCleanup.orphaned'] = $config['cartRuleCleanupOrphaned'] ?? true;
        
        // CMS cleanup
        $formattedConfig['cmsPageCleanup.neverViewedMonths'] = $config['cmsPageCleanupNeverViewedMonths'] ?? 6;
        $formattedConfig['cmsPageCleanup.unpublishedDraftsMonths'] = $config['cmsPageCleanupUnpublishedDraftsMonths'] ?? 6;
        
        // Review cleanup
        $formattedConfig['reviewCleanup.unapprovedDays'] = $config['reviewCleanupUnapprovedDays'] ?? 30;
        
        // Newsletter cleanup
        $formattedConfig['newsletterCleanup.bouncedMonths'] = $config['newsletterCleanupBouncedMonths'] ?? 12;
        
        // Media cleanup
        $formattedConfig['mediaCleanup.orphanAgeDays'] = $config['mediaCleanupOrphanAgeDays'] ?? 60;
        $formattedConfig['mediaThumbnailCleanup.deleteThumbnails'] = $config['mediaThumbnailCleanupDeleteThumbnails'] ?? true;
        
        // Log cleanup
        $formattedConfig['systemLogCleanup.months'] = $config['systemLogCleanupMonths'] ?? 6;
        $formattedConfig['cacheCleanup.flush'] = $config['cacheCleanupFlush'] ?? true;
        $formattedConfig['customFieldSetCleanup.orphaned'] = $config['customFieldSetCleanupOrphaned'] ?? true;
        
        // Add general settings
        $formattedConfig['dryRunMode'] = $config['dryRunMode'] ?? true;
        $formattedConfig['enableLogging'] = $config['enableLogging'] ?? true;
        $formattedConfig['enableScheduler'] = $config['enableScheduler'] ?? false;
        $formattedConfig['scheduleFrequency'] = $config['scheduleFrequency'] ?? 'weekly';
        $formattedConfig['notificationEmail'] = $config['notificationEmail'] ?? '';
        
        return $formattedConfig;
    }
}
