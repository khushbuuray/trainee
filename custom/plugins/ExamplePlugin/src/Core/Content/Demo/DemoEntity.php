<?php declare(strict_types=1);

namespace Example\Core\Content\Demo;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\TranslationEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Example\Core\Content\Demo\DemoTranslationEntity;

class DemoEntity extends Entity
{
    use EntityIdTrait;

    protected bool $active;
    protected ?string $countryId;
    protected ?string $stateId;
    protected ?string $imageId;
    protected ?string $productId;



    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function getCountryId(): ?string
    {
        return $this->countryId;
    }

    public function setCountryId(?string $countryId): void
    {
        $this->countryId = $countryId;
    }
}
