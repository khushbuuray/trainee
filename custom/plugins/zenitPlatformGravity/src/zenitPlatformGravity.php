<?php declare(strict_types=1);

namespace zenit\PlatformGravity;

use Shopware\Storefront\Framework\ThemeInterface;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UpdateContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use zenit\PlatformGravity\Core\CustomFieldsHelper;

class zenitPlatformGravity extends Plugin implements ThemeInterface
{
    public function getThemeConfigPath(): string
    {
        return 'theme.json';
    }

    public function install(InstallContext $installContext): void
    {
        parent::install($installContext);

        $customFieldRepository = $this->container->get('custom_field.repository');
        $customFieldSetRepository = $this->container->get('custom_field_set.repository');
        $customFields = new CustomFieldsHelper($customFieldRepository, $customFieldSetRepository);
        $customFields->getCustomFields($this->container, $installContext->getContext());
    }

    public function update(UpdateContext $updateContext): void
    {
        parent::update($updateContext);

        if (version_compare($updateContext->getCurrentPluginVersion(), '4.1.0', '<')) {
            $customFieldRepository = $this->container->get('custom_field.repository');
            $customFieldSetRepository = $this->container->get('custom_field_set.repository');
            $customFields = new CustomFieldsHelper($customFieldRepository, $customFieldSetRepository);
            $customFields->getCustomFields($this->container, $updateContext->getContext());
        }
    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
        parent::uninstall($uninstallContext);

        if ($uninstallContext->keepUserData()) {
            return;
        }

        $customFieldRepository = $this->container->get('custom_field.repository');
        $customFieldSetRepository = $this->container->get('custom_field_set.repository');
        $customFields = new CustomFieldsHelper($customFieldRepository, $customFieldSetRepository);
        $customFields->deleteCustomFields($uninstallContext->getContext());
    }

    /**
     * @param ContainerBuilder $container
     * @throws \Exception
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $loader = new XmlFileLoader($container, new FileLocator($this->getPath() . '/Core/Content/DependencyInjection'));
        $loader->load('services.xml');
    }
}
