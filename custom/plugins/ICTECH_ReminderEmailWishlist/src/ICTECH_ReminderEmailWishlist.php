<?php

declare(strict_types=1);

namespace ICTECH_ReminderEmailWishlist;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\Framework\Uuid\Uuid;

class ICTECH_ReminderEmailWishlist extends Plugin
{
    public const TEMPLATE_TYPE_NAME = 'Cart Reminder Email';
    public const TEMPLATE_TYPE_NAME_WISHLIST = 'Wishlist Reminder Email';
    public const TEMPLATE_TYPE_NAME_RESTOCK = 'Restock Reminder Email';
    public const TEMPLATE_TYPE_TECHNICAL_NAME = 'cart_email_reminder';
    public const TEMPLATE_TYPE_TECHNICAL_NAME_WISHLIST = 'wishlist_email_reminder';
    public const TEMPLATE_TYPE_TECHNICAL_NAME_RESTOCK = 'restock_email_reminder';
    public const SUBJECT_ENG = "{customerName}, Make a purchase of the items in your cart with {salesChannelName}! ";
    public const SUBJECT_ENG_WISHLIST = "{customerName}, Make a purchase of the items in your wishlist with {salesChannelName}!";
    public const SUBJECT_ENG_RESTOCK = "{customerName}, Items back in stock from your wishlist and cart with {salesChannelName}!";
    public const SUBJECT_DE = "{customerName}, Kaufen Sie die Artikel in Ihrem Einkaufswagen mit {salesChannelName}!";
    public const SUBJECT_DE_WISHLIST = "{customerName}, Kaufen Sie die Artikel auf Ihrem Wunschzettel mit {salesChannelName}!";
    public const SUBJECT_DE_RESTOCK = "{customerName}, Artikel aus Ihrer Wunschliste und Ihrem Warenkorb wieder auf Lager mit {salesChannelName}!";

    public const CONTAIN_PLAIN_EN = "We noticed that you recently added some fantastic products to your shopping cart at {salesChannelName}, but haven't completed your purchase yet. We wanted to remind you about the items you left behind and offer some assistance in case you have any questions or concerns.";
    public const CONTAIN_PLAIN_EN_WISHLIST = "We noticed that you recently added some fantastic products to your shopping wishlist at {salesChannelName}, but haven't completed your purchase yet. We wanted to remind you about the items you left behind and offer some assistance in case you have any questions or concerns.";
    public const CONTAIN_PLAIN_EN_RESTOCK = "Good news! Items you previously added to your wishlist or cart are now back in stock at {salesChannelName}. Don't miss the chance to grab them before they sell out again!";

