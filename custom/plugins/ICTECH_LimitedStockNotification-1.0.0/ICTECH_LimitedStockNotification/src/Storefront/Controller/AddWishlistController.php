<?php declare(strict_types=1);

namespace ICTECH_LimitedStockNotification\Storefront\Controller;

use ICTECH_LimitedStockNotification\Checkout\IctCartWishlist\IctCartWishlist;
use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Checkout\Customer\Exception\CustomerWishlistNotFoundException;
use Shopware\Core\Checkout\Customer\Exception\DuplicateWishlistProductException;
use Shopware\Core\Checkout\Customer\SalesChannel\AbstractAddWishlistProductRoute;
use Shopware\Core\Checkout\Customer\SalesChannel\AbstractLoadWishlistRoute;
use Shopware\Core\Checkout\Customer\SalesChannel\AbstractMergeWishlistProductRoute;
use Shopware\Core\Checkout\Customer\SalesChannel\AbstractRemoveWishlistProductRoute;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Log\Package;
use Shopware\Core\Framework\Routing\RoutingException;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Storefront\Page\Wishlist\GuestWishlistPageLoadedHook;
use Shopware\Storefront\Page\Wishlist\GuestWishlistPageLoader;
use Shopware\Storefront\Page\Wishlist\WishlistPageLoadedHook;
use Shopware\Storefront\Page\Wishlist\WishlistPageLoader;
use Shopware\Storefront\Page\Wishlist\WishListPageProductCriteriaEvent;
use Shopware\Storefront\Page\Wishlist\WishlistWidgetLoadedHook;
use Shopware\Storefront\Pagelet\Wishlist\GuestWishlistPageletLoadedHook;
use Shopware\Storefront\Pagelet\Wishlist\GuestWishlistPageletLoader;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[Route(defaults: ['_routeScope' => ['storefront']])]
#[Package('storefront')]
class AddWishlistController extends StorefrontController
{
    protected ?Request $currentRequest;
    /**
     * @internal
     */
    public function __construct(
        private readonly WishlistPageLoader $wishlistPageLoader,
        private readonly AbstractLoadWishlistRoute $wishlistLoadRoute,
        private readonly AbstractAddWishlistProductRoute $addWishlistRoute,
        private readonly AbstractRemoveWishlistProductRoute $removeWishlistProductRoute,
        private readonly AbstractMergeWishlistProductRoute $mergeWishlistProductRoute,
        private readonly GuestWishlistPageLoader $guestPageLoader,
        private readonly GuestWishlistPageletLoader $guestPageletLoader,
        private readonly EventDispatcherInterface $eventDispatcher,
        private SystemConfigService $systemConfigService,
        private EntityRepository $wishlistRepository,
        private EntityRepository $ictCartWishlistRepository,
    ) {
    }

    #[Route(path: '/wishlist', name: 'frontend.wishlist.page', options: ['seo' => false], defaults: ['_noStore' => true], methods: ['GET'])]
    public function index(Request $request, SalesChannelContext $context): Response
    {
        $customer = $context->getCustomer();
        if ($customer !== null && $customer->getGuest() === false) {
            $page = $this->wishlistPageLoader->load($request, $context, $customer);
            $this->hook(new WishlistPageLoadedHook($page, $context));
        } else {
            $page = $this->guestPageLoader->load($request, $context);
            $this->hook(new GuestWishlistPageLoadedHook($page, $context));
        }

        return $this->renderStorefront('@Storefront/storefront/page/wishlist/index.html.twig', ['page' => $page]);
    }

    #[Route(path: '/wishlist/guest-pagelet', name: 'frontend.wishlist.guestPage.pagelet', options: ['seo' => false], defaults: ['XmlHttpRequest' => true], methods: ['POST'])]
    public function guestPagelet(Request $request, SalesChannelContext $context): Response
    {
        $customer = $context->getCustomer();
        if ($customer !== null && $customer->getGuest() === false) {
            throw new NotFoundHttpException();
        }

        $pagelet = $this->guestPageletLoader->load($request, $context);
        $this->hook(new GuestWishlistPageletLoadedHook($pagelet, $context));

        return $this->renderStorefront(
            '@Storefront/storefront/page/wishlist/wishlist-pagelet.html.twig',
            ['page' => $pagelet, 'searchResult' => $pagelet->getSearchResult()->getObject()]
        );
    }

