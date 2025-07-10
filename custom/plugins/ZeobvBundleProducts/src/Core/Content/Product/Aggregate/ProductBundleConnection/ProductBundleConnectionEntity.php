<?php

declare(strict_types=1);

namespace Zeobv\BundleProducts\Core\Content\Product\Aggregate\ProductBundleConnection;

use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class ProductBundleConnectionEntity extends Entity
{
    use EntityIdTrait;

    protected string $bundleProductId;
    protected string $bundleProductVersionId;
    protected string $productId;
    protected string $productVersionId;
    protected ?ProductEntity $bundleProduct;
    protected ?ProductEntity $product;
    protected int $quantity;
    protected int $position;
    protected bool $modifiable = true;
    protected bool $optional = true;
    protected ?string $comment = null;

    public function getBundleProductId(): string
    {
        return $this->bundleProductId;
    }

    public function setBundleProductId(string $bundleProductId): void
    {
        $this->bundleProductId = $bundleProductId;
    }

    public function getProductId(): string
    {
        return $this->productId;
    }

    public function setProductId(string $productId): void
    {
        $this->productId = $productId;
    }

    public function getBundleProduct(): ?ProductEntity
    {
        return $this->bundleProduct;
    }

    public function setBundleProduct(ProductEntity $bundleProduct): void
    {
        $this->bundleProduct = $bundleProduct;
    }

    public function getProduct(): ?ProductEntity
    {
        return $this->product;
    }

    public function setProduct(ProductEntity $product): void
    {
        $this->product = $product;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function isModifiable(): bool
    {
        return $this->modifiable;
    }

    public function setModifiable(bool $modifiable): void
    {
        $this->modifiable = $modifiable;
    }

    public function isOptional(): bool
    {
        return $this->optional;
    }

    public function setOptional(bool $optional): void
    {
        $this->optional = $optional;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): void
    {
        $this->comment = $comment;
    }
}
