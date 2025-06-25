<?php declare(strict_types=1);

namespace IctDataCleanerPro\ScheduledTask;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Core\Framework\Context;
use IctDataCleanerPro\Controller\Api\CleanupController;

class CategoryCleanupTaskHandler extends ScheduledTaskHandler
{
    private SystemConfigService $systemConfigService;
    private CleanupController $cleanupController;

    public function __construct(
        EntityRepository $scheduledTaskRepository,
        SystemConfigService $systemConfigService,
        CleanupController $cleanupController
    ) {
        parent::__construct($scheduledTaskRepository);
        $this->systemConfigService = $systemConfigService;
        $this->cleanupController = $cleanupController;
    }

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
        $this->cleanupController->cleanup(Context::createDefaultContext(), 'categoryCleanup');       
    }

    private function shouldRunNow(string $moduleKey, string $frequency): bool
    {

        $lastRun = $this->systemConfigService->get("IctDataCleanerPro.config.{$moduleKey}LastRun");
        $now = new \DateTime();

        if (!$lastRun) {
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
