<?php declare(strict_types=1);

namespace IctDataCleanerPro\ScheduledTask;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use IctDataCleanerPro\Service\CleanupService;
use Shopware\Core\Framework\Context;

class CleanupTaskHandler extends ScheduledTaskHandler
{
    private CleanupService $cleanupService;
    private SystemConfigService $systemConfigService;

    public function __construct(
        EntityRepository $scheduledTaskRepository,
        CleanupService $cleanupService,
        SystemConfigService $systemConfigService
    ) {
        parent::__construct($scheduledTaskRepository);
        $this->cleanupService = $cleanupService;
        $this->systemConfigService = $systemConfigService;
    }

    public static function getHandledMessages(): iterable
    {
        return [CleanupTask::class];
    }

    public function run(): void
    {
        $enabled = $this->systemConfigService->get('IctDataCleanerPro.config.enableScheduler');
        
        if (!$enabled) {
            return;
        }

        $this->cleanupService->runCleanup(
            Context::createDefaultContext(),
            null,
            'scheduled'
        );
    }
}
