<?php declare(strict_types=1);

namespace Blog\Core\Content\BlogCategory\Aggregate;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Shopware\Core\Framework\Struct\ArrayEntity;
use Shopware\Core\System\Language\LanguageEntity;

class BlogCategoryTranslationEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var \DateTimeInterface
     */
    protected $createdAt;

    /**
     * @var \DateTimeInterface|null
     */
    protected $updatedAt;

    /**
     * @var string
     */
    protected $blogCategoryId;

    /**
     * @var string
     */
    protected $languageId;

    /**
     * @var ArrayEntity|null
     */
    protected $blogCategory;

    /**
     * @var LanguageEntity|null
     */
    protected $language;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
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

    public function getBlogCategoryId(): string
    {
        return $this->blogCategoryId;
    }

    public function setBlogCategoryId(string $blogCategoryId): void
    {
        $this->blogCategoryId = $blogCategoryId;
    }

    public function getLanguageId(): string
    {
        return $this->languageId;
    }

    public function setLanguageId(string $languageId): void
    {
        $this->languageId = $languageId;
    }

    public function getBlogCategory(): ?ArrayEntity
    {
        return $this->blogCategory;
    }

    public function setBlogCategory(?ArrayEntity $blogCategory): void
    {
        $this->blogCategory = $blogCategory;
    }

    public function getLanguage(): ?LanguageEntity
    {
        return $this->language;
    }

    public function setLanguage(?LanguageEntity $language): void
    {
        $this->language = $language;
    }
}