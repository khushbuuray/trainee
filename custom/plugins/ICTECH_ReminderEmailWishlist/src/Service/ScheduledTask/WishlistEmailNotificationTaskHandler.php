<?php

declare(strict_types=1);

namespace ICTECH_ReminderEmailWishlist\Service\ScheduledTask;

use ICTECH_ReminderEmailWishlist\Storefront\Controller\CustomWishlistEmailController;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: WishlistEmailNotificationTask::class)]
class WishlistEmailNotificationTaskHandler extends ScheduledTaskHandler
{
    protected EntityRepository $scheduledTaskRepository;
    protected EntityRepository $salesChannelRepository;
    private CustomWishlistEmailController $customWishlistEmailController;
    private RequestStack $requestStack;
    private SystemConfigService $systemConfigService;

    public function __construct(
        EntityRepository $scheduledTaskRepository,
        EntityRepository $salesChannelRepository,
        CustomWishlistEmailController $customWishlistEmailController,
        RequestStack $requestStack,
        SystemConfigService $systemConfigService,
    ) {
        $this->scheduledTaskRepository = $scheduledTaskRepository;
        $this->salesChannelRepository = $salesChannelRepository;
        $this->customWishlistEmailController = $customWishlistEmailController;
        $this->requestStack = $requestStack;
        $this->systemConfigService = $systemConfigService;
    }

    public function run(): void
    {
        $context = Context::createDefaultContext();

        $criteria = new Criteria();
        $salesChannels = $this->salesChannelRepository->search($criteria, $context);

        if ($salesChannels->getTotal() === 0) {
            return;
        }

        foreach ($salesChannels as $salesChannel) {
            $salesChannelId = $salesChannel->getId();
            $pluginStatus = $this->systemConfigService->get('ICTECH_ReminderEmailWishlist.config.ActiveInactive', $salesChannelId);
            $wishlistStatus = $this->systemConfigService->get('ICTECH_ReminderEmailWishlist.config.WishlistReminder', $salesChannelId);
            $wishlistDays = $this->systemConfigService->get('ICTECH_ReminderEmailWishlist.config.WishlistDays', $salesChannelId);

            if ($pluginStatus && $wishlistStatus && $wishlistDays !== null) {
                $this->customWishlistEmailController->customWishlistProductEmailReminder($salesChannelId, $context, $this->requestStack);
            }
        }
    }
}