    public const CONTAIN_PLAIN_DE = "Uns ist aufgefallen, dass Sie kürzlich einige fantastische Produkte in Ihren Warenkorb bei {salesChannelName} gelegt, Ihren Einkauf aber noch nicht abgeschlossen haben. Wir möchten Sie an die Artikel erinnern, die Sie zurückgelassen haben, und Ihnen Hilfe anbieten, falls Sie Fragen oder Bedenken haben.";
    public const CONTAIN_PLAIN_DE_WISHLIST = "Wir haben bemerkt, dass Sie kürzlich einige fantastische Produkte zu Ihrer Wunschliste bei {salesChannelName} hinzugefügt, aber Ihren Kauf noch nicht abgeschlossen haben. Wir möchten Sie an die Artikel erinnern, die Sie zurückgelassen haben, und Ihnen Hilfe anbieten, falls Sie Fragen oder Bedenken haben.";
    public const CONTAIN_PLAIN_DE_RESTOCK = "Gute Nachrichten! Artikel, die Sie zuvor zu Ihrer Wunschliste oder Ihrem Warenkorb hinzugefügt haben, sind jetzt wieder auf Lager bei {salesChannelName}. Verpassen Sie nicht die Chance, sie zu ergattern, bevor sie wieder ausverkauft sind!";
    public const CONTAIN_HTML_EN = "<table style='width:800px;background-color:#ececec; padding-bottom:0px; border-spacing: 0;margin:0 auto;'>
    <tr>
        <td style='text-align: center;'>
            <p style='line-height:18px;background-color:#ececec;letter-spacing:1px'> Dear {firstName} {lastName} , </p>
            <p style='line-height:18px;background-color:#ececec;letter-spacing:1px'> We hope this email finds you well. We noticed that you have some items in your cart. We appreciate your interest in our products.</p>
            <table border='0'  cellpadding='10' cellspacing='0' style='width:100%;margin: auto;padding:0 20px;'>
            <tr><a href='{productURL}'>{productName}</a></tr></table></p>
            <p style='line-height:18px;background-color:#ececec;letter-spacing:1px;;
    max-width: 800px; margin: auto; text-align: center;'> Don't miss out on this opportunity to purchase the items you want. </p>
            <p style='line-height:18px;background-color:#ececec;letter-spacing:1px;    max-width: 800px;
    margin: auto;  text-align: center;'> Thank you for choosing our store, and we look forward to serving you. </p>
        </td>
    </tr>
</table>";
    public const CONTAIN_HTML_EN_WISHLIST = "<table style='width:800px;background-color:#ececec; padding-bottom:0px; border-spacing: 0;margin:0 auto;'>
    <tr>
        <td style='text-align: center;'>
            <p style='line-height:18px;background-color:#ececec;letter-spacing:1px'> Dear {firstName} {lastName} , </p>
            <p style='line-height:18px;background-color:#ececec;letter-spacing:1px'> We hope this email finds you well. We noticed that you have some items on your wishlist. We appreciate your interest in our products. </p>
            <p style='line-height:18px;background-color:#ececec;letter-spacing:1px'>
            <table border='0'  cellpadding='10' cellspacing='0' style='width:100%;margin: auto;padding:0 20px;'>
            <tr><a href='{productURL}'>{productName}</a></tr></table></p>
            <p style='line-height:18px;background-color:#ececec;letter-spacing:1px;;
    max-width: 800px; margin: auto; text-align: center;'> Don't miss out on this opportunity to purchase the items you want. </p>
            <p style='line-height:18px;background-color:#ececec;letter-spacing:1px;    max-width: 800px;
    margin: auto;  text-align: center;'> Thank you for choosing our store, and we look forward to serving you. </p>
        </td>
    </tr>
</table>";
    public const CONTAIN_HTML_EN_RESTOCK = "<table style='width:800px;background-color:#ececec; padding-bottom:0px; border-spacing: 0;margin:0 auto;'>
    <tr>
        <td style='text-align: center;'>
            <p style='line-height:18px;background-color:#ececec;letter-spacing:1px'> Dear {firstName} {lastName} , </p>
            <p style='line-height:18px;background-color:#ececec;letter-spacing:1px'> We hope this email finds you well. We noticed that you have some items in your wishlist and cart. We appreciate your interest in our products. </p>
            <p style='line-height:18px;background-color:#ececec;letter-spacing:1px'>We’re glad to inform you that your wishlist and cart items are now back in stock.</p>
            <table border='0'  cellpadding='10' cellspacing='0' style='width:100%;margin: auto;padding:0 20px;'>
            <tr><a href='{productURL}'>{productName}</a></tr></table></p>
            <p style='line-height:18px;background-color:#ececec;letter-spacing:1px;;
    max-width: 800px; margin: auto; text-align: center;'> Don't miss out on this opportunity to purchase the items you want. </p>
            <p style='line-height:18px;background-color:#ececec;letter-spacing:1px;    max-width: 800px;
    margin: auto;  text-align: center;'> Thank you for choosing our store, and we look forward to serving you. </p>
        </td>
    </tr>
</table>";
    public const CONTAIN_HTML_DE = "<table style='width:800px;background-color:#ececec; padding-bottom:0px; border-spacing: 0;margin:0 auto;'>
    <tr>
        <td style='text-align: center;'>
            <p style='line-height:18px;background-color:#ececec;letter-spacing:1px'> Liebe {firstName} {lastName} , </p>
            <p style='line-height:18px;background-color:#ececec;letter-spacing:1px'> Wir hoffen, dass diese E-Mail Sie gut erreicht. Wir haben festgestellt, dass Sie einige Artikel in Ihrem Warenkorb haben. Wir freuen uns über Ihr Interesse an unseren Produkten.</p>
            <table border='0'  cellpadding='10' cellspacing='0' style='width:100%;margin: auto;padding:0 20px;'>
            <tr><a href='{productURL}'>{productName}</a></tr></table></p>
            <p style='line-height:18px;background-color:#ececec;letter-spacing:1px;;
    max-width: 800px; margin: auto; text-align: center;'> Lassen Sie sich diese Gelegenheit nicht entgehen, um die gewünschten Artikel zu kaufen. </p>
            <p style='line-height:18px;background-color:#ececec;letter-spacing:1px;    max-width: 800px;
    margin: auto;  text-align: center;'> Wir danken Ihnen, dass Sie sich für unser Geschäft entschieden haben und freuen uns darauf, Sie zu bedienen. </p>
        </td>
    </tr>
</table>";
    public const CONTAIN_HTML_DE_WISHLIST = "<table style='width:800px;background-color:#ececec; padding-bottom:0px; border-spacing: 0;margin:0 auto;'>
    <tr>
        <td style='text-align: center;'>
            <p style='line-height:18px;background-color:#ececec;letter-spacing:1px'> Liebe {firstName} {lastName} , </p>
            <p style='line-height:18px;background-color:#ececec;letter-spacing:1px'> Wir hoffen, dass diese E-Mail Sie gut erreicht. Wir haben festgestellt, dass Sie einige Artikel auf Ihrer Wunschliste haben. Wir freuen uns über Ihr Interesse an unseren Produkten. </p>
            <p style='line-height:18px;background-color:#ececec;letter-spacing:1px'>
            <table border='0'  cellpadding='10' cellspacing='0' style='width:100%;margin: auto;padding:0 20px;'>
            <tr><a href='{productURL}'>{productName}</a></tr></table></p>
            <p style='line-height:18px;background-color:#ececec;letter-spacing:1px;;
    max-width: 800px; margin: auto; text-align: center;'> Lassen Sie sich diese Gelegenheit nicht entgehen, um die gewünschten Artikel zu kaufen. </p>
            <p style='line-height:18px;background-color:#ececec;letter-spacing:1px;    max-width: 800px;
    margin: auto;  text-align: center;'> Wir danken Ihnen, dass Sie sich für unser Geschäft entschieden haben und freuen uns darauf, Sie zu bedienen. </p>
        </td>
    </tr>
</table>";
    public const CONTAIN_HTML_DE_RESTOCK = "<table style='width:800px;background-color:#ececec; padding-bottom:0px; border-spacing: 0;margin:0 auto;'>
    <tr>
        <td style='text-align: center;'>
            <p style='line-height:18px;background-color:#ececec;letter-spacing:1px'> Liebe {firstName} {lastName} , </p>
            <p style='line-height:18px;background-color:#ececec;letter-spacing:1px'> Wir hoffen, dass diese E-Mail Sie gut erreicht. Wir haben festgestellt, dass Sie einige Artikel in Ihrer Wunschliste und Ihrem Warenkorb haben. Wir freuen uns über Ihr Interesse an unseren Produkten. </p>
            <p style='line-height:18px;background-color:#ececec;letter-spacing:1px'>Wir freuen uns, Ihnen mitzuteilen, dass die Artikel Ihrer Wunschliste und Ihres Warenkorbs jetzt wieder auf Lager sind.</p>
            <table border='0'  cellpadding='10' cellspacing='0' style='width:100%;margin: auto;padding:0 20px;'>
            <tr><a href='{productURL}'>{productName}</a></tr></table></p>
            <p style='line-height:18px;background-color:#ececec;letter-spacing:1px;;
    max-width: 800px; margin: auto; text-align: center;'> Lassen Sie sich diese Gelegenheit nicht entgehen, um die gewünschten Artikel zu kaufen. </p>
            <p style='line-height:18px;background-color:#ececec;letter-spacing:1px;    max-width: 800px;
    margin: auto;  text-align: center;'> Wir danken Ihnen, dass Sie sich für unser Geschäft entschieden haben und freuen uns darauf, Sie zu bedienen. </p>
        </td>
    </tr>
</table>";

