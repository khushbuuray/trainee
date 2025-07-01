import './component';
import './preview';
import './config';


Shopware.Service('cmsService').registerCmsElement({
    name: 'my-image-text-reversed',
    label: 'Text & Image Block (Reversed)',
    component: 'sw-cms-el-my-image-text-reversed',
    configComponent: 'sw-cms-el-config-my-image-text-reversed',
    previewComponent: 'sw-cms-el-preview-my-image-text-reversed',
    defaultConfig: {
        dailyUrl: {
            source: 'static',
            value: 'hiiiiii'
        }
    }
});

