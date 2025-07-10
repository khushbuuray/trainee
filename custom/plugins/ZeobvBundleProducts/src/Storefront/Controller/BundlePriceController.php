<?php

declare(strict_types=1);

namespace Zeobv\BundleProducts\Storefront\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Zeobv\BundleProducts\Service\BundleProductReconfigurator;

#[Route(defaults: ['_routeScope' => ['storefront']])]
class BundlePriceController extends StorefrontController
{
    public function __construct(
        private readonly BundleProductReconfigurator $bundleProductReconfigurator
    ) {
    }

    #[Route('/zeobv/bundle-products/calculate-bundle-price/{bundleProductId}', name: 'frontend.zeobv.bundle-products.calculate-bundle-price', methods: ['POST'])]
    public function calculateBundlePrice(Request $request, string $bundleProductId, SalesChannelContext $context): JsonResponse
    {
        /** [[connectionId: string] => [productId: string, qty: int]] */
        $itemSelection = $request->request->all('itemSelection');

        if (!is_array($itemSelection) || $itemSelection === []) {
            return new JsonResponse(['error' => 'No item selection provided'], 400);
        }

        foreach ($itemSelection as $connectionId => $data) {
            if (isset($data['qty']) && $data['qty'] && isset($data['productId'])) {
                continue;
            }

            unset($itemSelection[$connectionId]);
        }

        return new JsonResponse(
            $this->bundleProductReconfigurator->determineCalculatedBundlePrice(
                $bundleProductId,
                $itemSelection,
                $context
            )
        );
    }
}
