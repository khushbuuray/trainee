<?php

declare(strict_types=1);

namespace ICTECH_ReminderEmailWishlist\Core\Content\ICTEmail\ReminderEmailRestock;

use Shopware\Core\Checkout\Customer\Aggregate\CustomerWishlist\CustomerWishlistEntity;
use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Shopware\Core\System\Currency\CurrencyEntity;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;

class ICTEmailRestockEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string|null
     */
    protected $productId;

    /**
     * @var string|null
     */
    protected $token;

    /**
     * @var string|null
     */
    protected $customerWishlistId;

    /**
     * @var string|null
     */
    protected $customerId;

    /**
     * @var int|null
     */
    protected $stock;

    /**
     * @var string|null
     */
    protected $currencyId;

    /**
     * @var string
     */
    protected $salesChannelId;

    /**
     * @var ProductEntity|null
     */
    protected $product;

    /**
     * @var CustomerWishlistEntity|null
     */
    protected $customerWishlists;

    /**
     * @var CustomerEntity|null
     */
    protected $customer;

    /**
     * @var CurrencyEntity|null
     */
    protected $currency;

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

    public function getProductId(): ?string
    {
        return $this->productId;
    }

    public function setProductId(?string $productId): void
    {
        $this->productId = $productId;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): void
    {
        $this->token = $token;
    }

    public function getCustomerWishlistId(): ?string
    {
        return $this->customerWishlistId;
    }

    public function setCustomerWishlistId(?string $customerWishlistId): void
    {
        $this->customerWishlistId = $customerWishlistId;
    }

    public function getCustomerId(): ?string
    {
        return $this->customerId;
    }

    public function setCustomerId(?string $customerId): void
    {
        $this->customerId = $customerId;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(?int $stock): void
    {
        $this->stock = $stock;
    }

    public function getCurrencyId(): ?string
    {
        return $this->currencyId;
    }

    public function setCurrencyId(?string $currencyId): void
    {
        $this->currencyId = $currencyId;
    }

    public function getSalesChannelId(): string
    {
        return $this->salesChannelId;
    }

    public function setSalesChannelId(string $salesChannelId): void
    {
        $this->salesChannelId = $salesChannelId;
    }

    public function getProduct(): ?ProductEntity
    {
        return $this->product;
    }

    public function setProduct(?ProductEntity $product): void
    {
        $this->product = $product;
    }

    public function getCustomerWishlists(): ?CustomerWishlistEntity
    {
        return $this->customerWishlists;
    }

    public function setCustomerWishlists(?CustomerWishlistEntity $customerWishlists): void
    {
        $this->customerWishlists = $customerWishlists;
    }

    public function getCustomer(): ?CustomerEntity
    {
        return $this->customer;
    }

    public function setCustomer(?CustomerEntity $customer): void
    {
        $this->customer = $customer;
    }

    public function getCurrency(): ?CurrencyEntity
    {
        return $this->currency;
    }

    public function setCurrency(?CurrencyEntity $currency): void
    {
        $this->currency = $currency;
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
