import './component';
import './preview';

// Shopware.service('cmsService').registerCmsBlock({
//     name: 'my-image-text-reversed',
//     label: 'sw-cms.blocks.my-image-text-reversed.label',
//     category: 'text-image',
//     component: 'sw-cms-block-my-image-text-reversed',
  
// });

Shopware.Service('cmsService').registerCmsBlock({
    name: 'my-image-text-reversed',
    label: 'My Custom Block',
    category: 'text-image',
    component: 'sw-cms-block-my-image-text-reversed',
    previewComponent: 'sw-cms-preview-my-image-text-reversed',
    defaultConfig: {
        marginBottom: '20px',
        marginTop: '20px',
        marginLeft: '20px',
        marginRight: '20px',
        sizingMode: 'boxed'
    },
    slots: {
        left: 'Text',
        right: 'Image'
    }
});