    #[Route(path: '/widgets/wishlist', name: 'widgets.wishlist.pagelet', options: ['seo' => false], defaults: ['XmlHttpRequest' => true, '_loginRequired' => true], methods: ['GET', 'POST'])]
    public function ajaxPagination(Request $request, SalesChannelContext $context, CustomerEntity $customer): Response
    {
        $request->request->set('no-aggregations', true);
        $page = $this->wishlistPageLoader->load($request, $context, $customer);
        $this->hook(new WishlistPageLoadedHook($page, $context));
        $response = $this->renderStorefront('@Storefront/storefront/page/wishlist/index.html.twig', ['page' => $page]);
        $response->headers->set('x-robots-tag', 'noindex');

        return $response;
    }

    #[Route(path: '/wishlist/list', name: 'frontend.wishlist.product.list', options: ['seo' => false], defaults: ['XmlHttpRequest' => true, '_loginRequired' => true], methods: ['GET'])]
    public function ajaxList(Request $request, SalesChannelContext $context, CustomerEntity $customer): Response
    {
        $criteria = new Criteria();
        $this->eventDispatcher->dispatch(new WishListPageProductCriteriaEvent($criteria, $context, $request));
        try {
            $res = $this->wishlistLoadRoute->load($request, $context, $criteria, $customer);
        } catch (CustomerWishlistNotFoundException) {
            return new JsonResponse([]);
        }

        return new JsonResponse($res->getProductListing()->getIds());
    }

    #[Route(path: '/wishlist/product/delete/{id}', name: 'frontend.wishlist.product.delete', defaults: ['XmlHttpRequest' => true, '_loginRequired' => true], methods: ['POST', 'DELETE'])]
    public function remove(string $id, Request $request, SalesChannelContext $context, CustomerEntity $customer): Response
    {
        if (!$id) {
            throw RoutingException::missingRequestParameter('id');
        }
        try {
            $this->removeWishlistProductRoute->delete($id, $context, $customer);
            $this->addFlash(self::SUCCESS, $this->trans('wishlist.itemDeleteSuccess'));
        } catch (\Throwable) {
            $this->addFlash(self::DANGER, $this->trans('error.message-default'));
        }
        $this->removeWishlistItem($request, $customer, $context);

        return $this->createActionResponse($request);
    }

    #[Route(path: '/wishlist/add/{productId}', name: 'frontend.wishlist.product.add', options: ['seo' => false], defaults: ['XmlHttpRequest' => true, '_loginRequired' => true], methods: ['POST'])]
    public function ajaxAdd(Request $request, string $productId, SalesChannelContext $context, CustomerEntity $customer, SalesChannelContext $salesChannelDomainId): JsonResponse
    {
        try {
            $this->addWishlistRoute->add($productId, $context, $customer);
            $success = true;
        } catch (\Throwable) {
            $success = false;
        }

        // Add wishlist item from ict_cart_wishlist
        $this->addWishlistItem($request, $customer, $context);

        return new JsonResponse([
            'success' => $success,
        ]);
    }

    #[Route(path: '/wishlist/remove/{productId}', name: 'frontend.wishlist.product.remove', options: ['seo' => false], defaults: ['XmlHttpRequest' => true, '_loginRequired' => true], methods: ['POST'])]
    public function ajaxRemove(Request $request, string $productId, SalesChannelContext $context, CustomerEntity $customer): JsonResponse
    {
        try {
            $this->removeWishlistProductRoute->delete($productId, $context, $customer);
            $success = true;
        } catch (\Throwable) {
            $success = false;
        }

        // Remove wishlist item from ict_cart_wishlist
        $this->removeWishlistItem($request, $customer, $context);

        return new JsonResponse([
            'success' => $success,
        ]);
    }

