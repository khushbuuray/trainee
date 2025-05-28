import template from './sw-cms-preview-custom-image-text.html.twig';
import './sw-cms-preview-custom-image-text.scss';

Shopware.Component.register("sw-cms-preview-custom-image-text", {
    template,
    compatConfig: Shopware.compatConfig,
    computed: {
        assetFilter() {
            return Shopware.Filter.getByName('asset');
        },
    },
});