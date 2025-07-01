<?php declare(strict_types=1);

namespace ICTECH_LimitedStockNotification\Core\Content\IctCartWishlistProduct;

use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;

class IctCartWishlistProductEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string|null
     */
    protected $cartToken;

    /**
     * @var string|null
     */
    protected $wishlistId;

    /**
     * @var string|null
     */
    protected $customerId;

    /**
     * @var string|null
     */
    protected $salesChannelId;

    /**
     * @var string
     */
    protected $productId;

    /**
     * @var bool|null
     */
    protected $mailSent;

    /**
     * @var ProductEntity|null
     */
    protected $product;

    /**
     * @var CustomerEntity|null
     */
    protected $customer;

    /**
     * @var SalesChannelEntity|null
     */
    protected $salesChannel;

    /**
     * @var \DateTimeInterface
     */
    protected $createdAt;

    /**
     * @var \DateTimeInterface|null
     */
    protected $updatedAt;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getCartToken(): ?string
    {
        return $this->cartToken;
    }

    public function setCartToken(?string $cartToken): void
    {
        $this->cartToken = $cartToken;
    }

    public function getWishlistId(): ?string
    {
        return $this->wishlistId;
    }

    public function setWishlistId(?string $wishlistId): void
    {
        $this->wishlistId = $wishlistId;
    }

    public function getCustomerId(): ?string
    {
        return $this->customerId;
    }

    public function setCustomerId(?string $customerId): void
    {
        $this->customerId = $customerId;
    }

    public function getSalesChannelId(): ?string
    {
        return $this->salesChannelId;
    }

    public function setSalesChannelId(?string $salesChannelId): void
    {
        $this->salesChannelId = $salesChannelId;
    }

    public function getProductId(): string
    {
        return $this->productId;
    }

    public function setProductId(string $productId): void
    {
        $this->productId = $productId;
    }

    public function getMailSent(): ?bool
    {
        return $this->mailSent;
    }

    public function setMailSent(?bool $mailSent): void
    {
        $this->mailSent = $mailSent;
    }

    public function getProduct(): ?ProductEntity
    {
        return $this->product;
    }

    public function setProduct(?ProductEntity $product): void
    {
        $this->product = $product;
    }

    public function getCustomer(): ?CustomerEntity
    {
        return $this->customer;
    }

    public function setCustomer(?CustomerEntity $customer): void
    {
        $this->customer = $customer;
    }

    public function getSalesChannel(): ?SalesChannelEntity
    {
        return $this->salesChannel;
    }

    public function setSalesChannel(?SalesChannelEntity $salesChannel): void
    {
        $this->salesChannel = $salesChannel;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