    #[Route(path: '/wishlist/add-after-login/{productId}', name: 'frontend.wishlist.add.after.login', options: ['seo' => false], defaults: ['_loginRequired' => true], methods: ['GET'])]
    public function addAfterLogin(string $productId, SalesChannelContext $context, CustomerEntity $customer): Response
    {
        try {
            $this->addWishlistRoute->add($productId, $context, $customer);
            $this->addFlash(self::SUCCESS, $this->trans('wishlist.itemAddedSuccess'));
        } catch (DuplicateWishlistProductException) {
            $this->addFlash(self::WARNING, $this->trans('wishlist.duplicateItemError'));
        } catch (\Throwable) {
            $this->addFlash(self::DANGER, $this->trans('error.message-default'));
        }

        return $this->redirectToRoute('frontend.home.page');
    }

    #[Route(path: '/wishlist/merge', name: 'frontend.wishlist.product.merge', options: ['seo' => false], defaults: ['XmlHttpRequest' => true, '_loginRequired' => true], methods: ['POST'])]
    public function ajaxMerge(RequestDataBag $requestDataBag, Request $request, SalesChannelContext $context, CustomerEntity $customer): Response
    {
        try {
            $this->mergeWishlistProductRoute->merge($requestDataBag, $context, $customer);

            return $this->renderStorefront('@Storefront/storefront/utilities/alert.html.twig', [
                'type' => 'info', 'content' => $this->trans('wishlist.wishlistMergeHint'),
            ]);
        } catch (\Throwable) {
            $this->addFlash(self::DANGER, $this->trans('error.message-default'));
        }

        return $this->createActionResponse($request);
    }

    #[Route(path: '/wishlist/merge/pagelet', name: 'frontend.wishlist.product.merge.pagelet', defaults: ['XmlHttpRequest' => true, '_loginRequired' => true], methods: ['GET', 'POST'])]
    public function ajaxPagelet(Request $request, SalesChannelContext $context, CustomerEntity $customer): Response
    {
        $request->request->set('no-aggregations', true);
        $page = $this->wishlistPageLoader->load($request, $context, $customer);
        $this->hook(new WishlistWidgetLoadedHook($page, $context));

        return $this->renderStorefront('@Storefront/storefront/page/wishlist/wishlist-pagelet.html.twig', [
            'page' => $page,
            'searchResult' => $page->getWishlist()->getProductListing(),
        ]);
    }

    private function fetchCustomerWishlist($customerId)
    {
        $criteria = new Criteria();
        $context = Context::createDefaultContext();
        $criteria->addFilter(new EqualsFilter('customerId', $customerId));
        return $this->wishlistRepository->searchIds($criteria, $context)->firstId();
    }

