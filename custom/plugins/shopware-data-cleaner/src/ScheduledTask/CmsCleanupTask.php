<?php declare(strict_types=1);

namespace IctDataCleanerPro\ScheduledTask;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;

class CmsCleanupTask extends ScheduledTask
{
    public static function getTaskName(): string
    {
        return 'ict_data_cleaner.cms_cleanup_task';
    }

    public static function getDefaultInterval(): int
    {
        return 300; // 5 minutes
    }
}
