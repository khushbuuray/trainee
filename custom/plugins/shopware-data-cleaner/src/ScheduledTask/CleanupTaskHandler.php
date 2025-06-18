<?php declare(strict_types=1);

namespace IctDataCleanerPro\ScheduledTask;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use IctDataCleanerPro\Service\CleanupService;
use Shopware\Core\Framework\Context;
use Psr\Log\LoggerInterface;


class CleanupTaskHandler extends ScheduledTaskHandler
{
    private CleanupService $cleanupService;
    private SystemConfigService $systemConfigService;
    private LoggerInterface $logger;


    public function __construct(
        EntityRepository $scheduledTaskRepository,
        CleanupService $cleanupService,
        SystemConfigService $systemConfigService,
        LoggerInterface $logger

    ) {
        parent::__construct($scheduledTaskRepository);
        $this->cleanupService = $cleanupService;
        $this->systemConfigService = $systemConfigService;
        $this->logger = $logger;

    }

    public static function getHandledMessages(): iterable
    {
        return [CleanupTask::class];
    }

    public function run(): void
    {
                $context = Context::createDefaultContext();
        $this->logger->info('🧹 CleanupTaskHandler triggered.');

        $enabled = $this->systemConfigService->get('IctDataCleanerPro.config.enableScheduler');

        if (!$enabled) {
            $this->logger->info('[ProductCleanup] Skipped: enableScheduler is disabled.');
            return;
        }

        // $this->cleanupService->runCleanup(
        //     Context::createDefaultContext(),
        //     null,
        //     'scheduled'
        // );
         // Get the schedule frequency
        $frequency = $this->systemConfigService->get('IctDataCleanerPro.config.productCleanupScheduleFrequency', $context);
        $this->logger->info('[ProductCleanup] Schedule Frequency: ' . $frequency);

        // Optional: do frequency-based logic here if you want different timing later
        $this->cleanupService->runCleanup(
            $context,
            null,
            'scheduled'
        );

        $this->logger->info('[ProductCleanup] Cleanup run executed.');
    }
}
