<?php declare(strict_types=1);

namespace zenit\PlatformGravity\Storefront\Controller;

use Shopware\Core\Framework\Routing\RoutingException;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Shopware\Core\Content\Product\Exception\ProductNotFoundException;
use Shopware\Core\Content\Product\Exception\VariantNotFoundException;
use Shopware\Core\Content\Product\SalesChannel\FindVariant\AbstractFindProductVariantRoute;
use Shopware\Storefront\Page\Product\QuickView\MinimalQuickViewPageLoader;

#[Route(defaults: ['_routeScope' => ['storefront']])]
class QuickViewController extends StorefrontController
{

    private MinimalQuickViewPageLoader $minimalQuickViewPageLoader;

    private AbstractFindProductVariantRoute $findVariantRoute;

    public function __construct(
        MinimalQuickViewPageLoader $minimalQuickViewPageLoader,
        AbstractFindProductVariantRoute $findVariantRoute

    ) {
        $this->minimalQuickViewPageLoader = $minimalQuickViewPageLoader;
        $this->findVariantRoute = $findVariantRoute;
    }

    #[Route(path: '/zenit/PlatformGravity/quickview/variant/{productId}', name: 'widgets.zenit.PlatformGravity.quickview.variant', options: ['seo' => false], defaults: ['productId' => null, 'XmlHttpRequest' => true], methods: ['GET'])]
    public function quickviewVariant(SalesChannelContext $context, Request $request, string $productId): Response
    {

        if (!$productId) {
            throw RoutingException::missingRequestParameter('productId', '/productId');
        }

        $switchedGroup = $request->query->has('switched') ? (string) $request->query->get('switched') : null;

        /** @var array|null $options */
        $options = json_decode($request->query->get('options', ''), true);

        try {
            $variantResponse = $this->findVariantRoute->load(
                $productId,
                new Request(
                    [
                        'switchedGroup' => $switchedGroup,
                        'options' => $options ?? [],
                    ]
                ),
                $context
            );

            $productId = $variantResponse->getFoundCombination()->getVariantId();
        } catch (VariantNotFoundException|ProductNotFoundException $productNotFoundException) {
            //
        }

        $request->attributes->set('productId', $productId);
        $page = $this->minimalQuickViewPageLoader->load($request, $context);

        return $this->renderStorefront('@Storefront/storefront/component/product/quickview/minimal.html.twig', ['page' => $page]);
    }
}