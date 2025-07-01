<?php

declare(strict_types=1);

namespace ICTECH_ReminderEmailWishlist\Service\ScheduledTask;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;

class RestockEmailNotificationTask extends ScheduledTask
{
    public static function getTaskName(): string
    {
        return 'restock_email_notification_task';
    }

    public static function getDefaultInterval(): int
    {
        return 60;
    }
}
