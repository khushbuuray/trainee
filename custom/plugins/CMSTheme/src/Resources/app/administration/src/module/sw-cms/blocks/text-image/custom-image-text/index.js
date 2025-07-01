import './component';
import './preview';

Shopware.Service('cmsService').registerCmsBlock({
    name: 'custom-image-text',
    label: 'Custom Text & Image Block',
    category: 'text-image',
    component: 'sw-cms-block-custom-image-text',
    previewComponent: 'sw-cms-preview-custom-image-text',
    defaultConfig: {
        marginBottom: '20px',
        marginTop: '20px',
        marginLeft: '20px',
        marginRight: '20px',
        sizingMode: 'boxed'
    },
slots: {
        'left-image': {
            type: 'image',
            default: {
                config: {
                    displayMode: { source: 'static', value: 'cover' },
                },
                data: {
                    media: {
                        value: 'bundles/administration/static/img/cms/preview_camera_small.jpg',
                        source: 'default',
                    },
                },
            },
        },
        'left-text': {
            type: 'text',
            default: {
                config: {
                    content: {
                        source: 'static',
                        value: `
                        <h2 style="text-align: center;">Lorem Ipsum dolor</h2>
                        <p style="text-align: center;">Lorem ipsum dolor sit amet, consetetur sadipscing elitr,
                        sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat,
                        sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum.</p>
                        `.trim(),
                    },
                },
            },
        },
      'left-button': {
       type: "button",
        default: {
                config: {
                    name: {
                        source: "static",
                        value: "Shop",
                        required: true,
                    }
                },
            },
        
    },

        "center-left-image": {
            type: "image",
            default: {
                config: {
                    displayMode: { source: 'static', value: 'cover' },
                },
                data: {
                    media: {
                                value: 'bundles/administration/static/img/cms/preview_camera_small.jpg',
                        source: 'default',
                    },
                }
                }
        },
        "center-left-text": {
            type: "text",
            default: {
                config: {
                    content: {
                        source: 'static',
                        value: `
                        <h2 style="text-align: center;">Lorem Ipsum dolor</h2>
                        <p style="text-align: center;">Lorem ipsum dolor sit amet, consetetur sadipscing elitr,
                        sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat,
                        sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum.</p>
                        `.trim(),
                    },
                },
            },
        },
        "center-left-button": {
            type: "button",
            default: {
                config: {
                    name: {
                        source: "static",
                        value: "Shop",
                        required: true,
                    },
                    link: {
                        source: "static",
                        value: null,
                    },
                },
            },
        },
        'center-right-image': {
            type: 'image',
            default: {
                config: {
                    displayMode: { source: 'static', value: 'cover' },
                },
                data: {
                    media: {
                                                value: 'bundles/administration/static/img/cms/preview_plant_small.jpg',
                        source: 'default',
                    },
                },
            },
        },
        'center-right-text': {
            type: 'text',
            default: {
                config: {
                    content: {
                        source: 'static',
                        value: `
                        <h2 style="text-align: center;">Lorem Ipsum dolor</h2>
                        <p style="text-align: center;">Lorem ipsum dolor sit amet, consetetur sadipscing elitr,
                        sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat,
                        sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum.</p>
                        `.trim(),
                    },
                },
            },
        },
        "center-right-button": {
            type: "button",
            default: {
                config: {
                    name: {
                        source: "static",
                        value: "Shop",
                        required: true,
                    },
                    link: {
                        source: "static",
                        value: null,
                    },
                },
            },
        },
        
        'right-image': {
            type: 'image',
            default: {
                config: {
                    displayMode: { source: 'static', value: 'cover' },
                },
                data: {
                    media: {
                                                value: 'bundles/administration/static/img/cms/preview_glasses_small.jpg',
                        source: 'default',
                    },
                },
            },
        },
        'right-text': {
            type: 'text',
            default: {
                config: {
                    content: {
                        source: 'static',
                        value: `
                        <h2 style="text-align: center;">Lorem Ipsum dolor</h2>
                        <p style="text-align: center;">Lorem ipsum dolor sit amet, consetetur sadipscing elitr,
                        sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat,
                        sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum.</p>
                        `.trim(),
                    },
                },
            },
        },
        "right-button": {
            type: "button",
            default: {
                config: {
                    name: {
                        source: "static",
                        value: "Shop",
                        required: true,
                    },
                    link: {
                        source: "static",
                        value: null,
                    },
                },
            },
        },
    },
    
});
