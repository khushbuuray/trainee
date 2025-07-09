<?php declare(strict_types=1);

namespace ICTECHDataCleanerPro\ScheduledTask;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;
use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use ICTECHDataCleanerPro\Controller\Api\CleanupController;
use Shopware\Core\Framework\Context;
use ICTECHDataCleanerPro\Service\CleanupService;


class ReviewCleanupTaskHandler extends ScheduledTaskHandler
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
        return [ReviewCleanupTask::class];
    }

    public function run(): void
    {
        $context = Context::createDefaultContext();

        $config = $this->systemConfigService->getDomain('ICTECHDataCleanerPro.config');
        $enabled = $config['ICTECHDataCleanerPro.config.enableSchedulerOfReviews'] ?? false;
        $frequency = $config['ICTECHDataCleanerPro.config.reviewCleanupScheduleFrequency'] ?? 'weekly';

        if (!$enabled || !$this->shouldRunNow('reviewCleanup', $frequency)) {
            return;
        }

        $this->cleanupService->runCleanup($context, false, 'scheduled', 'reviewCleanup');

        $this->systemConfigService->set(
            'ICTECHDataCleanerPro.config.reviewCleanupLastRun',
            (new \DateTime())->format('Y-m-d H:i:s')
        );
    }

    private function shouldRunNow(string $moduleKey, string $frequency): bool
    {
        $lastRun = $this->systemConfigService->get("ICTECHDataCleanerPro.config.{$moduleKey}LastRun");
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
