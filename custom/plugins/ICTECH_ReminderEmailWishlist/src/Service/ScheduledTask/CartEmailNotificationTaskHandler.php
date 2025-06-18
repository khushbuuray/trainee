<?php

declare(strict_types=1);

namespace ICTECH_ReminderEmailWishlist\Service\ScheduledTask;

use ICTECH_ReminderEmailWishlist\Storefront\Controller\CustomCartEmailController;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: CartEmailNotificationTask::class)]
class CartEmailNotificationTaskHandler extends ScheduledTaskHandler
{
    protected EntityRepository $scheduledTaskRepository;
    protected EntityRepository $salesChannelRepository;
    private CustomCartEmailController $customCartEmailController;
    private RequestStack $requestStack;
    private SystemConfigService $systemConfigService;

    public function __construct(
        EntityRepository $scheduledTaskRepository,
        EntityRepository $salesChannelRepository,
        CustomCartEmailController $customCartEmailController,
        RequestStack $requestStack,
        SystemConfigService $systemConfigService,
    ) {
        $this->scheduledTaskRepository = $scheduledTaskRepository;
        $this->salesChannelRepository = $salesChannelRepository;
        $this->customCartEmailController = $customCartEmailController;
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
            $cartStatus = $this->systemConfigService->get('ICTECH_ReminderEmailWishlist.config.CartReminder', $salesChannelId);
            $cartDays = $this->systemConfigService->get('ICTECH_ReminderEmailWishlist.config.CartDays', $salesChannelId);

            if ($pluginStatus && $cartStatus && $cartDays !== null) {
                $this->customCartEmailController->customCartProductEmailReminder($salesChannelId, $context, $this->requestStack);
            }
        }
    }
}
