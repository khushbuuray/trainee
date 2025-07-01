<?php

declare(strict_types=1);

namespace ICTECH_ReminderEmailWishlist\Service;

use Psr\Log\LoggerInterface;
use Shopware\Core\Content\Mail\Service\AbstractMailService;
use Shopware\Core\Content\MailTemplate\Aggregate\MailTemplateTranslation\MailTemplateTranslationEntity;
use Shopware\Core\Content\MailTemplate\MailTemplateEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class EmailService
{
    private AbstractMailService $mailService;
    private EntityRepository $mailTemplateRepository;
    private EntityRepository $customCartRepository;
    private EntityRepository $customWishlistRepository;
    private EntityRepository $customRestockRepository;
    private LoggerInterface $logger;
    private SystemConfigService $systemConfigService;
    private EntityRepository $productRepository;

    /**
     * @internal
     */
    public function __construct(
        AbstractMailService $mailService,
        EntityRepository $mailTemplate,
        EntityRepository $customCartRepository,
        EntityRepository $customWishlistRepository,
        EntityRepository $customRestockRepository,
        LoggerInterface $logger,
        SystemConfigService $systemConfigService,
        EntityRepository $productRepository
    ) {
        $this->mailService = $mailService;
        $this->mailTemplateRepository = $mailTemplate;
        $this->customCartRepository = $customCartRepository;
        $this->customWishlistRepository = $customWishlistRepository;
        $this->customRestockRepository = $customRestockRepository;
        $this->logger = $logger;
        $this->systemConfigService = $systemConfigService;
        $this->productRepository = $productRepository;
    }

    public function sendEmailForCart($productInfoData, $context): void
    {
        foreach ($productInfoData as $productMailData) {
            $i = 1;
            $customHtmlTable = '';
            $data = new RequestDataBag();
            $currencySymbol = $productMailData['currencySymbol'];

            foreach ($productMailData['products'] as $productData) {
                $productName = $productData['name'];
                $productImage = $productData['image'];
                $totalPrice = $productData['price'];
                $productUrl = $productMailData['customerDomain'] . $productData['url'];
                if ($i % 2 == 1) {
                    $customHtmlTable .= '<tr>';
                }

                $customHtmlTrTable = "<td>
                <img src='{$productImage}' alt='{$productName}' style='width:250px; height:250px; margin:0 auto; display:block; object-fit:contain;'>
                <br>{$currencySymbol} {$totalPrice}
                <br><a href='{$productUrl}' rel='noreferrer' target='_blank' style='text-decoration:none;'>{$productName}</a>
            </td>";
                $customHtmlTable .= $customHtmlTrTable;

                if ($i % 2 == 0) {
                    $customHtmlTable .= '</tr>';
                }

                $i++;
            }

            if ($i % 2 !== 1) {
                $customHtmlTable .= '</tr>';
            }

            $customHtmlWithTableTable = "<table border='0' cellpadding='10' cellspacing='0' style='width:100%;margin:auto;padding:0 20px;'>{$customHtmlTable}</table>";

            $firstName = $productMailData['firstName'] ?: '';
            $lastName = $productMailData['lastName'] ?: '';
            $customerName = trim($firstName . ' ' . $lastName);

            // Get template
            $mailTemplate = $this->getMailTemplate($context, 'cart_email_reminder');
            $mailTranslation = $this->getMailTranslation($mailTemplate, $productMailData);

            $htmlCustomContent = str_replace(
                ['{firstName}', '{lastName}', '{productName}', '{productURL}', '{salesChannelName}'],
                [$firstName, $lastName, $customHtmlWithTableTable, '', $productMailData['salesChannelName']],
                $mailTranslation->getContentHtml()
            );

            $plainCustomContent = str_replace(
                ['{firstName}', '{lastName}', '{productName}', '{productURL}', '{salesChannelName}'],
                [$firstName, $lastName, $customHtmlWithTableTable, '', $productMailData['salesChannelName']],
                $mailTemplate->getContentPlain()
            );

            $subject = str_replace(
                ['{customerName}', '{salesChannelName}'],
                [$customerName, $productMailData['salesChannelName']],
                $mailTranslation->getSubject()
            );

            // Set mail data
            $data->set('subject', $subject);
            $data->set('senderName', $mailTranslation->getSenderName());
            $data->set('contentHtml', $htmlCustomContent);
            $data->set('contentPlain', $plainCustomContent);
            $data->set('recipients', [$productMailData['email'] => $productMailData['email']]);
            $data->set('salesChannelId', $productMailData['salesChannelId']);

            try {
                $this->mailService->send($data->all(), $context);
                // Delete old reminders
                $cartDays = $this->systemConfigService->get('ICTECH_ReminderEmailWishlist.config.CartDays', $productMailData['salesChannelId']);
                $cartDate = date('Y-m-d', strtotime('-' . $cartDays . ' days'));

                $criteria = new Criteria();
                $criteria->addFilter(
                    new MultiFilter(
                        MultiFilter::CONNECTION_AND,
                        [
                            new ContainsFilter('createdAt', $cartDate),
                            new EqualsFilter('token', $productMailData['token'])
                        ]
                    )
                );

                $cartTokenDelete = $this->customCartRepository->searchIds($criteria, $context);
                $cartDataIds = array_map(static fn ($id) => ['id' => $id], $cartTokenDelete->getIds());

                if (! empty($cartDataIds)) {
                    $this->customCartRepository->delete($cartDataIds, $context);
                }
            } catch (\Exception $e) {
                $this->logger->error('Could not send mail: ' . $e->getMessage() . "\nError Code: " . $e->getCode() . "\nTemplate data: " . json_encode($data->all()));
            }
        }
    }

    public function sendEmailForWishlist($wishlistInfoData, $context, $wishlistReminderEmailId): void
    {
        $i = 1;
        $customHtmlTable = '';
        $data = new RequestDataBag();
        foreach ($wishlistInfoData as $wishlistMailData) {
            $productId = $wishlistMailData['productId'];
            $productName = $wishlistMailData['productName'];
            $productImage = $wishlistMailData['productImage'];
            $currencySymbol = $wishlistMailData['currencySymbol'];
            $totalPrice = $wishlistMailData['productTotalPrice'];
            $firstName = $wishlistMailData['firstName'] ?: '';
            $lastName = $wishlistMailData['lastName'] ?: '';
            $customerDomain = $wishlistMailData['customerDomain'] . '/detail/' . $productId ?: '';
            $customerName = $firstName . ' ' . $lastName;

            // Get Mail template
            $mailTemplate = $this->getMailTemplate($context, 'wishlist_email_reminder');
            $mailTranslation = $this->getMailTranslation($mailTemplate, $wishlistMailData);

            //setup content
            $htmlCustomContent = $mailTranslation->getContentHtml();
            $replaceCustomContent = str_replace('{firstName}', $firstName, $htmlCustomContent);
            $replaceCustomContent = str_replace('{lastName}', $lastName, $replaceCustomContent);

            if ($i % 2 == 1) {
                $customHtmlTable .= '<tr>';
            }
            $customHtmlTrTable = "<td><img src='{$productImage}' alt={$productName} style='width:250px; margin:0 auto; display: block; height: 250px; object-fit: contain;'><br>{$currencySymbol} {$totalPrice}<br><a href={$customerDomain} rel='noreferrer' target='_blank'  style='text-decoration: none;'>{$productName}</a></td>";
            $customHtmlTable .= $customHtmlTrTable;

            $i++;

            if ($i % 2 == 1) {
                $customHtmlTable .= '</tr>';
            }

            $customHtmlWithTableTable = "<table border='0'  cellpadding='10' cellspacing='0' style='width:100%;margin: auto;padding:0 20px;'>
                                        {$customHtmlTable}
                                    </table>";

            $replaceCustomContent = str_replace('{productName}', $customHtmlWithTableTable, $replaceCustomContent);
            $replaceCustomContent = str_replace('{salesChannelName}', $wishlistMailData['salesChannelName'], $replaceCustomContent);

            $htmlCustomContentPlain = $mailTemplate->getContentPlain();
            $replaceCustomContentPlain = str_replace('{firstName}', $firstName, $htmlCustomContentPlain);
            $replaceCustomContentPlain = str_replace('{lastName}', $lastName, $replaceCustomContentPlain);
            $replaceCustomContentPlain = str_replace('{salesChannelName}', $wishlistMailData['salesChannelName'], $replaceCustomContentPlain);

            // Replace Subject dynamic content
            $htmlContentSubject = $mailTranslation->getSubject();
            $replaceHtmlCustomSubject = str_replace('{customerName}', $customerName, $htmlContentSubject);
            $replaceHtmlCustomSubject = str_replace('{salesChannelName}', $wishlistMailData['salesChannelName'], $replaceHtmlCustomSubject);

            $data->set('senderName', $mailTranslation->getSenderName());
            $data->set('contentHtml', $replaceCustomContent);
            $data->set('contentPlain', $replaceCustomContentPlain);
            $data->set('subject', $replaceHtmlCustomSubject);
            $data->set('recipients', [$wishlistMailData['email'] => $wishlistMailData['email']]);
            $data->set('salesChannelId', $wishlistMailData['salesChannelId']);
        }
        try {
            $this->mailService->send($data->all(), $context);
            $this->customWishlistRepository->delete([['id' => $wishlistReminderEmailId]], $context);
        } catch (\Exception $e) {
            $this->logger->error(
                'Could not send mail:' . $e->getMessage() . "\n" . 'Error Code:' . $e->getCode() . "\n" . 'Template data:' . json_encode($data->all()) . "\n"
            );
        }
    }

    public function sendEmailForRestock($restockInfoData, $context): void
    {
        $i = 1;
        $customHtmlTable = '';
        $restockProductIdArray = [];
        foreach ($restockInfoData as $productMailData) {
            if ($productMailData['products']) {
                $currencySymbol = $productMailData['currencySymbol'];

                foreach ($productMailData['products'] as $productData) {
                    $productId = $productData->getId();
                    $customerDomain = $productMailData['customerDomain'] . '/detail/' . $productId ?: '';
                    $totalPrice = $productData->getPrice()->first()->getGross();
                    $productImage = $productData->getCover() ? $productData->getCover()->getMedia()->getUrl() : '';
                    $productName = $productData->getName();
                    $translations = $productData->getTranslations();
                    if ($translations && $translations->count()) {
                        foreach ($translations->getElements() as $productTranslation) {
                            if (
                                $productTranslation->getLanguageId() === $productMailData['languageId']
                                && !empty($productTranslation->getName())
                            ) {
                                $productName = $productTranslation->getName();
                                break;
                            }
                        }
                    }
                    if (empty($productName) && $parent = $productData->getParent()) {
                        $parentTranslations = $parent->getTranslations();
                        if ($parentTranslations && $parentTranslations->count()) {
                            foreach ($parentTranslations->getElements() as $productTranslation) {
                                if (
                                    $productTranslation->getLanguageId() === $productMailData['languageId']
                                    && !empty($productTranslation->getName())
                                ) {
                                    $productName = $productTranslation->getName();
                                    break;
                                }
                            }
                        }
                    }
                    if (empty($productName)) {
                        $defaultLangId = $context->getLanguageId();

                        foreach ($translations->getElements() as $productTranslation) {
                            if (
                                $productTranslation->getLanguageId() === $defaultLangId
                                && !empty($productTranslation->getName())
                            ) {
                                $productName = $productTranslation->getName();
                                break;
                            }
                        }
                        if (empty($productName) && $parent && $parentTranslations) {
                            foreach ($parentTranslations->getElements() as $productTranslation) {
                                if (
                                    $productTranslation->getLanguageId() === $defaultLangId
                                    && !empty($productTranslation->getName())
                                ) {
                                    $productName = $productTranslation->getName();
                                    break;
                                }
                            }
                        }
                    }
                    $customHtmlTrTable = "<td><img src='{$productImage}' alt={$productName} style='width:250px; margin:0 auto; display: block; height: 250px; object-fit: contain;'><br>{$currencySymbol} {$totalPrice}<br><a href={$customerDomain} rel='noreferrer' target='_blank'  style='text-decoration: none;'>{$productName}</a></td>";
                    $customHtmlTable .= $customHtmlTrTable;
                    $restockProductIdArray[] = $productId;
                }
            }

            $firstName = $productMailData['firstName'] ?: '';
            $lastName = $productMailData['lastName'] ?: '';
            $customerName = $firstName . ' ' . $lastName;

            // Get Mail template
            $mailTemplate = $this->getMailTemplate($context, 'restock_email_reminder');
            $mailTranslation = $this->getMailTranslation($mailTemplate, $productMailData);

            $data = new RequestDataBag();

            //setup content
            $htmlCustomContent = $mailTranslation->getContentHtml();
            $replaceCustomContent = str_replace('{firstName}', $firstName, $htmlCustomContent);
            $replaceCustomContent = str_replace('{lastName}', $lastName, $replaceCustomContent);

            if ($i % 2 == 1) {
                $customHtmlTable .= '<tr>';
            }

            if ($i % 2 == 0) {
                $customHtmlTable .= '</tr>';
            }
            $i++;
        }

        if ($i % 2 != 1) {
            $customHtmlTable .= '</tr>';
        }

        $customHtmlWithTableTable = "<table border='0'  cellpadding='10' cellspacing='0' style='width:100%;margin: auto;padding:0 20px;'>
                                       {$customHtmlTable}
                                    </table>";

        // Replace html content dynamic content
        $replaceCustomContent = str_replace('{productName}', $customHtmlWithTableTable, $replaceCustomContent);
        $replaceCustomContent = str_replace('{productURL}', $productImage, $replaceCustomContent);
        $replaceCustomContent = str_replace('{salesChannelName}', $productMailData['salesChannelName'], $replaceCustomContent);

        // Replace Plain content dynamic content
        $htmlCustomContentPlain = $mailTemplate->getContentPlain();
        $replaceCustomContentPlain = str_replace('{firstName}', $firstName, $htmlCustomContentPlain);
        $replaceCustomContentPlain = str_replace('{lastName}', $lastName, $replaceCustomContentPlain);
        $replaceCustomContentPlain = str_replace('{productName}', $customHtmlWithTableTable, $replaceCustomContentPlain);
        $replaceCustomContentPlain = str_replace('{productURL}', $productImage, $replaceCustomContentPlain);
        $replaceCustomContentPlain = str_replace('{salesChannelName}', $productMailData['salesChannelName'], $replaceCustomContentPlain);

        // Replace Subject dynamic content
        $htmlContentSubject = $mailTranslation->getSubject();
        $replaceHtmlCustomSubject = str_replace('{customerName}', $customerName, $htmlContentSubject);
        $replaceHtmlCustomSubject = str_replace('{salesChannelName}', $productMailData['salesChannelName'], $replaceHtmlCustomSubject);

        //check condition mail translation is null or not
        $data->set('senderName', $mailTranslation->getSenderName());
        $data->set('contentHtml', $replaceCustomContent);
        $data->set('contentPlain', $replaceCustomContentPlain);
        $data->set('subject', $replaceHtmlCustomSubject);
        //getting recipients and sales channel id
        $data->set('recipients', [$productMailData['email'] => $productMailData['email']]);
        $data->set('salesChannelId', $productMailData['salesChannelId']);
        try {
            $this->mailService->send($data->all(), $context);
            $restockProductData = $this->getRestockProductData($productMailData, $restockProductIdArray, $context);
            if ($restockProductData) {
                foreach ($restockProductData as $restockProduct) {
                    $this->customRestockRepository->delete([['id' => $restockProduct]], $context);
                }
            }
        } catch (\Exception $e) {
            $this->logger->error(
                'Could not send mail:' . $e->getMessage() . "\n" . 'Error Code:' . $e->getCode() . "\n" . 'Template data:' . json_encode($data->all()) . "\n"
            );
        }
    }

    private function getRestockProductData($productMailData, $restockProductIdArray, $context): array
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('customerId', $productMailData['customerId']));

        // Check if the given productId is a parent
        $variantCriteria = new Criteria();
        $variantCriteria->addFilter(new EqualsAnyFilter('parentId', $restockProductIdArray));
        $variantIds = $this->productRepository->searchIds($variantCriteria, $context)->getIds();

        // Merge parent and variant IDs
        $allProductIds = array_merge($restockProductIdArray, $variantIds);

        $criteria->addFilter(new EqualsAnyFilter('productId', $allProductIds));
        return $this->customRestockRepository->searchIds($criteria, $context)->getIds();
    }
    private function getMailTemplate($context, $templateTechName): ?MailTemplateEntity
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('mailTemplateType.technicalName', $templateTechName));
        $criteria->addAssociation('translations');
        return $this->mailTemplateRepository->search($criteria, $context)->first();
    }
    private function getMailTranslation($mailTemplate, $mailData): ?MailTemplateTranslationEntity
    {
        $mailTranslations = $mailTemplate->getTranslations();
        return $mailTranslations->filter(function ($element) use ($mailData) {
            return $element->getLanguageId() === $mailData['languageId'];
        })->first();
    }
}
