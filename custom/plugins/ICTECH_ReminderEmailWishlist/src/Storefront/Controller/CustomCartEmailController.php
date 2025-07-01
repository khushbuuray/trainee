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
class CustomCartEmailController extends StorefrontController
{
    private EntityRepository $currencyRepository;
    private EntityRepository $customCartRepository;
    private EntityRepository $productRepository;
    private AbstractSalesChannelContextFactory $salesChannelContextFactory;
    private EmailService $emailService;
    private EntityRepository $customerRepository;
    private SystemConfigService $systemConfigService;

    public function __construct(
        EntityRepository $currencyRepository,
        EntityRepository $customCartRepository,
        EntityRepository $productRepository,
        AbstractSalesChannelContextFactory $salesChannelContextFactory,
        EmailService $emailService,
        EntityRepository $customerRepository,
        SystemConfigService $systemConfigService
    ) {
        $this->currencyRepository = $currencyRepository;
        $this->customCartRepository = $customCartRepository;
        $this->productRepository = $productRepository;
        $this->salesChannelContextFactory = $salesChannelContextFactory;
        $this->emailService = $emailService;
        $this->customerRepository = $customerRepository;
        $this->systemConfigService = $systemConfigService;
    }

    #[Route(path: '/api/cart/reminder/customCartProductEmailReminder', name: 'api.cart.reminder.customCartProductEmailReminder', methods: ['GET'])]
    public function customCartProductEmailReminder($salesChannelId, Context $context, RequestStack $requestStack): JsonResponse
    {
        $cartDays = $this->systemConfigService->get('ICTECH_ReminderEmailWishlist.config.CartDays', $salesChannelId);
        $cartDate = date('Y-m-d', strtotime('-' . $cartDays . ' days'));

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('salesChannelId', $salesChannelId));
        $criteria->addFilter(new ContainsFilter('createdAt', $cartDate));
        $getCartReminderData = $this->customCartRepository->search($criteria, $context);

        $customCartData = $getCartReminderData->getElements();
        $cartTokenArray = [];
        foreach ($customCartData as $cartData) {
            $customerId = $cartData->customerId;
            $productId = $cartData->getProductId();

            // Get customer data including language ID
            $criteria = new Criteria();
            $criteria->addFilter(new EqualsFilter('id', $customerId));
            $customerData = $this->customerRepository->search($criteria, $context)->first();
            $languageId = $customerData->getLanguageId(); // Fetching customer's languageId

            // Fetch product data based on language
            $criteria = new Criteria();
            $criteria->addFilter(new EqualsFilter('id', $productId));
            $criteria->addAssociation('cover');
            $criteria->addAssociation('prices');
            $criteria->addAssociation('translations'); // Ensuring translations are retrieved
            $productTableData = $this->productRepository->search($criteria, $context)->getElements();

            foreach ($productTableData as $variantProduct) {
                $parentProduct = null;

                if ($variantProduct->getParentId()) {
                    $parentCriteria = new Criteria();
                    $parentCriteria->addFilter(new EqualsFilter('id', $variantProduct->getParentId()));
                    $parentCriteria->addAssociation('cover');
                    $parentCriteria->addAssociation('translations');
                    $parentCriteria->addAssociation('prices');
                    $parentProduct = $this->productRepository->search($parentCriteria, $context)->first();
                }

                // Name
                $productName = $variantProduct->getTranslated()['name'] ?? '';
                foreach ($variantProduct->getTranslations()->getElements() as $translation) {
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

                $cover = $variantProduct->getCover() ?? ($parentProduct ? $parentProduct->getCover() : null);
                $image = $cover ? $cover->getMedia()->getUrl() : '';

                $variantPrice = $variantProduct->getPrice();
                $parentPrice = $parentProduct ? $parentProduct->getPrice() : null;

                $price = 0;
                if ($variantPrice && $variantPrice->count() > 0) {
                    $price = $variantPrice->first()->getGross();
                } elseif ($parentPrice && $parentPrice->count() > 0) {
                    $price = $parentPrice->first()->getGross();
                }

                // Prepare customer data and array
                if (! isset($cartTokenArray[$customerId])) {
                    $cartTokenArray[$customerId] = clone $cartData;
                    $cartTokenArray[$customerId]->products = [];
                    $cartTokenArray[$customerId]->customerId = $customerId;
                    $cartTokenArray[$customerId]->email = $customerData->getEmail() ?? '';
                    $cartTokenArray[$customerId]->firstName = $customerData->getFirstName() ?? '';
                    $cartTokenArray[$customerId]->lastName = $customerData->getLastName() ?? '';
                    $cartTokenArray[$customerId]->languageId = $languageId;
                }

                $cartTokenArray[$customerId]->products[] = [
                    'productId' => $variantProduct->getId(),
                    'name' => $productName,
                    'image' => $image,
                    'price' => $price,
                    'url' => '/detail/' . $variantProduct->getId()
                ];
            }
        }
        // Process email sending
        foreach ($cartTokenArray as $cartDataInfo) {
            if (count($cartDataInfo->products) > 0) {
                $token = Uuid::randomHex();
                $customerDomain = null;
                $salesChannelContext = $this->salesChannelContextFactory->create($token, $cartDataInfo->salesChannelId);
                $salesChannelId = $salesChannelContext->getSalesChannelId();
                $currencySymbol = $this->getCurrencySymbol($cartDataInfo->currencyId, $context);

                $getDomainElements = $salesChannelContext->getSalesChannel()->getDomains()->getElements();

                $request = $requestStack->getCurrentRequest();
                $scheme = $request ? $request->getScheme() : 'https';
                foreach ($getDomainElements as $item) {
                    if ($item->getLanguageId() === $cartDataInfo->languageId) {
                        $domainUrl = $item->getUrl();
                        if (parse_url($domainUrl, PHP_URL_SCHEME) === $scheme) {
                            $customerDomain = $domainUrl;
                            break;
                        }
                    }
                }

                $cartProductDetail = [
                    'Id' => $cartDataInfo->getId() ?? '',
                    'token' => $cartDataInfo->getToken() ?? '',
                    'createdAt' => $cartDataInfo->getCreatedAt() ?? '',
                    'products' => $cartDataInfo->products ?? '',
                    'salesChannelId' => $salesChannelId ?? '',
                    'salesChannelName' => $salesChannelContext->getSalesChannel()->getTranslated()['name'] ?? '',
                    'languageId' => $cartDataInfo->languageId ?? '',
                    'currencySymbol' => $currencySymbol ?? '',
                    'languageIdChain' => $salesChannelContext->getLanguageIdChain() ?? '',
                    'email' => $cartDataInfo->email ?? '',
                    'firstName' => $cartDataInfo->firstName ?? '',
                    'lastName' => $cartDataInfo->lastName ?? '',
                    'customerId' => $cartDataInfo->customerId ?? '',
                    'customerDomain' => $customerDomain ?? ''
                ];

                $this->emailService->sendEmailForCart([$cartProductDetail], $context);
            }
        }

        return new JsonResponse([
            'type' => 'success',
            'message' => 'Cart Mail Sent Successfully'
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
