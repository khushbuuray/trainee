<?php

declare(strict_types=1);

namespace ICTECH_ReminderEmailWishlist\Storefront\Controller;

use ICTECH_ReminderEmailWishlist\Service\EmailService;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SalesChannel\Context\AbstractSalesChannelContextFactory;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Attribute\Route;

#[Route(defaults: ['_routeScope' => ['storefront']])]
class CustomWishlistEmailController extends StorefrontController
{
    private EntityRepository $currencyRepository;
    private EntityRepository $customerRepository;
    private EntityRepository $customWishlistRepository;
    private EmailService $emailService;
    private EntityRepository $customerWishlistRepository;
    private AbstractSalesChannelContextFactory $salesChannelContextFactory;
    private EntityRepository $productRepository;
    private SystemConfigService $systemConfigService;

    public function __construct(
        EntityRepository $currencyRepository,
        EntityRepository $customerRepository,
        EntityRepository $customWishlistRepository,
        EmailService $emailService,
        EntityRepository $customerWishlistRepository,
        AbstractSalesChannelContextFactory $salesChannelContextFactory,
        EntityRepository $productRepository,
        SystemConfigService $systemConfigService,
    ) {
        $this->currencyRepository = $currencyRepository;
        $this->customerRepository = $customerRepository;
        $this->customWishlistRepository = $customWishlistRepository;
        $this->emailService = $emailService;
        $this->customerWishlistRepository = $customerWishlistRepository;
        $this->salesChannelContextFactory = $salesChannelContextFactory;
        $this->productRepository = $productRepository;
        $this->systemConfigService = $systemConfigService;
    }

