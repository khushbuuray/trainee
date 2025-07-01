<?php

declare(strict_types=1);

namespace ICTECH_ReminderEmailWishlist\Storefront\Controller;

use ICTECH_ReminderEmailWishlist\Service\EmailService;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SalesChannel\Context\AbstractSalesChannelContextFactory;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Attribute\Route;

#[Route(defaults: ['_routeScope' => ['storefront']])]
class CustomRestockEmailController extends StorefrontController
{
    private EntityRepository $currencyRepository;
    private EntityRepository $customerRepository;
    private EntityRepository $customRestockRepository;
    private AbstractSalesChannelContextFactory $salesChannelContextFactory;
    private EmailService $emailService;
    private EntityRepository $productRepository;

    /**
     * @internal
     */
    public function __construct(
        EntityRepository $currencyRepository,
        EntityRepository $customerRepository,
        EntityRepository $customRestockRepository,
        AbstractSalesChannelContextFactory $salesChannelContextFactory,
        EmailService $emailService,
        EntityRepository $productRepository
    ) {
        $this->currencyRepository = $currencyRepository;
        $this->customerRepository = $customerRepository;
        $this->customRestockRepository = $customRestockRepository;
        $this->salesChannelContextFactory = $salesChannelContextFactory;
        $this->emailService = $emailService;
        $this->productRepository = $productRepository;
    }

    #[Route(path: '/api/restock/reminder/email', name: 'api.restock.reminder.email', methods: ['GET'])]
    public function customRestockProductEmailReminder($salesChannelId, Context $context, RequestStack $requestStack): JsonResponse
    {
        // Get configuration values
        $customerArray = [];
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('salesChannelId', $salesChannelId));
        $criteria->addAssociation('customer');
        $restockTableData = $this->customRestockRepository->search($criteria, $context);

        // Update stock information
        foreach ($restockTableData->getElements() as $restockData) {
            $productId = $restockData->getProductId();

            $criteria = new Criteria();
            $criteria->addFilter(new EqualsFilter('id', $productId));
            $productTableData = $this->productRepository->search($criteria, $context)->getElements();

            foreach ($productTableData as $productData) {
                if ($productData->getId() === $productId && $productData->getAvailableStock() <= 0) {
                    $this->customRestockRepository->upsert([
                        [
                            'id' => $restockData->getId(),
                            'stock' => $productData->getAvailableStock(),
                        ]
                    ], $context);
                }
            }
        }

        foreach ($restockTableData->getElements() as $restockDataT) {
            if ($restockDataT->getStock() <= 0) {
                $customerId = $restockDataT->customerId;
                $productId = $restockDataT->getProductId();

                // Fetch product information
                $criteria = new Criteria();
                $criteria->addFilter(new EqualsFilter('id', $productId));
                $criteria->addAssociation('cover');
                $criteria->addAssociation('prices');
                $criteria->addAssociation('translations');
                $productTableData = $this->productRepository->search($criteria, $context)->getElements();

                if (! empty($productTableData)) {
                    foreach ($productTableData as $product) {
                        if ($product->getAvailableStock() > $restockDataT->getStock()) {
                            // Check if the product has a parent (variant case)
                            if ($product->getParentId()) {
                                $criteria = new Criteria([$product->getParentId()]);
                                $criteria->addAssociation('cover');
                                $criteria->addAssociation('prices');
                                $criteria->addAssociation('translations');
                                $parentProduct = $this->productRepository->search($criteria, $context)->first();

                                if ($parentProduct) {
                                    $productCover = $product->getCover() ?: $parentProduct->getCover();
                                    $productPrice = $product->getPrice() ?: $parentProduct->getPrice();
                                    if ($productCover) {
                                        $product->setCover($productCover);
                                    }
                                    if ($productPrice) {
                                        $product->setPrice($productPrice);
                                    }
                                    $product->setParent($parentProduct);
                                }
                            }

                            if (! isset($customerArray[$customerId])) {
                                $customerArray[$customerId] = $restockDataT;
                                $customerArray[$customerId]->products = [];
                            }

                            $productKey = $product->getId();

                            if (! isset($customerArray[$customerId]->products[$productKey])) {
                                $customerArray[$customerId]->products[$productKey] = $product;
                            }
                        }
                    }
                }
            }
        }

        // Process email sending
        foreach ($customerArray as $customerData) {
            if (! empty($customerData->products)) {
                $dateUpdateAt = $customerData->getUpdatedAt() ?
                    $customerData->getUpdatedAt()->format('Y-m-d') :
                    $customerData->getCreatedAt()->format('Y-m-d');

                if ($dateUpdateAt) {
                    $token = Uuid::randomHex();
                    $customerDomain = null;
                    $salesChannelContext = $this->salesChannelContextFactory->create($token, $customerData->salesChannelId);
                    $salesChannelId = $salesChannelContext->getSalesChannelId();
                    $currencySymbol = $this->getCurrencySymbol($customerData->currencyId, $context);

                    // Fetch customer language ID
                    $criteria = new Criteria();
                    $criteria->addFilter(new EqualsFilter('id', $customerData->customerId));
                    $customerLanguageData = $this->customerRepository->search($criteria, $context)->first();
                    $languageId = $customerLanguageData->getLanguageId();

                    // Determine the correct customer domain
                    $getDomainElements = $salesChannelContext->getSalesChannel()->getDomains()->getElements();
                    $request = $requestStack->getCurrentRequest();
                    $scheme = $request ? $request->getScheme() : 'https';
                    foreach ($getDomainElements as $item) {
                        if ($item->getLanguageId() === $languageId && parse_url($item->getUrl(), PHP_URL_SCHEME) === $scheme) {
                            $customerDomain = $item->getUrl();
                            break;
                        }
                    }
                    // Prepare to restock product details
                    $restockProductDetail = [
                        'Id' => $customerData->getId() ?? '',
                        'token' => $customerData->getToken() ?? '',
                        'createdAt' => $customerData->getCreatedAt() ?? '',
                        'products' => array_values($customerData->products ?? []),
                        'customerId' => $customerData->customerId ?? '',
                        'salesChannelId' => $salesChannelId ?? '',
                        'salesChannelName' => $salesChannelContext->getSalesChannel()->getTranslated()['name'] ?? '',
                        'languageId' => $languageId ?? '',
                        'currencySymbol' => $currencySymbol ?? '',
                        'languageIdChain' => $salesChannelContext->getLanguageIdChain() ?? '',
                        'firstName' => $customerData->customer->firstName ?? '',
                        'lastName' => $customerData->customer->lastName ?? '',
                        'email' => $customerData->customer->email ?? '',
                        'customerDomain' => $customerDomain ?? '',
                    ];

                    $this->emailService->sendEmailForRestock([$restockProductDetail], $context);
                }
            }
        }

        return new JsonResponse([
            'type' => 'success',
            'message' => 'Restock Mail Sent Successfully',
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
