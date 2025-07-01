<?php declare(strict_types=1);

namespace ICTECH_LimitedStockNotification;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Shopware\Core\Content\MailTemplate\Aggregate\MailTemplateType\MailTemplateTypeEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\Framework\Uuid\Uuid;

class ICTECH_LimitedStockNotification extends Plugin
{
    public const TEMPLATE_TYPE_NAME = 'Cart Wishlist Reminded';
    public const TEMPLATE_TYPE_NAME_DE = 'Warenkorb-Wunschliste Erinnert';
    public const TEMPLATE_TYPE_TECHNICAL_NAME = 'cart_wishlist_reminded';
    public const SUBJECT_ENG = 'Hurry! Limited Stock Alert for Your Favorite {productName}';
    public const SUBJECT_DE = 'Beeil dich! Begrenzter Lagerbestandsalarm für Ihr Lieblingsprodukt {productName}';
    public const CONTAIN_PLAIN_EN = "Dear {customer},\n\n
We hope this message finds you well.\n\n
We couldn't help but notice that you've added the stunning {productName} to your {cart/wishlist} on our website. Great choice!
We wanted to give you a heads-up that the {productName} you've had your eye on is selling like hotcakes, and we currently have just {availableStock} pieces left in stock. With its sleek design, durability, and unmatched style, it's no surprise that it's flying off our shelves.\n\n
\n\n
{productImage}

\n\n
\n\nDon't miss out on this opportunity to own a piece of iconic style. Click the link below to complete your purchase and secure your Adidas bag {productName} before it's too late.\n\n

\n\nIf you have any questions or need assistance with your purchase, please don't hesitate to reach out to our friendly customer support team. We're here to help!

\n\nThank you for considering {productBrand} and choosing us as your trusted shopping destination. We can't wait to see you sporting your new Adidas bag {productName}. Act fast, as these last {availableStock} units won't last long!

\n\nHappy Shopping!

\n\nWarm regards,";

    public const CONTAIN_PLAIN_DE = "Liebling {customer},\n\n
Wir hoffen, dass diese Nachricht Sie gut findet.\n\n
Wir konnten nicht anders, als zu bemerken, dass Sie den atemberaubenden {productName} zu Ihrem {cart/wishlist} auf unserer Website hinzugefügt haben. Schöne Wahl!\n\n
Wir wollten Sie darüber informieren, dass sich die Tasche, die Sie im Auge behalten haben, rasant verkauft und wir derzeit nur noch {availableStock} Stück auf Lager haben. Mit seinem schlanken Design, seiner Langlebigkeit und seinem unübertroffenen Stil ist es keine Überraschung, dass es aus unseren Regalen fliegt.\n\n

{productImage}


\n\nLassen Sie sich diese Gelegenheit nicht entgehen, ein Stück ikonischen Stils zu besitzen. Klicken Sie auf den Link unten, um Ihren Kauf abzuschließen und Ihren {productName} zu sichern, bevor es zu spät ist.

\n\nWenn Sie Fragen haben oder Hilfe bei Ihrem Kauf benötigen, wenden Sie sich bitte an unser freundliches Kundensupport-Team. Wir sind hier um zu helfen!

\n\nVielen Dank, dass Sie {productBrand} in Betracht gezogen und uns als Ihr vertrauenswürdiges Einkaufsziel ausgewählt haben. Wir können es kaum erwarten, Sie mit Ihrem neuen {productName} zu sehen. Handeln Sie schnell, denn diese letzten {availableStock}-Einheiten werden nicht lange halten!

\n\nViel Spaß beim Einkaufen!

\n\nHerzliche Grüße,";
    public const CONTAIN_HTML_EN = "Dear {customer},<br><br>We hope this message finds you well.<br><br>
    We couldn't help but notice that you've added the stunning {productName} to your {cart/wishlist} on our website. Great choice!<br><br>
    We wanted to give you a heads-up that {productName} you've had your eye on is selling like hotcakes, and we currently have just {availableStock} pieces left in stock. With its sleek design, durability, and unmatched style, it's no surprise that it's flying off our shelves.<br>
<br>
    {productImage}

<br>
<div style='display: block; width: 100%'>
<br>
Don't miss out on this opportunity to own a piece of iconic style. Click the link below to complete your purchase and secure your {productName} before it's too late.<br>
<br>If you have any questions or need assistance with your purchase, please don't hesitate to reach out to our friendly customer support team. We're here to help!
<br>
<br>Thank you for considering {productBrand} and choosing us as your trusted shopping destination. We can't wait to see you sporting your {productName}. Act fast, as these last {availableStock} units won't last long!
<br>
<br>Happy Shopping!
<br>
<br>Warm regards,
</div>";

