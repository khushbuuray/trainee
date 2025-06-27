<?php declare(strict_types=1);

namespace IctDataCleanerPro;

use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\Framework\Plugin\Context\UpdateContext;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\DeactivateContext;
use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\DataAbstractionLayer\Indexing\EntityIndexerRegistry;

class IctDataCleanerPro extends Plugin
{
    public function install(InstallContext $installContext): void
    {
        parent::install($installContext);
        $logDir = $this->container->getParameter('kernel.project_dir') . '/var/log/ict-data-cleaner';

        if (!is_dir($logDir)) {
            mkdir($logDir, 0775, true);
        }
            echo "Created log folder at: $logDir\n";


    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
        parent::uninstall($uninstallContext);

        if ($uninstallContext->keepUserData()) {
            return;
        }

        // Remove custom tables
        $connection = $this->container->get(Connection::class);
        $connection->executeStatement('DROP TABLE IF EXISTS `ict_data_cleanup_log`');
    }

    public function activate(ActivateContext $activateContext): void
    {
        parent::activate($activateContext);
        
        // Register scheduled task
        $this->registerScheduledTask($activateContext->getContext());
    }

    public function deactivate(DeactivateContext $deactivateContext): void
    {
        parent::deactivate($deactivateContext);
    }

    private function registerScheduledTask($context): void
    {
        // Scheduled task registration will be handled by the container
    }
}
