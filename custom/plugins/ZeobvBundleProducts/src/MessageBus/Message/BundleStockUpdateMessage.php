<?php

declare(strict_types=1);

namespace Zeobv\BundleProducts\MessageBus\Message;

class BundleStockUpdateMessage implements \Shopware\Core\Framework\MessageQueue\AsyncMessageInterface
{
    public function __construct(private array $productIds)
    {
        $this->productIds = $productIds;
    }

    public function getProductIds(): array
    {
        return $this->productIds;
    }
}
