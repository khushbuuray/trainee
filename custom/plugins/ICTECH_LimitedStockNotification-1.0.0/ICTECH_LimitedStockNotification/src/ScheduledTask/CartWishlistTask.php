<?php

declare(strict_types=1);

namespace ICTECH_LimitedStockNotification\ScheduledTask;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;

class CartWishlistTask extends ScheduledTask
{
    public static function getTaskName(): string
    {
        return 'cart_wishlist_task';
    }

    public static function getDefaultInterval(): int
    {
        return 60;
    }
}
