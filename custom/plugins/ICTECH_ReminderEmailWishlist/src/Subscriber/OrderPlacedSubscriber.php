<?php

declare(strict_types=1);

namespace ICTECH_ReminderEmailWishlist\Subscriber;

use Shopware\Core\Checkout\Cart\Event\CheckoutOrderPlacedEvent;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OrderPlacedSubscriber implements EventSubscriberInterface
{
    private EntityRepository $cartEmailReminderRepository;
    private EntityRepository $restockEmailReminderRepository;

    public function __construct(
        EntityRepository $cartEmailReminderRepository,
        EntityRepository $restockEmailReminderRepository
    ) {
        $this->cartEmailReminderRepository = $cartEmailReminderRepository;
        $this->restockEmailReminderRepository = $restockEmailReminderRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CheckoutOrderPlacedEvent::class => 'onOrderPlaced',
        ];
    }

    public function onOrderPlaced(CheckoutOrderPlacedEvent $event): void
    {
        $order = $event->getOrder();
        $context = $event->getContext();
        $customerId = $order->getOrderCustomer()->getCustomerId(); // Get current customer ID

        foreach ($order->getLineItems() as $lineItem) {
            $productId = $lineItem->getReferencedId();

            if ($productId) {
                $this->removeReminderData($productId, $customerId, $context);
            }
        }
    }

    private function removeReminderData(string $productId, string $customerId, Context $context): void
    {
        $repositories = [
            $this->cartEmailReminderRepository,
            $this->restockEmailReminderRepository,
        ];

        foreach ($repositories as $repository) {
            $criteria = new Criteria();
                $criteria->addFilter(new EqualsFilter('productId', $productId));
            $criteria->addFilter(new EqualsFilter('customerId', $customerId));

            $ids = $repository->searchIds($criteria, $context)->getIds();

            if (! empty($ids)) {
                $deletePayload = array_map(static fn ($id) => ['id' => $id], $ids);
                $repository->delete($deletePayload, $context);
            }
        }
    }
}
