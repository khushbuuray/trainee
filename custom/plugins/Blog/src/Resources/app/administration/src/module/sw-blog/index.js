import './page/blog-list';
import './page/blog-detail';

Shopware.Module.register('sw-blog', {
    type: 'plugin',
    name: 'Blog',
    title: 'Blog',
    description: 'Blog management',
    color: '#ff3d58',
    icon: 'default-text-editor-document',

    routes: {
        index: {
            path: 'index',
            component: 'blog-list',
        },
        create :{
            path: 'create',
            component: 'blog-detail',
        },
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