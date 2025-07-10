<?php

declare(strict_types=1);

namespace Zeobv\BundleProducts\Storefront\Subscriber;

use Zeobv\BundleProducts\Repository\BundleProductRepository;
use Zeobv\BundleProducts\Service\ConfigService;
use Shopware\Core\Content\Product\ProductCollection;
use Shopware\Storefront\Page\Product\ProductPageLoadedEvent;
use Zeobv\BundleProducts\Struct\BundleProduct\ProductBundle;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Shopware\Core\System\SalesChannel\Entity\SalesChannelEntityLoadedEvent;
use Shopware\Core\Content\Product\SalesChannel\Price\AbstractProductPriceCalculator;

class SalesChannelBundleProductSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly AbstractProductPriceCalculator $calculator,
        private readonly BundleProductRepository $bundleProductRepository,
        private readonly ConfigService $configService
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProductPageLoadedEvent::class => 'productPageLoaded',
            'sales_channel.product.loaded' => 'salesChannelLoaded',
        ];
    }

    public function productPageLoaded(ProductPageLoadedEvent $event): void
    {
        $product = $event->getPage()->getProduct();
        $context = $event->getSalesChannelContext();

        if (!$product->hasExtension(ProductBundle::EXTENSION_NAME)) {
            $disableReference = $this->configService->getDisableReferenceToBundleProductsFromProductDetailPage(
                $context->getSalesChannel()->getId()
            );

            if ($disableReference) {
                return;
            }

            $maxReferences = $this->configService->getMaxNumberOfProductDetailBundleReferences(
                $context->getSalesChannel()->getId()
            );

            $result = $this->bundleProductRepository->getBundleProductsForProductId(
                $product->getId(),
                $maxReferences,
                $context
            );

            if ($result === null) {
                return;
            }

            $product->addExtension('bundleMainProducts', $result);

            return;
        }

        $product = $event->getPage()->getProduct();

        $productBundle = $product->getExtension(ProductBundle::EXTENSION_NAME);

        if (!$productBundle instanceof ProductBundle) {
            return;
        }

        $this->calculator->calculate(
            $productBundle->getSalesChannelProducts(),
            $event->getSalesChannelContext()
        );
    }

    public function salesChannelLoaded(SalesChannelEntityLoadedEvent $event): void
    {
        $entity = current($event->getEntities());

        if (
            $entity === false
            || count($event->getIds()) !== 1
            || !$entity->hasExtension(ProductBundle::EXTENSION_NAME)
        ) {
            return;
        }

        $product = $entity;

        $productBundle = $product->getExtension(ProductBundle::EXTENSION_NAME);

        if (!$productBundle instanceof ProductBundle) {
            return;
        }

        $this->calculator->calculate(
            $productBundle->getSalesChannelProducts(),
            $event->getSalesChannelContext()
        );

        foreach ($productBundle->getSalesChannelProducts() as $salesChannelProduct) {
            if (
                $salesChannelProduct->getChildren() === null
                || $salesChannelProduct->getChildren()->count() < 1
            ) {
                continue;
            }

            $children = [];
            foreach ($salesChannelProduct->getChildren() as $child) {
                $children[] = $salesChannelProduct::createFrom($child);
            }

            $this->calculator->calculate($children, $event->getSalesChannelContext());

            $salesChannelProduct->setChildren(new ProductCollection($children));
        }
    }
}
