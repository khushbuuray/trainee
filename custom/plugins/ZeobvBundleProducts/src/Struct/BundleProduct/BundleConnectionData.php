<?php

declare(strict_types=1);

namespace Zeobv\BundleProducts\Struct\BundleProduct;

use Shopware\Core\Framework\Struct\Struct;

class BundleConnectionData extends Struct
{
    public const EXTENSION_NAME = 'zeobvProductBundleConnection';

    protected string $id;
    protected string $bundleProductId;
    protected string $productId;
    protected int $quantity;
    protected int $position;
    protected bool $modifiable;
    protected bool $optional;
    protected ?string $comment;

    public function __construct(
        string $id,
        string $bundleProductId,
        string $productId,
        int $quantity,
        int $position,
        bool $modifiable = true,
        bool $optional = true,
        ?string $comment = null
    ) {
        $this->id = $id;
        $this->bundleProductId = $bundleProductId;
        $this->productId = $productId;
        $this->quantity = $quantity;
        $this->position = $position;
        $this->modifiable = $modifiable;
        $this->optional = $optional;
        $this->comment = $comment;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getBundleProductId(): string
    {
        return $this->bundleProductId;
    }

    public function getProductId(): string
    {
        return $this->productId;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function isModifiable(): bool
    {
        return $this->modifiable;
    }

    public function isOptional(): bool
    {
        return $this->optional;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }
}
