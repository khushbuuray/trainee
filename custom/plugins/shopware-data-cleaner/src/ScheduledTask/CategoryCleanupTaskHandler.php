<?php declare(strict_types=1);

namespace IctDataCleanerPro\ScheduledTask;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;
use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Core\Framework\Context;
use IctDataCleanerPro\Controller\Api\CleanupController;
use IctDataCleanerPro\Service\CleanupService;

class CategoryCleanupTaskHandler extends ScheduledTaskHandler
{
    private SystemConfigService $systemConfigService;
        private CleanupService $cleanupService;


    /**
     * @param EntityRepository<ScheduledTaskCollection> $scheduledTaskRepository
     */
    public function __construct(
        EntityRepository $scheduledTaskRepository,
        SystemConfigService $systemConfigService,
               CleanupService $cleanupService

    ) {
        parent::__construct($scheduledTaskRepository);
        $this->systemConfigService = $systemConfigService;
          $this->cleanupService = $cleanupService;
    }

    /**
     * @return iterable<class-string>
     */
    public static function getHandledMessages(): iterable
    {
        return [CategoryCleanupTask::class];
    }

    public function run(): void
    {
        $config = $this->systemConfigService->getDomain('IctDataCleanerPro.config');
        $enabled = $config['IctDataCleanerPro.config.enableSchedulerOfCategories'] ?? false;
        $frequency = $config['IctDataCleanerPro.config.categoryCleanupScheduleFrequency'] ?? 'weekly';

        if (!$enabled || !$this->shouldRunNow('categoryCleanup', $frequency)) {
            return;
        }

        $this->cleanupService->runCleanup(Context::createDefaultContext(), false, 'scheduled', 'categoryCleanup');
    }

    private function shouldRunNow(string $moduleKey, string $frequency): bool
    {
        $lastRun = $this->systemConfigService->get("IctDataCleanerPro.config.{$moduleKey}LastRun");
        $now = new \DateTime();

        if (!is_string($lastRun)) {
            return true;
        }

        $last = new \DateTime($lastRun);

        return match ($frequency) {
            // 'weekly' => $last->modify('+7 days') <= $now,
            // 'monthly' => $last->modify('+1 month') <= $now,
            default => true,
        };
    }
}
