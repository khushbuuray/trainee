import './page/blog-list';
import './page/blog-detail';
import enGB from './snippet/en-GB.json';
import deDE from './snippet/de-DE.json';

Shopware.Module.register('sw-blog', {
    type: 'plugin',
    name: 'Blog',
    title: 'Blog',
    description: 'Blog management',
    color: '#ff3d58',
    icon: 'default-text-editor-document',

      snippets: {
    'en-GB': enGB,
    'de-DE': deDE
    },

    routes: {
        index: {
            path: 'index',
            component: 'blog-list',
        },
        create :{
            path: 'create',
            component: 'blog-detail',
        },
        detail: {
            path: 'detail/:id',
            component: 'blog-detail',
            props: true
        }
    },

    navigation: [{
    label: 'Blog',
    color: '#ff3d58',
    path: 'sw.blog.index',
    icon: 'default-text-editor-document',
    parent: 'sw-catalogue',
    position: 90
    }],

   
})