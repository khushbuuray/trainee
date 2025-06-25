<?php declare(strict_types=1);

namespace IctDataCleanerPro\ScheduledTask;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;

class CategoryCleanupTask extends ScheduledTask
{
    public static function getTaskName(): string
    {
        return 'ict_data_cleaner.category_cleanup_task';
    }

    public static function getDefaultInterval(): int
    {
        return 300; // Every 5 minutes; actual control comes from config
    }
}