    public function install(InstallContext $installContext): void
    {
        parent::install($installContext);
        $this->cartReminderEmailTemplate($installContext);
        $this->wishlistReminderEmailTemplate($installContext);
        $this->restockReminderEmailTemplate($installContext);
    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
        parent::uninstall($uninstallContext);

        if ($uninstallContext->keepUserData()) {
            return;
        }

        $connection = $this->container->get(Connection::class);
        $connection->executeStatement('DROP TABLE IF EXISTS `wishlist_email_reminder`');
        $connection->executeStatement('DROP TABLE IF EXISTS `restock_email_reminder`');
        $connection->executeStatement('DROP TABLE IF EXISTS `cart_email_reminder`');
        $connection->executeStatement("DELETE FROM `system_config` WHERE `configuration_key` LIKE '%ICTECH_ReminderEmailWishlist%'");

        $this->uninstallMailTemplates($uninstallContext);
    }

    //install email template
    public function cartReminderEmailTemplate(InstallContext $installContext): void
    {
        /** @var EntityRepository $mailTemplateTypeRepository */
        $mailTemplateTypeRepository = $this->container->get('mail_template_type.repository');
        /** @var EntityRepository $mailTemplateRepository */
        $mailTemplateRepository = $this->container->get('mail_template.repository');

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('technicalName', self::TEMPLATE_TYPE_TECHNICAL_NAME));
        $existingType = $mailTemplateTypeRepository->search($criteria, $installContext->getContext())->first();

