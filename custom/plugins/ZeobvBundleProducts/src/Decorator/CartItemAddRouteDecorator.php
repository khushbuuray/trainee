<?php

declare(strict_types=1);

namespace Zeobv\BundleProducts\Decorator;

use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\Error\Error;
use Symfony\Component\HttpFoundation\Request;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Symfony\Component\HttpFoundation\RequestStack;
use Shopware\Core\Checkout\Cart\Error\GenericCartError;
use Shopware\Core\Checkout\Cart\SalesChannel\CartResponse;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\Checkout\Cart\SalesChannel\AbstractCartItemAddRoute;

class CartItemAddRouteDecorator extends AbstractCartItemAddRoute
{
    private AbstractCartItemAddRoute $decorated;

    private RequestStack $requestStack;

    public function __construct(
        AbstractCartItemAddRoute $decorated,
        RequestStack $requestStack
    ) {
        $this->decorated = $decorated;
        $this->requestStack = $requestStack;
    }

    public function getDecorated(): AbstractCartItemAddRoute
    {
        return $this->decorated;
    }

    /**
     * @param array<LineItem>|null $items
     */
    public function add(Request $request, Cart $cart, SalesChannelContext $context, ?array $items): CartResponse
    {

        if($this->requestStack->getCurrentRequest()->request === null){
            return $this->getDecorated()->add($request, $cart, $context, $items);
        }

        $params = $this->requestStack->getCurrentRequest()->request->all();

        if ($items === null) {
            return $this->getDecorated()->add($request, $cart, $context, $items);
        }

        $existingLineItems = $cart->getLineItems();

        foreach ($items as $item) {
            $lineItemSubmitData = $params['lineItems'][$item->getReferencedId()] ?? null;

            if (
                $lineItemSubmitData === null
                || $item->getReferencedId() === null
            ) {
                continue;
            }

            if (
                key_exists('bundleContent', $lineItemSubmitData)
                && is_array($lineItemSubmitData['bundleContent'])
            ) {
                $bundleContent = [];
                foreach ($lineItemSubmitData['bundleContent'] as $connectionId => $data) {
                    if (!isset($data['included'])) {
                        continue;
                    }

                    $bundleContent[$connectionId] = [
                        'productId' => $data['productId'],
                        'quantity' => intval($data['quantity']),
                        'isChild' => boolval($data['isChild']),
                    ];
                }
            } else {
                $bundleContent = null;
            }

            $existingLineItem = $existingLineItems->get($item->getId());
            $includeBundleContent = isset($lineItemSubmitData['includeBundleContent']);

            # We check if there is already a line item for this bundle product in the cart
            if ($existingLineItem !== null) {
                $existingIncludesBundleContent = $existingLineItem->getPayloadValue('includeBundleContent');
                $existingBundleContent = $existingLineItem->getPayloadValue('bundleContent');

                $existingHash = md5(json_encode($existingBundleContent) ?: '') . ($existingIncludesBundleContent ? '1' : '0');
                $newHash = md5(json_encode($bundleContent) ?: '') . ($includeBundleContent ? '1' : '0');

                # If the content of the bundle product changed,
                # we throw an exception. We don't allow different
                # configurations of the same bundle product in the cart
                if ($existingHash !== $newHash) {
                    $cart->addErrors(
                        new GenericCartError(
                            $item->getId(),
                            'bundle_conflict',
                            [],
                            Error::LEVEL_ERROR,
                            true,
                            true,
                            true
                        )
                    );

                    return new CartResponse($cart);
                }
            }

            $item->setPayloadValue('bundleContent', $bundleContent);
            $item->setPayloadValue('includeBundleContent', $includeBundleContent);
        }

        return $this->getDecorated()->add($request, $cart, $context, $items);
    }
}
