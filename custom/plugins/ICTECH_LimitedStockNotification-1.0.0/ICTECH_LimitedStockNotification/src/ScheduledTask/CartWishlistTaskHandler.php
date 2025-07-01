<?php

declare(strict_types=1);

namespace ICTECH_LimitedStockNotification\ScheduledTask;

use ICTECH_LimitedStockNotification\Storefront\Controller\CartWishlistEmailController;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;

class CartWishlistTaskHandler extends ScheduledTaskHandler
{
    public function __construct(
        EntityRepository $scheduledTaskRepository,
        CartWishlistEmailController $cartWishlistEmailController,
    ) {
        parent::__construct($scheduledTaskRepository);
        $this->cartWishlistEmailController = $cartWishlistEmailController;
    }

    public static function getHandledMessages(): iterable
    {
        return [CartWishlistTask::class];
    }

    public function run(): void
    {
        $this->cartWishlistEmailController->cartWishlistEmail(Context::createDefaultContext());
    } 
}