        $mailTemplateTypeId = $existingType ? $existingType->getId() : Uuid::randomHex();

        $mailTemplateType = [[
            'id' => $mailTemplateTypeId,
            'name' => self::TEMPLATE_TYPE_NAME,
            'technicalName' => self::TEMPLATE_TYPE_TECHNICAL_NAME,
            'availableEntities' => [
                'product' => 'product',
                'salesChannel' => 'sales_channel'
            ]
        ]];

        $mailTemplateTypeRepository->upsert($mailTemplateType, $installContext->getContext());

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('mailTemplateTypeId', $mailTemplateTypeId));
        $existingTemplate = $mailTemplateRepository->search($criteria, $installContext->getContext())->first();

        $mailTemplateId = $existingTemplate ? $existingTemplate->getId() : Uuid::randomHex();

        $mailTemplate = [[
            'id' => $mailTemplateId,
            'mailTemplateTypeId' => $mailTemplateTypeId,
            'senderName' => [
                'en-GB' => 'Admin',
                'de-DE' => 'Administratorin',
            ],
            'subject' => [
                'en-GB' => self::SUBJECT_ENG,
                'de-DE' => self::SUBJECT_DE,
            ],
            'contentPlain' => [
                'en-GB' => self::CONTAIN_PLAIN_EN,
                'de-DE' => self::CONTAIN_PLAIN_DE,
            ],
            'contentHtml' => [
                'en-GB' => self::CONTAIN_HTML_EN,
                'de-DE' => self::CONTAIN_HTML_DE,
            ],
        ]];

        $mailTemplateRepository->upsert($mailTemplate, $installContext->getContext());
    }

    public function wishlistReminderEmailTemplate(InstallContext $installContext): void
    {
        /** @var EntityRepository $mailTemplateTypeRepository */
        $mailTemplateTypeRepository = $this->container->get('mail_template_type.repository');
        /** @var EntityRepository $mailTemplateRepository */
        $mailTemplateRepository = $this->container->get('mail_template.repository');

        $context = $installContext->getContext();

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('technicalName', self::TEMPLATE_TYPE_TECHNICAL_NAME_WISHLIST));
        $existingType = $mailTemplateTypeRepository->search($criteria, $context)->first();

        $mailTemplateTypeId = $existingType ? $existingType->getId() : Uuid::randomHex();

        $mailTemplateType = [[
            'id' => $mailTemplateTypeId,
            'name' => self::TEMPLATE_TYPE_NAME_WISHLIST,
            'technicalName' => self::TEMPLATE_TYPE_TECHNICAL_NAME_WISHLIST,
            'availableEntities' => [
                'product' => 'product',
                'salesChannel' => 'sales_channel'
            ]
        ]];

        $mailTemplateTypeRepository->upsert($mailTemplateType, $context);

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('mailTemplateTypeId', $mailTemplateTypeId));
        $existingTemplate = $mailTemplateRepository->search($criteria, $context)->first();

        $mailTemplateId = $existingTemplate ? $existingTemplate->getId() : Uuid::randomHex();

        $mailTemplate = [[
            'id' => $mailTemplateId,
            'mailTemplateTypeId' => $mailTemplateTypeId,
            'senderName' => [
                'en-GB' => 'Admin',
                'de-DE' => 'Administratorin',
            ],
            'subject' => [
                'en-GB' => self::SUBJECT_ENG_WISHLIST,
                'de-DE' => self::SUBJECT_DE_WISHLIST,
            ],
            'contentPlain' => [
                'en-GB' => self::CONTAIN_PLAIN_EN_WISHLIST,
                'de-DE' => self::CONTAIN_PLAIN_DE_WISHLIST,
            ],
            'contentHtml' => [
                'en-GB' => self::CONTAIN_HTML_EN_WISHLIST,
                'de-DE' => self::CONTAIN_HTML_DE_WISHLIST,
            ],
        ]];

        $mailTemplateRepository->upsert($mailTemplate, $context);
    }

    public function restockReminderEmailTemplate(InstallContext $installContext): void
    {
        /** @var EntityRepository $mailTemplateTypeRepository */
        $mailTemplateTypeRepository = $this->container->get('mail_template_type.repository');
        /** @var EntityRepository $mailTemplateRepository */
        $mailTemplateRepository = $this->container->get('mail_template.repository');

        $context = $installContext->getContext();

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('technicalName', self::TEMPLATE_TYPE_TECHNICAL_NAME_RESTOCK));
        $existingType = $mailTemplateTypeRepository->search($criteria, $context)->first();

        $mailTemplateTypeId = $existingType ? $existingType->getId() : Uuid::randomHex();

        $mailTemplateType = [[
            'id' => $mailTemplateTypeId,
            'name' => self::TEMPLATE_TYPE_NAME_RESTOCK,
            'technicalName' => self::TEMPLATE_TYPE_TECHNICAL_NAME_RESTOCK,
            'availableEntities' => [
                'product' => 'product',
                'salesChannel' => 'sales_channel'
            ]
        ]];

        $mailTemplateTypeRepository->upsert($mailTemplateType, $context);

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('mailTemplateTypeId', $mailTemplateTypeId));
        $existingTemplate = $mailTemplateRepository->search($criteria, $context)->first();

        $mailTemplateId = $existingTemplate ? $existingTemplate->getId() : Uuid::randomHex();

        $mailTemplate = [[
            'id' => $mailTemplateId,
            'mailTemplateTypeId' => $mailTemplateTypeId,
            'senderName' => [
                'en-GB' => 'Admin',
                'de-DE' => 'Administratorin',
            ],
            'subject' => [
                'en-GB' => self::SUBJECT_ENG_RESTOCK,
                'de-DE' => self::SUBJECT_DE_RESTOCK,
            ],
            'contentPlain' => [
                'en-GB' => self::CONTAIN_PLAIN_EN_RESTOCK,
                'de-DE' => self::CONTAIN_PLAIN_DE_RESTOCK,
            ],
            'contentHtml' => [
                'en-GB' => self::CONTAIN_HTML_EN_RESTOCK,
                'de-DE' => self::CONTAIN_HTML_DE_RESTOCK,
            ],
        ]];

        $mailTemplateRepository->upsert($mailTemplate, $context);
    }

    public function uninstallMailTemplates(UninstallContext $uninstallContext): void
    {
        /** @var EntityRepository $mailTemplateTypeRepository */
        $mailTemplateTypeRepository = $this->container->get('mail_template_type.repository');
        /** @var EntityRepository $mailTemplateRepository */
        $mailTemplateRepository = $this->container->get('mail_template.repository');

        $context = $uninstallContext->getContext();

        $templateTechnicalNames = [
            self::TEMPLATE_TYPE_TECHNICAL_NAME,
            self::TEMPLATE_TYPE_TECHNICAL_NAME_WISHLIST,
            self::TEMPLATE_TYPE_TECHNICAL_NAME_RESTOCK
        ];

        foreach ($templateTechnicalNames as $technicalName) {
            $typeCriteria = new Criteria();
            $typeCriteria->addFilter(new EqualsFilter('technicalName', $technicalName));

            $templateType = $mailTemplateTypeRepository->search($typeCriteria, $context)->first();

            if (!$templateType) {
                continue;
            }

            $templateTypeId = $templateType->getId();

            $emailCriteria = new Criteria();
            $emailCriteria->addFilter(new EqualsFilter('mailTemplateTypeId', $templateTypeId));
            $emailTemplates = $mailTemplateRepository->search($emailCriteria, $context)->getEntities();

            foreach ($emailTemplates as $template) {
                $mailTemplateRepository->delete([
                    ['id' => $template->getId()]
                ], $context);
            }

            $mailTemplateTypeRepository->delete([
                ['id' => $templateTypeId]
            ], $context);
        }
    }
}
