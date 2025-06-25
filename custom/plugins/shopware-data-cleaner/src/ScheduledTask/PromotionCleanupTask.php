<?php declare(strict_types=1);

namespace IctDataCleanerPro\ScheduledTask;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;

class PromotionCleanupTask extends ScheduledTask
{
    public static function getTaskName(): string
    {
        return 'ict_data_cleaner.promotion_cleanup_task';
    }

    public static function getDefaultInterval(): int
    {
        return 300; // Run every 5 minutes (adjust as needed)
    }
}