    #[Route(path: '/api/customer/customWishlistProductEmailReminder', name: 'api.customer.customWishlistProductEmailReminder', methods: ['GET', 'POST'])]
    public function customWishlistProductEmailReminder($salesChannelId, Context $context, RequestStack $requestStack): JsonResponse
    {
        $wishlistDays = $this->systemConfigService->get('ICTECH_ReminderEmailWishlist.config.WishlistDays', $salesChannelId);
        $wishlistDate = date('Y-m-d', strtotime('-' . $wishlistDays . 'days'));
        $criteria = new Criteria();
        $criteria->addFilter(
            new EqualsFilter('salesChannelId', $salesChannelId),
            new ContainsFilter('createdAt', $wishlistDate)
        );
        $criteria->addAssociation('customerWishlist');
        $wishlistReminderData = $this->customWishlistRepository->search($criteria, $context)->getElements();

        foreach ($wishlistReminderData as $wishlistData) {
            $token = Uuid::randomHex();
            $wishlistReminderEmailId = $wishlistData->id;
            $customerDomain = null;
            $salesChannelContext = $this->salesChannelContextFactory->create($token, $wishlistData->salesChannelId);

            $criteria = new Criteria();
            $criteria->addFilter(new EqualsFilter('id', $wishlistData->getCustomerWishlist()->customerId));
            $customerData = $this->customerRepository->search($criteria, $context)->first();
            $languageId = $customerData->getLanguageId();

            $currencySymbol = $this->getCurrencySymbol($wishlistData->currencyId, $context);

            $getDomainElements = $salesChannelContext->getSalesChannel()->getDomains()->getElements();

            $request = $requestStack->getCurrentRequest();
            $scheme = $request ? $request->getScheme() : 'https';
            foreach ($getDomainElements as $item) {
                if ($item->getLanguageId() === $languageId) {
                    $domainUrl = $item->getUrl();
                    if (parse_url($domainUrl, PHP_URL_SCHEME) === $scheme) {
                        $customerDomain = $domainUrl;
                        break;
                    }
                }
            }
            $salesChannelId = $salesChannelContext->getSalesChannelId();

//          get customer wishlist data
            $criteria = new Criteria();
            $criteria->addFilter(new EqualsFilter('salesChannelId', $salesChannelId));
            $criteria->addFilter(new EqualsFilter('id', $wishlistData->customerWishlistId));
            $criteria->addAssociation('customer');
            $criteria->addAssociation('products');
            $wishlistTableData = $this->customerWishlistRepository->search($criteria, $context);
            $wishlistDataNew = $wishlistTableData->getElements();
            foreach ($wishlistDataNew as $data) {
                $wishlistInfoData = [];
                foreach ($data->products->getElements() as $p) {
                    //get product table data
                    $productCreatedAt = date_format($p->createdAt, 'Y-m-d');
                    if (strtotime($productCreatedAt) === strtotime($wishlistDate)) {
                        $wishlistProductDetail = [];
                        $criteria = new Criteria();
                        $criteria->addFilter(new EqualsFilter('id', $p->productId));
                        $criteria->addAssociation('cover');
                        $criteria->addAssociation('translations');
                        $productTableData = $this->productRepository->search($criteria, $context);
                        foreach ($productTableData->getElements() as $proData) {
                            $wishlistProductDetail = [];

                            $product = $proData;
                            $parentProduct = null;

                            if ($proData->getParentId()) {
                                $variantCriteria = new Criteria();
                                $variantCriteria->addFilter(new EqualsFilter('id', $proData->getParentId()));
                                $variantCriteria->addAssociation('cover');
                                $variantCriteria->addAssociation('translations');
                                $variantCriteria->addAssociation('prices');
                                $parentProduct = $this->productRepository->search($variantCriteria, $context)->first();
                            }

                            // Name
                            $productName = $product->getTranslated()['name'] ?? '';
                            foreach ($product->getTranslations()->getElements() as $translation) {
                                if ($translation->getLanguageId() === $languageId) {
                                    $productName = $translation->getName();
                                }
                            }

                            if (! $productName && $parentProduct) {
                                $productName = $parentProduct->getTranslated()['name'] ?? '';
                                foreach ($parentProduct->getTranslations()->getElements() as $translation) {
                                    if ($translation->getLanguageId() === $languageId) {
                                        $productName = $translation->getName();
                                    }
                                }
                            }

                            // Image
                            $productImage = $product->getCover()?->getMedia()?->getUrl()
                                ?? ($parentProduct?->getCover()?->getMedia()?->getUrl() ?? '');

                            // Price (safe)
                            $price = 0;
                            $variantPrice = $product->getPrice();
                            $parentPrice = $parentProduct?->getPrice();

                            if ($variantPrice && $variantPrice->count() > 0) {
                                $price = $variantPrice->first()->getGross();
                            } elseif ($parentPrice && $parentPrice->count() > 0) {
                                $price = $parentPrice->first()->getGross();
                            }

                            $wishlistProductDetail['Id'] = $data->getId() ?? '';
                            $wishlistProductDetail['createdAt'] = $data->getCreatedAt() ?? '';
                            $wishlistProductDetail['salesChannelId'] = $data->salesChannelId ?? '';
                            $wishlistProductDetail['salesChannelName'] = $salesChannelContext->getSalesChannel()->getTranslated()['name'] ?? '';
                            $wishlistProductDetail['email'] = $data->getCustomer()->getEmail() ?? '';
                            $wishlistProductDetail['firstName'] = $data->getCustomer()->firstName ?? '';
                            $wishlistProductDetail['lastName'] = $data->getCustomer()->lastName ?? '';
                            $wishlistProductDetail['languageId'] = $languageId ?? '';
                            $wishlistProductDetail['currencySymbol'] = $currencySymbol ?? '';
                            $wishlistProductDetail['languageIdChain'] = $salesChannelContext->getLanguageIdChain() ?? '';
                            $wishlistProductDetail['productName'] = $productName;
                            $wishlistProductDetail['productImage'] = $productImage;
                            $wishlistProductDetail['productTotalPrice'] = $price;
                            $wishlistProductDetail['productId'] = $product->getId();
                            $wishlistProductDetail['customerDomain'] = $customerDomain ?? '';
                            $wishlistProductDetail['customerWishlistId'] = $wishlistData->id;

                            $wishlistInfoData[] = $wishlistProductDetail;
                        }
                    }
                }
                $this->emailService->sendEmailForWishlist($wishlistInfoData, $context, $wishlistReminderEmailId);
            }
        }
        return new JsonResponse([
            'type' => 'success',
            'message' => 'Mail Sent Successfully'
        ]);
    }

    private function getCurrencySymbol($currencyId, $context): string
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('id', $currencyId));
        $currencyData = $this->currencyRepository->search($criteria, $context)->getEntities();
        $currency = $currencyData->first();
        return $currency->getSymbol();
    }
}
