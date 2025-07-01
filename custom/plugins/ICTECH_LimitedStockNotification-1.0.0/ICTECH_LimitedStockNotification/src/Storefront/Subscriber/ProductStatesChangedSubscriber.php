<?php declare(strict_types=1);

namespace ICTECH_LimitedStockNotification\Storefront\Subscriber;

use Shopware\Core\Content\Product\Events\ProductIndexerEvent;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductStatesChangedSubscriber implements EventSubscriberInterface
{
    private EntityRepository $productRepository;
    private EntityRepository $ictCartWishlistProductRepository;
    private SystemConfigService $systemConfigService;

    public function __construct(
        EntityRepository $productRepository, 
        EntityRepository $ictCartWishlistProductRepository, 
        SystemConfigService $systemConfigService
    ) {
        $this->productRepository = $productRepository;
        $this->ictCartWishlistProductRepository = $ictCartWishlistProductRepository;
        $this->systemConfigService = $systemConfigService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProductIndexerEvent::class => 'onProductWritten',
        ];
    }
    
    public function onProductWritten(ProductIndexerEvent $event): void
    {
        $context = $event->getContext();
        $productId = $event->getIds()[0];

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('id', $productId));
        $criteria->addAssociation('options');
        $productData = $this->productRepository->search($criteria, $context)->first();

        if (! $productData) {
            return; // Exit if no product data found
        }

        $availableStock = $productData->get('availableStock');
        $context = $event->getContext();

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('productId', $productId));
        $productInfo = $this->ictCartWishlistProductRepository->search($criteria, $context)->getElements();

        foreach ($productInfo as $data) {
            $salesChannelId = $data->get('salesChannelId');
            $configStock = $this->systemConfigService->get('ICTECH_LimitedStockNotification.config.limitedStockAlert', $salesChannelId);

            if ($availableStock <= $configStock) {
                $this->ictCartWishlistProductRepository->update([
                    [
                        'id' => $data->get('id'),
                        'productId' => $productId,
                        'mailSent' => false
                    ]
                ], $context);
            }
        }
    }
}