    public function addWishlistItem(Request $request, CustomerEntity $customer, SalesChannelContext $salesChannelContext): void
    {
        $salesChannelId = $salesChannelContext->getContext()->getSource()->getSaleschannelId();
        $customerSalesChannelId = $customer->getSalesChannelId();
        $active = $this->systemConfigService->get('ICTECH_LimitedStockNotification.config.active', $salesChannelId);
        $configStock = $this->systemConfigService->get('ICTECH_LimitedStockNotification.config.limitedStockAlert', $salesChannelId);
        $wishlistStatus = $this->systemConfigService->get('ICTECH_LimitedStockNotification.config.wishlistNotification', $salesChannelId);
       
        if ($active === true && $wishlistStatus === true && $salesChannelId === $customerSalesChannelId) {
            $customerId = $customer->getId();
            $salesChannelDomainId = $salesChannelContext->getSalesChannel()->getDomains()->first()->getId();
            $wishlistLastId = $this->fetchCustomerWishlist($customerId);
            
            $criteria = new Criteria();
            $criteria->addFilter(new EqualsFilter('email', $customer->getEmail()));
            $criteria->addFilter(new EqualsFilter('cartToken', null));

            $page = $this->wishlistPageLoader->load($request, $salesChannelContext, $customer);
            $wishlist = $page->getWishlist();

            /** @var IctCartWishlist|mixed $wishlistResult */
            $wishlistResult = $this->ictCartWishlistRepository->search($criteria, $salesChannelContext->getContext());
            $lineItemsWishlist = json_encode($wishlist->getProductListing()->getElements());
            $lineItemsWishlist = json_decode($lineItemsWishlist, true);

            if (empty($lineItemsWishlist)) {
                $id = $wishlistResult->first() ? $wishlistResult->first()->getId() : null;
                if ($id !== null) {
                    $this->ictCartWishlistRepository->delete([
                        [
                            'id' => $id,
                        ],
                    ], $salesChannelContext->getContext());
                }
            }

            $filteredLineItems = array_filter($lineItemsWishlist, function ($wishlistData) use ($configStock) {
                return ($configStock >= $wishlistData['availableStock'])
                    && ($wishlistData['availableStock'] > 0) && (!$wishlistData['availableStock'] == 0);
            });

            if (empty($filteredLineItems)) {
                return;
            }

            if ($active === true && $wishlistStatus === true) {
                $data = [
                    'id' => $wishlistResult->first() ? $wishlistResult->first()->getId() : Uuid::randomHex(),
                    'wishlistId' => $wishlistLastId,
                    'lineItemsWishlist' => $filteredLineItems,
                    'currencyId' => $salesChannelContext->getCurrency()->getId(),
                    'paymentMethodId' => $salesChannelContext->getPaymentMethod()->getId(),
                    'shippingMethodId' => $salesChannelContext->getShippingMethod()->getId(),
                    'countryId' => $salesChannelContext->getShippingLocation()->getCountry()->getId(),
                    'salesChannelId' => $salesChannelContext->getSalesChannel()->getId(),
                    'salesChannelDomainId' => $salesChannelDomainId,
                    'customerId' => $customer->getId(),
                    'email' => $customer->getEmail(),
                ];

                $this->ictCartWishlistRepository->upsert([$data], $salesChannelContext->getContext());
            }
        }
    }
    public function removeWishlistItem(Request $request, CustomerEntity $customer, SalesChannelContext $salesChannelContext): void
    {
        $salesChannelId = $customer->getSalesChannelId();
        $configStock = $this->systemConfigService->get('ICTECH_LimitedStockNotification.config.limitedStockAlert', $salesChannelId);

        $customerId = $customer->getId();
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('customerId', $customerId));
        $criteria->addAssociation('products');
        $wishlistData = $this->wishlistRepository->search($criteria, $salesChannelContext->getContext())->first();

        $wishlistId = $wishlistData->getId();

        $page = $this->wishlistPageLoader->load($request, $salesChannelContext, $customer);
        $wishlist = $page->getWishlist();

        /** @var IctCartWishlist|mixed $wishlistResult */
        $wishlistResult = $this->ictCartWishlistRepository->search($criteria, $salesChannelContext->getContext());
        $lineItemsWishlist = json_encode($wishlist->getProductListing()->getElements());
        $lineItemsWishlist = json_decode($lineItemsWishlist, true);

        $wishlistLastId = $this->fetchCustomerWishlist($customerId);
        $salesChannelDomainId = $salesChannelContext->getSalesChannelId();

        $filteredLineItems = array_filter($lineItemsWishlist, function ($wishlistData) use ($configStock) {
            return ($configStock >= $wishlistData['availableStock'])
                && ($wishlistData['availableStock'] > 0) && (!$wishlistData['availableStock'] == 0);
        });

        $data = [
            'id' => $wishlistResult->first() ? $wishlistResult->first()->getId() : Uuid::randomHex(),
            'wishlistId' => $wishlistLastId,
            'lineItemsWishlist' => $filteredLineItems,
            'currencyId' => $salesChannelContext->getCurrency()->getId(),
            'paymentMethodId' => $salesChannelContext->getPaymentMethod()->getId(),
            'shippingMethodId' => $salesChannelContext->getShippingMethod()->getId(),
            'countryId' => $salesChannelContext->getShippingLocation()->getCountry()->getId(),
            'salesChannelId' => $salesChannelContext->getSalesChannel()->getId(),
            'salesChannelDomainId' => $salesChannelDomainId,
            'customerId' => $customer->getId(),
            'email' => $customer->getEmail(),
        ];

        $this->ictCartWishlistRepository->upsert([$data], $salesChannelContext->getContext());

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('wishlistId', $wishlistId));
        $cartWishlistData = $this->ictCartWishlistRepository->search($criteria, $salesChannelContext->getContext())->first();

        if (empty($cartWishlistData->getLineItemsWishlist())) {
            $this->ictCartWishlistRepository->delete([
                [
                    'id' => $cartWishlistData->getId(),
                ],
            ], $salesChannelContext->getContext());
        }
    }
}