    public const CONTAIN_HTML_DE = "Liebling {customer},<br><br>Wir hoffen, dass diese Nachricht Sie gut findet.<br><br>
    Wir konnten nicht anders, als zu bemerken, dass Sie das atemberaubende {productName} zu Ihrem {cart/wishlist} auf unserer Website hinzugefügt haben. Schöne Wahl!<br><br>
    Wir wollten Sie darüber informieren, dass sich die Tasche, die Sie im Auge behalten haben, rasant verkauft und wir derzeit nur noch {availableStock} Stück auf Lager haben. Mit seinem schlanken Design, seiner Langlebigkeit und seinem unübertroffenen Stil ist es keine Überraschung, dass es aus unseren Regalen fliegt.<br>
<br>
    {productImage}


<br>
<div style='display: block; width: 100%'>
<br>
Lassen Sie sich diese Gelegenheit nicht entgehen, ein Stück ikonischen Stils zu besitzen. Klicken Sie auf den Link unten, um Ihren Kauf abzuschließen und Ihr {productName} zu sichern, bevor es zu spät ist.<br>
<br>
<br>Wenn Sie Fragen haben oder Hilfe bei Ihrem Kauf benötigen, wenden Sie sich bitte an unser freundliches Kundensupport-Team. Wir sind hier um zu helfen!
<br>
<br>Vielen Dank, dass Sie {productBrand} in Betracht gezogen und uns als Ihr vertrauenswürdiges Einkaufsziel ausgewählt haben. Wir können es kaum erwarten, Sie mit Ihrem {productName} zu sehen. Handeln Sie schnell, denn diese letzten {availableStock}-Einheiten werden nicht lange halten!
<br>
<br>Viel Spaß beim Einkaufen!
<br>
<br>Herzliche Grüße,
</div>";

    public function install(InstallContext $installContext): void
    {
        parent::install($installContext);
        $this->productEmailTemplate($installContext);
    }

    //install email template
    public function productEmailTemplate(InstallContext $installContext): void
    {
        /**
         * @var EntityRepository $mailTemplateTypeRepository
         */
        $mailTemplateTypeRepository = $this->container->get('mail_template_type.repository');

        /**
         * @var EntityRepository $mailTemplateRepository
         */
        $mailTemplateRepository = $this->container->get('mail_template.repository');

        $mailTemplateTypeId = Uuid::randomHex();
        $mailTemplateType = [
            [
                'id' => $mailTemplateTypeId,
                'name' => [
                    'en-GB' => self::TEMPLATE_TYPE_NAME,
                    'de-DE' => self::TEMPLATE_TYPE_NAME_DE,
                ],
                'technicalName' => self::TEMPLATE_TYPE_TECHNICAL_NAME,
                'availableProduct' => [
                    'product' => 'product',
                    'salesChannel' => 'sales_channel'
                ]
            ]
        ];

        $mailTemplate = [
            [
                'id' => Uuid::randomHex(),
                'mailTemplateTypeId' => $mailTemplateTypeId,
                'senderName' => [
                    'en-GB' => 'Admin',
                    'de-DE' => 'Verwaltung'
                ],
                'subject' => [
                    'en-GB' => self::SUBJECT_ENG,
                    'de-DE' => self::SUBJECT_DE
                ],
                'contentPlain' => [
                    'en-GB' => self::CONTAIN_PLAIN_EN,
                    'de-DE' => self::CONTAIN_PLAIN_DE
                ],
                'contentHtml' => [
                    'en-GB' => self::CONTAIN_HTML_EN,
                    'de-DE' => self::CONTAIN_HTML_DE
                ],
            ]
        ];
        try {
            $mailTemplateTypeRepository->create($mailTemplateType, $installContext->getContext());
            $mailTemplateRepository->create($mailTemplate, $installContext->getContext());
        } catch (UniqueConstraintViolationException $exception) {
            throw new \RuntimeException(sprintf(
                'Error: %s',
                $exception->getMessage()
            ));
        }
    }

    //uninstall mail template and table from database
    public function uninstall(UninstallContext $uninstallContext): void
    {
        if ($uninstallContext->keepUserData()) {
            return;
        }

        $connection = $this->container->get(Connection::class);
        $connection->executeStatement('DROP TABLE IF EXISTS `ict_cart_wishlist`');
        $connection->executeStatement('DROP TABLE IF EXISTS `ict_cart_wishlist_product`');
        $connection->executeStatement(
            'DELETE FROM system_config WHERE configuration_key LIKE :domain',
            [
                'domain' => '%ICTECH_LimitedStockNotification.config%',
            ]
        );
        /** @var EntityRepositoryInterface $mailTemplateTypeRepository */
        $mailTemplateTypeRepository = $this->container->get('mail_template_type.repository');
        /** @var EntityRepositoryInterface $mailTemplateRepository */
        $mailTemplateRepository = $this->container->get('mail_template.repository');

        /** @var MailTemplateTypeEntity $myCustomMailTemplateType */
        $myCustomMailTemplateType = $mailTemplateTypeRepository->search((new Criteria())->addFilter(new EqualsFilter('technicalName', self::TEMPLATE_TYPE_TECHNICAL_NAME)), $uninstallContext->getContext())->first();

        $mailTemplateIds = $mailTemplateRepository->searchIds((new Criteria())->addFilter(new EqualsFilter('mailTemplateTypeId', $myCustomMailTemplateType->getId())), $uninstallContext->getContext())->getIds();

        $ids = array_map(static function ($id) {
            return ['id' => $id];
        }, $mailTemplateIds);

        $mailTemplateRepository->delete($ids, $uninstallContext->getContext());
        $mailTemplateTypeRepository->delete([['id' => $myCustomMailTemplateType->getId()]], $uninstallContext->getContext());
    }
}
