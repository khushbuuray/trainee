import template from './sw-cms-el-preview-my-image-text-reversed.html.twig';
import './sw-cms-el-preview-my-image-text-reversed.scss';

Shopware.Component.register('sw-cms-el-preview-my-image-text-reversed', {
    template,

    compatConfig: Shopware.compatConfig,
        computed: {
        assetFilter() {
            console.log('assetFilter');
            
            return Shopware.Filter.getByName('asset');
        },
    },
});
