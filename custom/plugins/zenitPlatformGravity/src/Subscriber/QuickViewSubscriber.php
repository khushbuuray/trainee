<?php declare(strict_types=1);

namespace zenit\PlatformGravity\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Shopware\Core\Content\Product\SalesChannel\Detail\AbstractProductDetailRoute;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Storefront\Page\Product\QuickView\MinimalQuickViewPageLoadedEvent;

class QuickViewSubscriber implements EventSubscriberInterface
{
    /**
     * @var AbstractProductDetailRoute
     */
    protected $productDetailRoute;

    public function __construct(AbstractProductDetailRoute $productDetailRoute)
    {
        $this->productDetailRoute = $productDetailRoute;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MinimalQuickViewPageLoadedEvent::class => 'onMinimalQuickViewPageLoaded',
        ];
    }

    public function onMinimalQuickViewPageLoaded(MinimalQuickViewPageLoadedEvent $event): void
    {
        $page = $event->getPage();
        $product = $page->getProduct();
        $productId = $product->getId();

        $criteria = (new Criteria())
            ->addAssociation('manufacturer.media')
            ->addAssociation('options.group')
            ->addAssociation('properties.group')
            ->addAssociation('mainCategories.category');

        $criteria
            ->getAssociation('media')
            ->addSorting(new FieldSorting('position'));

        $criteriaResult = $this->productDetailRoute->load($productId, new Request, $event->getSalesChannelContext(), $criteria);
        $page->configuratorSettings = $criteriaResult->getConfigurator();
    }
}
