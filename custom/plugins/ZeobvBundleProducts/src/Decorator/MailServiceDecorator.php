<?php

declare(strict_types=1);

namespace Zeobv\BundleProducts\Decorator;

use Symfony\Component\Mime\Email;
use Shopware\Core\Framework\Context;
use Zeobv\BundleProducts\Service\ConfigService;
use Shopware\Core\Content\Mail\Service\AbstractMailService;
use Shopware\Core\Checkout\Order\Aggregate\OrderLineItem\OrderLineItemEntity;
use Shopware\Core\Checkout\Order\Aggregate\OrderLineItem\OrderLineItemCollection;

class MailServiceDecorator extends AbstractMailService
{
    public function __construct(
        private AbstractMailService $decorated,
        private ConfigService $configService
    )
    {
    }

    public function getDecorated(): AbstractMailService
    {
        return $this->decorated;
    }

    public function send(array $data, Context $context, array $templateData = []): ?Email
    {
        if (!isset($templateData['order'])) {
            return $this->getDecorated()->send($data, $context, $templateData);
        }

        if (isset($templateData['salesChannelId']) &&
            !$this->configService->getHideBundleChildlineItemsOnOrderConfirmationMail(
                $templateData['salesChannelId']
            )
        ) {
            return $this->getDecorated()->send($data, $context, $templateData);
        }

        if (is_array($templateData['order'])) {
            // Ensure $templateData['order']['lineItems'] is set and is an array
            $lineItems = $templateData['order']['lineItems'] ?? [];
            if (is_array($lineItems)) {
                $templateData['order']['lineItems'] = array_filter(
                    $lineItems,
                    static function (array $lineItem) {
                        return !isset($lineItem['payload']['zeobvCustomLineItemType']);
                    }
                );
            }
        } else {
            /** @var OrderLineItemCollection $lineItems */
            $lineItems = $templateData['order']->getLineItems();
            if ($lineItems) {
                $lineItems = $lineItems->filter(static function (OrderLineItemEntity $lineItem) {
                    return !isset($lineItem->getPayload()['zeobvCustomLineItemType']);
                });
                $templateData['order']->setLineItems($lineItems);
            }
        }

        return $this->getDecorated()->send($data, $context, $templateData);
    }
}
