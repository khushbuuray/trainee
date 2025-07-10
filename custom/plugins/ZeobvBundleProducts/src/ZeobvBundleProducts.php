<?php

declare(strict_types=1);

namespace Zeobv\BundleProducts;

use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;

class ZeobvBundleProducts extends Plugin
{
    public function uninstall(UninstallContext $uninstallContext): void
    {
        parent::uninstall($uninstallContext);

        if (!$uninstallContext->keepUserData()) {
            $query = 'DROP TABLE IF EXISTS `zeobv_product_bundle_connection`';
            $conn = $this->container->get('Doctrine\DBAL\Connection');
            $conn->executeStatement($query);
        }
    }
}
