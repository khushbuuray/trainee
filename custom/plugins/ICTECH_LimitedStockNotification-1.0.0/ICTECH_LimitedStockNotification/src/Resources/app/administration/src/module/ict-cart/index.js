import './page/ict-cart-list';
import deDE from './snippet/de-DE.json';
import enGB from './snippet/en-GB.json';

const { Module} = Shopware;

Module.register('ict-cart', {
    name: 'bundle',
    title: 'ict-cart.general.mainMenuItemGeneral',
    description: 'ict-cart.general.descriptionTextModule',
    color: '#ff3d58',

    snippets: {
        'de-DE': deDE,
        'en-GB': enGB
    },
    routes: {
        index: {
            component: 'ict-cart-list',
            path: 'index'
        },
        detail: {
            component: 'ict-cart-detail',
            path: 'detail/:id',
            meta: {
                parentPath: 'ict-cart.index'
            }
        },
    },
    navigation: [{
        label: 'ict-cart.general.mainMenuItemGeneral',
        color: '#ff3d58',
        path: 'ict.cart.index',
        parent: 'sw-catalogue',
        icon: 'regular-cog',
        position: 100
    }],
});
