<?php declare(strict_types=1);

namespace zenit\PlatformGravity\Subscriber;

use Shopware\Core\Content\Product\Events\ProductCrossSellingStreamCriteriaEvent;
use Shopware\Core\Content\Product\Events\ProductCrossSellingIdsCriteriaEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Storefront\Theme\ThemeConfigValueAccessor;
use Shopware\Core\Content\Product\Events\ProductListingCriteriaEvent;
use Shopware\Core\Content\Product\Events\ProductSearchCriteriaEvent;
use Shopware\Core\Content\Product\Events\ProductSuggestCriteriaEvent;
use Shopware\Storefront\Page\Wishlist\WishListPageProductCriteriaEvent;


class ProductListingSubscriber implements EventSubscriberInterface
{

    /**
     * @var string
     */
    private $pluginName = 'zenitPlatformGravity';

    /**
     * @var ThemeConfigValueAccessor
     */
    private $themeConfigAccessor;

    /**
     * @var EntityRepository
     */
    private $themeRepository;

    /**
     * @var string
     */
    private $themeId;

    public function __construct(ThemeConfigValueAccessor $themeConfigAccessor, EntityRepository $themeRepository)
    {
        $this->themeConfigAccessor = $themeConfigAccessor;
        $this->themeRepository = $themeRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProductSuggestCriteriaEvent::class => 'handleSuggestRequest',
            ProductCrossSellingIdsCriteriaEvent::class => 'handleCrossSellingRequest',
            ProductCrossSellingStreamCriteriaEvent::class => 'handleCrossSellingLoadRequest',
            ProductListingCriteriaEvent::class => 'handleListingRequest',
            ProductSearchCriteriaEvent::class => 'handleSearchRequest',
            WishListPageProductCriteriaEvent::class => 'handleWishlistRequest'
        ];
    }

    public function handleCrossSellingRequest(ProductCrossSellingIdsCriteriaEvent $event)
    {
        if ($this->hasCardImgSwitch($event)) {
            $criteria = $event->getCriteria();
            $criteria->addAssociation('media');
        }
    }

    public function handleCrossSellingLoadRequest(ProductCrossSellingStreamCriteriaEvent $event)
    {
        if ($this->hasCardImgSwitch($event)) {
            $criteria = $event->getCriteria();
            $criteria->addAssociation('media');
        }
    }

    public function handleListingRequest(ProductListingCriteriaEvent $event)
    {
        if ($this->hasCardImgSwitch($event)) {
            $criteria = $event->getCriteria();
            $criteria->addAssociation('media');
        }
    }

    public function handleSuggestRequest(ProductSuggestCriteriaEvent $event)
    {
        if ($this->hasCardImgSwitch($event)) {
            $criteria = $event->getCriteria();
            $criteria->addAssociation('media');
        }
    }

    public function handleSearchRequest(ProductSearchCriteriaEvent $event)
    {
        if ($this->hasCardImgSwitch($event)) {
            $criteria = $event->getCriteria();
            $criteria->addAssociation('media');
        }
    }

    public function handleWishlistRequest(WishListPageProductCriteriaEvent $event)
    {
        if ($this->hasCardImgSwitch($event)) {
            $criteria = $event->getCriteria();
            $criteria->addAssociation('media');
        }
    }

    private function getThemeIdByTechnicalName(string $technicalName, Context $context): ?string
    {
        return $this->themeRepository->searchIds(
            (new Criteria())->addFilter(new EqualsFilter('technicalName', $technicalName)),
            $context
        )->firstId();
    }

    private function hasCardImgSwitch($event): ?bool
    {
        $context = $event->getContext();
        $salesChannelContext = $event->getSalesChannelContext();

        $themeId = $this->getThemeIdByTechnicalName($this->pluginName, $context);
        $this->themeId = $themeId;
        $cardImgSwitchValue = $this->themeConfigAccessor->get('zen-product-listing-card-img-switch', $salesChannelContext, $this->themeId);

        return $cardImgSwitchValue !== null and $cardImgSwitchValue !== 'none';
    }
}
