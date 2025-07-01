<?php declare(strict_types=1);

namespace ICTECH_LimitedStockNotification\Service;

use Psr\Log\LoggerInterface;
use Shopware\Core\Content\Mail\Service\AbstractMailService;
use Shopware\Core\Content\MailTemplate\MailTemplateEntity;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Api\Context\SystemSource;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\Context\AbstractSalesChannelContextFactory;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class EmailService
{
    private AbstractMailService $mailService;
    private EntityRepository $mailTemplateRepository;
    private LoggerInterface $logger;
    private SystemConfigService $systemConfigService;
    private EntityRepository $productsRepository;
    private EntityRepository $ictCartWishlistProductRepository;
    private AbstractSalesChannelContextFactory $salesChannelContextFactory;
    Private EntityRepository $languageRepository;

    public function __construct(
        AbstractMailService $mailService,
        EntityRepository $mailTemplateRepository,
        LoggerInterface $logger,
        SystemConfigService $systemConfigService,
        EntityRepository $productsRepository,
        EntityRepository $ictCartWishlistProductRepository,
        AbstractSalesChannelContextFactory $salesChannelContextFactory,
        EntityRepository $languageRepository
    ) {
        $this->mailService = $mailService;
        $this->mailTemplateRepository = $mailTemplateRepository;
        $this->logger = $logger;
        $this->systemConfigService = $systemConfigService;
        $this->productsRepository = $productsRepository;
        $this->ictCartWishlistProductRepository = $ictCartWishlistProductRepository;
        $this->salesChannelContextFactory = $salesChannelContextFactory;
        $this->languageRepository = $languageRepository;
    }

    public function sendEmailWishlist($ProductInfoData, $context, $customer, $wishlistId): void
    {
        $languageId = $customer->get('languageId');

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('id', $languageId));
        $criteria->addAssociation('locale');
        $language = $this->languageRepository->search($criteria, $context)->first();
        $localeCode = $language->getLocale()->getCode();
        $languageContext = $this->getLanguageContext($context, [$languageId]);

        if ($localeCode === 'de-DE') {
            $wishlistText = 'Wunschzettel';
        } elseif ($localeCode === 'en-GB') {
            $wishlistText = 'Wishlist';
        }

        foreach ($ProductInfoData as $productData) {
            $salesChannelId = $customer->get('salesChannelId');
            $configStock = $this->systemConfigService->get('ICTECH_LimitedStockNotification.config.limitedStockAlert', $salesChannelId);

            $productStock = $productData['availableStock'];
            $customerId = $customer->get('id');

            $criteria = new Criteria();
            $criteria->addFilter(new EqualsFilter('id', $productData['id']));
            $criteria->addAssociation('cover');
            $criteria->addAssociation('translations');
            $criteria->addAssociation('manufacturer');
            $criteria->addAssociation('options');
            $criteria->addAssociation('options.group');
            $criteria->addAssociation('properties');
            $productInfo = $this->productsRepository->search($criteria, $languageContext)->first();

            $productInfo->cover = $productInfo->getCover();
            $productName = $productInfo->getTranslated()['name'];

            if ($productInfo->get('parentId')) {
                $criteria = new Criteria();
                $criteria->addFilter(new EqualsFilter('id', $productInfo->get('parentId')));
                $criteria->addAssociation('cover');
                $criteria->addAssociation('translations');
                $criteria->addAssociation('options');
                $criteria->addAssociation('manufacturer');
                $criteria->addAssociation('properties');
                $productInfoParent = $this->productsRepository->search($criteria, $languageContext)->first();
                $productInfo->cover = $productInfoParent->getCover();
                $productName = $productInfoParent->getTranslated()['name'];
            }

            $criteria = new Criteria();
            $criteria->addFilter(new EqualsFilter('productId', $productData['id']));
            $criteria->addFilter(new EqualsFilter('wishlistId', $wishlistId));
            $cartWishlistProduct = $this->ictCartWishlistProductRepository->search($criteria, $context)->first();

            if ($cartWishlistProduct == null or $cartWishlistProduct->get('mailSent') === false) {
                if ($productStock <= $configStock) {
                    $email = $customer->get('email');
                    $firstname = $customer->get('firstName');
                    $lastname = $customer->get('lastName');
                    $customerName = $firstname .'  '. $lastname;

                    $data = new RequestDataBag();

                    //setup content
                    $mailTemplate = $this->getMailTemplate($context);
                    $mailTranslations = $mailTemplate->getTranslations();
                    $mailTranslation = $mailTranslations->filter(function ($element) use ($customer) {
                        return $element->getLanguageId() === $customer->get('languageId');
                    })->first();

                    $manufactureName = 'Default Manufacturer';
                    if (array_key_exists('manufacturer', $productData)) {
                        if ($productData['manufacturer'] && $productData['manufacturer']['name']) {
                            $manufactureName = $productData['manufacturer']['name'];
                        }
                    } elseif (isset($productInfoParent) && $productInfoParent->get('manufacturer') && $productInfoParent->get('translated')['name']) {
                        $manufactureName = $productInfoParent->get('manufacturer')->get('translated')['name'];
                    }

                    $availableStock = (string) $productInfo->get('availableStock');

                    $tokenUuid = Uuid::randomHex();
                    $salesChannelContext = $this->salesChannelContextFactory->create($tokenUuid, $salesChannelId);

                    $customerDomain = null;
                    $getDomainElements = $salesChannelContext->getSalesChannel()->getDomains()->getElements();
                    foreach ($getDomainElements as $item) {

                        if ($item->getLanguageId() == $languageId) {
                            $customerDomain = $item->getUrl();
                        }
                    }
                    $customerDomain = $customerDomain . '/detail/' . $productData['id'] ?: '';

                    $coverImageUrl = '';
                    if ($productInfo->get('cover')) {
                        $coverImageUrl = str_replace(' ', '%20', $productInfo->get('cover')->getMedia()->getUrl());
                    }

                    $productUrl = '';
                    if (!empty($coverImageUrl)) {
                        $productUrl = '<img src=' . $coverImageUrl . ' alt=' . $productName . ' style="width:100px; margin:0; display: block; height: 100px; object-fit: contain;" rel="noreferrer">';
                    }
                    $productUrl .= '<button><a href=' . $customerDomain . '>' . $productName . '</a></button>';

                    $htmlCustomContent = $mailTranslation->getContentHtml();
                    $replaceCustomContent = str_replace('{customer}', $customerName, $htmlCustomContent);
                    $replaceCustomContent = str_replace('{productName}', $productName, $replaceCustomContent);
                    $replaceCustomContent = str_replace('{productImage}', $productUrl, $replaceCustomContent);
                    $replaceCustomContent = str_replace('{productBrand}', $manufactureName, $replaceCustomContent);
                    $replaceCustomContent = str_replace('{availableStock}', $availableStock, $replaceCustomContent);
                    $replaceCustomContent = str_replace('{cart/wishlist}', $wishlistText, $replaceCustomContent);

                    $htmlCustomContentPlain = $mailTemplate->getTranslation('contentPlain');
                    $replaceCustomContentPlain = str_replace('{customer}', $customerName, $htmlCustomContentPlain);
                    $replaceCustomContentPlain = str_replace('{productName}', $productName, $replaceCustomContentPlain);
                    $replaceCustomContentPlain = str_replace('{productImage}', $productUrl, $replaceCustomContentPlain);
                    $replaceCustomContentPlain = str_replace('{productBrand}', $manufactureName, $replaceCustomContentPlain);
                    $replaceCustomContentPlain = str_replace('{availableStock}', $availableStock, $replaceCustomContentPlain);
                    $replaceCustomContentPlain = str_replace('{cart/wishlist}', $wishlistText, $replaceCustomContentPlain);

                    $htmlCustomContentPlainSubject = $mailTranslation->getSubject();
                    $replaceHtmlCustomSubject = str_replace('{productName}', $productName, $htmlCustomContentPlainSubject);

                    //check condition mail translation is null or not
                    if ($mailTranslation === null) {
                        $data->set('senderName', $mailTranslation->getSenderName());
                        $data->set('contentHtml', $replaceCustomContent);
                        $data->set('contentPlain', $replaceCustomContentPlain);
                        $data->set('subject', $replaceHtmlCustomSubject);
                    } else {
                        $data->set('senderName', $mailTranslation->getSenderName());
                        $data->set('contentHtml', $replaceCustomContent);
                        $data->set('contentPlain', $replaceCustomContentPlain);
                        $data->set('subject', $replaceHtmlCustomSubject);
                    }

                    try {
                        $data->set('recipients', [$email => $email]);
                        $data->set('salesChannelId', $salesChannelId);
                        $this->mailService->send($data->all(), $context);
                        if ($cartWishlistProduct) {
                            $productCartWishlistId = $cartWishlistProduct->getId();
                        } else {
                            $productCartWishlistId = Uuid::randomHex();
                        }
                        $this->updateWishlistProductHistory($productCartWishlistId, $customerId, $salesChannelId, $wishlistId, $productData['id'], $context);

                    } catch (\Exception $e) {
                        $this->logger->error(
                            "Could not send mail:\n"
                            . $e->getMessage() . "\n"
                            . 'Error Code:' . $e->getCode() . "\n"
                            . "Template data: \n"
                            . json_encode($data->all()) . "\n"
                        );
                    }
                }
            }
        }
    }
    public function sendEmailCart($ProductInfoData, $context, $customer, $token): void
    {
        $languageId = $customer->get('languageId');

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('id', $languageId));
        $criteria->addAssociation('locale');
        $language = $this->languageRepository->search($criteria, $context)->first();
        $localeCode = $language->getLocale()->getCode();

        $languageContext = $this->getLanguageContext($context, [$languageId]);

        if ($localeCode === 'de-DE') {
            $cartText = 'Warenkorb';
        } elseif ($localeCode === 'en-GB') {
            $cartText = 'cart';
        }

        foreach ($ProductInfoData as $productData) {
            $salesChannelId = $customer->get('salesChannelId');
            $customerId = $customer->get('id');
            $configStock = $this->systemConfigService->get('ICTECH_LimitedStockNotification.config.limitedStockAlert', $salesChannelId);

            $criteria = new Criteria();
            $criteria->addFilter(new EqualsFilter('id', $productData['id']));
            $criteria->addAssociation('cover');
            $criteria->addAssociation('translations');
            $criteria->addAssociation('manufacturer');
            $criteria->addAssociation('options');
            $criteria->addAssociation('options.group');
            $criteria->addAssociation('properties');
            $productInfo = $this->productsRepository->search($criteria, $languageContext)->first();

            $productInfo->cover = $productInfo->getCover();
            $productName = $productInfo->getTranslated()['name'];

            if ($productInfo->get('parentId')) {
                $criteria = new Criteria();
                $criteria->addFilter(new EqualsFilter('id', $productInfo->get('parentId')));
                $criteria->addAssociation('cover');
                $criteria->addAssociation('translations');
                $criteria->addAssociation('options');
                $criteria->addAssociation('manufacturer');
                $criteria->addAssociation('properties');
                $productInfoParent = $this->productsRepository->search($criteria, $languageContext)->first();
                $productInfo->cover = $productInfoParent->getCover();
                $productName = $productInfoParent->getTranslated()['name'];
            }

            $productStock = $productInfo->get('availableStock');

            $criteria = new Criteria();
            $criteria->addFilter(new EqualsFilter('productId', $productData['id']));
            $criteria->addFilter(new EqualsFilter('cartToken', $token));
            $cartWishlistProduct = $this->ictCartWishlistProductRepository->search($criteria, $context)->first();

            if ($cartWishlistProduct == null or $cartWishlistProduct->get('mailSent') === false) {
                if ($productStock <= $configStock) {
                    $email = $customer->get('email');
                    $firstname = $customer->get('firstName');
                    $lastname = $customer->get('lastName');
                    $customerName = $firstname .'  '. $lastname;

                    $data = new RequestDataBag();

                    //setup content
                    $mailTemplate = $this->getMailTemplate($context);
                    $mailTranslations = $mailTemplate->getTranslations();
                    $mailTranslation = $mailTranslations->filter(function ($element) use ($customer) {
                        return $element->getLanguageId() === $customer->get('languageId');
                    })->first();

                    $availableStock = (string) $productInfo->get('availableStock');

                    $manufactureName = 'Default Manufacturer';
                    if ($productInfo->getManufacturerId()) {
                        if ($productInfo->getManufacturer()) {
                            $manufactureName = $productInfo->getManufacturer()->getName();
                        }
                    } elseif (isset($productInfoParent) && $productInfoParent->get('manufacturer') && $productInfoParent->get('translated')['name']) {
                        $manufactureName = $productInfoParent->get('manufacturer')->get('translated')['name'];
                    }

                    $tokenUuid = Uuid::randomHex();
                    $salesChannelContext = $this->salesChannelContextFactory->create($tokenUuid, $salesChannelId);

                    $customerDomain = null;
                    $getDomainElements = $salesChannelContext->getSalesChannel()->getDomains()->getElements();

                    foreach ($getDomainElements as $item) {

                        if ($item->getLanguageId() == $languageId) {
                            $customerDomain = $item->getUrl();
                        }
                    }
                    $customerDomain = $customerDomain . '/detail/' . $productData['id'] ?: '';

                    $coverImageUrl = '';
                    if ($productInfo->get('cover')) {
                        $coverImageUrl = str_replace(' ', '%20', $productInfo->get('cover')->getMedia()->getUrl());
                    }

                    $productUrl = '';
                    if (!empty($coverImageUrl)) {
                        $productUrl = '<img src=' . $coverImageUrl . ' alt=' . $productName . ' style="width:100px; margin:0; display: block; height: 100px; object-fit: contain;" rel="noreferrer">';
                    }
                    $productUrl .= '<button><a href=' . $customerDomain . '>' . $productName . '</a></button>';

                    $htmlCustomContent = $mailTranslation->getContentHtml();
                    $replaceCustomContent = str_replace('{customer}', $customerName, $htmlCustomContent);
                    $replaceCustomContent = str_replace('{productName}', $productName, $replaceCustomContent);
                    $replaceCustomContent = str_replace('{productImage}', $productUrl, $replaceCustomContent);
                    $replaceCustomContent = str_replace('{availableStock}', $availableStock, $replaceCustomContent);
                    $replaceCustomContent = str_replace('{productBrand}', $manufactureName, $replaceCustomContent);
                    $replaceCustomContent = str_replace('{cart/wishlist}', $cartText, $replaceCustomContent);

                    $htmlCustomContentPlain = $mailTemplate->getTranslation('contentPlain');
                    $replaceCustomContentPlain = str_replace('{customer}', $customerName, $htmlCustomContentPlain);
                    $replaceCustomContentPlain = str_replace('{productName}', $productName, $replaceCustomContentPlain);
                    $replaceCustomContentPlain = str_replace('{productImage}', $productUrl, $replaceCustomContentPlain);
                    $replaceCustomContentPlain = str_replace('{availableStock}', $availableStock, $replaceCustomContentPlain);
                    $replaceCustomContentPlain = str_replace('{productBrand}', $manufactureName, $replaceCustomContentPlain);
                    $replaceCustomContentPlain = str_replace('{cart/wishlist}', $cartText, $replaceCustomContentPlain);

                    $htmlCustomContentPlainSubject = $mailTranslation->getSubject();
                    $replaceHtmlCustomSubject = str_replace('{productName}', $productName, $htmlCustomContentPlainSubject);

                    //check condition mail translation is null or not
                    if ($mailTranslation === null) {
                        $data->set('senderName', $mailTranslation->getSenderName());
                        $data->set('contentHtml', $replaceCustomContent);
                        $data->set('contentPlain', $replaceCustomContentPlain);
                        $data->set('subject', $replaceHtmlCustomSubject);
                    } else {
                        $data->set('senderName', $mailTranslation->getSenderName());
                        $data->set('contentHtml', $replaceCustomContent);
                        $data->set('contentPlain', $replaceCustomContentPlain);
                        $data->set('subject', $replaceHtmlCustomSubject);
                    }

                    try {
                        $data->set('recipients', [$email => $email]);
                        $data->set('salesChannelId', $salesChannelId);
                        $this->mailService->send($data->all(), $context);
                        if ($cartWishlistProduct) {
                            $productCartWishlistId = $cartWishlistProduct->getId();
                        } else {
                            $productCartWishlistId = Uuid::randomHex();
                        }
                        $this->updateCartProductHistory($productCartWishlistId, $customerId, $salesChannelId, $token, $productData['id'], $context);
                    } catch (\Exception $e) {
                        $this->logger->error(
                            "Could not send mail:\n"
                            . $e->getMessage() . "\n"
                            . 'Error Code:' . $e->getCode() . "\n"
                            . "Template data: \n"
                            . json_encode($data->all()) . "\n"
                        );
                    }
                }
            }
        }
    }
    private function getMailTemplate($context): ?MailTemplateEntity
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('mailTemplateType.technicalName', 'cart_wishlist_reminded'));
        $criteria->addAssociation('translations');

        return $this->mailTemplateRepository
            ->search($criteria, $context)
            ->first();
    }
    public function updateCartProductHistory($productCartWishlistId, $customerId, $salesChannelId, $token, $productId, Context $context): void
    {
        $this->ictCartWishlistProductRepository->upsert([
            [
                'id' => $productCartWishlistId,
                'cartToken' => $token,
                'customerId' => $customerId,
                'salesChannelId' => $salesChannelId,
                'productId' => $productId,
                'mailSent' => true
            ]
        ], $context);
    }
    public function updateWishlistProductHistory($productCartWishlistId, $customerId, $salesChannelId, $wishlistId, $productId, Context $context): void
    {
        $this->ictCartWishlistProductRepository->upsert([
            [
                'id' => $productCartWishlistId,
                'wishlistId' => $wishlistId,
                'customerId' => $customerId,
                'salesChannelId' => $salesChannelId,
                'productId' => $productId,
                'mailSent' => true
            ]
        ], $context);
    }

    private function getLanguageContext(Context $context, array $languageIdChain): Context
    {
        if (!in_array(Defaults::LANGUAGE_SYSTEM, $languageIdChain)) {
            $languageIdChain[] = Defaults::LANGUAGE_SYSTEM;
        }

        return new Context(
            new SystemSource(),
            [],
            Defaults::CURRENCY,
            $languageIdChain,
            $context->getVersionId(),
            $context->getCurrencyFactor(),
            true
        );
    }
}
