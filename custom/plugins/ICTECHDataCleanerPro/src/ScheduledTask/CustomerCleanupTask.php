<?php declare(strict_types=1);

namespace ICTECHDataCleanerPro\ScheduledTask;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;

class CustomerCleanupTask extends ScheduledTask
{
    public static function getTaskName(): string
    {
        return 'ictech_data_cleaner.customer_cleanup_task';
    }

    public static function getDefaultInterval(): int
    {
        return 86400; // Run daily
    }
}