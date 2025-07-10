import DiztechBundleConfiguratorPlugin from './plugin/diztech-bundle-configurator.plugin';

if (window.PluginManager) {
    window.PluginManager.register(
        'DiztechBundleConfigurator',
        DiztechBundleConfiguratorPlugin,
        '[data-diztech-bundle-configurator]'
    );
}
