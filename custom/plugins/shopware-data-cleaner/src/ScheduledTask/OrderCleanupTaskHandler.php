<?php declare(strict_types=1);

namespace IctDataCleanerPro\ScheduledTask;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use IctDataCleanerPro\Controller\Api\CleanupController;
use Shopware\Core\Framework\Context;

class OrderCleanupTaskHandler extends ScheduledTaskHandler
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
        return [OrderCleanupTask::class];
    }

      public function run(): void
    {
        $config = $this->systemConfigService->getDomain('IctDataCleanerPro.config');
        $enabled = $config['IctDataCleanerPro.config.enableSchedulerOfOrders'] ?? false;
        $frequency = $config['IctDataCleanerPro.config.orderCleanupScheduleFrequency'] ?? 'weekly';
        if (!$enabled || !$this->shouldRunNow('orderCleanup', $frequency)) {
            return;
        }
        $this->cleanupController->cleanup(Context::createDefaultContext(), 'cartCleanup');
        $this->cleanupController->cleanup(Context::createDefaultContext(), 'orderCleanup');

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