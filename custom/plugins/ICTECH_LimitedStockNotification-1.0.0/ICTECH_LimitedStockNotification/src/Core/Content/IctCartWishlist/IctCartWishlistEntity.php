<?php declare(strict_types=1);

namespace ICTECH_LimitedStockNotification\Core\Content\IctCartWishlist;

use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;

class IctCartWishlistEntity extends Entity
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
    protected $email;

    /**
     * @var array|null
     */
    protected $lineItems;

    /**
     * @var array|null
     */
    protected $lineItemsWishlist;

    /**
     * @var string|null
     */
    protected $customerId;

    /**
     * @var string|null
     */
    protected $salesChannelId;

    /**
     * @var int|null
     */
    protected $scheduleIndex;

    /**
     * @var \DateTimeInterface|null
     */
    protected $lastMailSendAt;

    /**
     * @var bool|null
     */
    protected $mailSent;

    /**
     * @var bool|null
     */
    protected $isRecovered;

    /**
     * @var \DateTimeInterface
     */
    protected $createdAt;

    /**
     * @var \DateTimeInterface|null
     */
    protected $updatedAt;

    /**
     * @var CustomerEntity|null
     */
    protected $customer;

    /**
     * @var SalesChannelEntity|null
     */
    protected $salesChannel;

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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getLineItems(): ?array
    {
        return $this->lineItems;
    }

    public function setLineItems(?array $lineItems): void
    {
        $this->lineItems = $lineItems;
    }

    public function getLineItemsWishlist(): ?array
    {
        return $this->lineItemsWishlist;
    }

    public function setLineItemsWishlist(?array $lineItemsWishlist): void
    {
        $this->lineItemsWishlist = $lineItemsWishlist;
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

    public function getScheduleIndex(): ?int
    {
        return $this->scheduleIndex;
    }

    public function setScheduleIndex(?int $scheduleIndex): void
    {
        $this->scheduleIndex = $scheduleIndex;
    }

    public function getLastMailSendAt(): ?\DateTimeInterface
    {
        return $this->lastMailSendAt;
    }

    public function setLastMailSendAt(?\DateTimeInterface $lastMailSendAt): void
    {
        $this->lastMailSendAt = $lastMailSendAt;
    }

    public function getMailSent(): ?bool
    {
        return $this->mailSent;
    }

    public function setMailSent(?bool $mailSent): void
    {
        $this->mailSent = $mailSent;
    }

    public function getIsRecovered(): ?bool
    {
        return $this->isRecovered;
    }

    public function setIsRecovered(?bool $isRecovered): void
    {
        $this->isRecovered = $isRecovered;
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
}
