
import './page/blog-bundle-list';

Shopware.Module.register('blog-bundle', {
  type: 'plugin',
  name: 'BlogBundle',
  title: 'Blog Bundle',
  description: 'Blog bundle management',
  color: '#ff3d58',
  icon: 'default-text-editor-document',

  routes: {
    index: {
      component: 'blog-bundle-list',
      path: 'index'
    },
    detail:{
      component: 'blog-bundle-detail',
      path: 'detail/:id',
      props: true
    },
    create :{
      path: 'create',
      component: 'blog-bundle-create'
    },

  },

  navigation: [{
    label: 'Blog',
    color: '#ff3d58',
    path: 'blog.bundle.index',
    icon: 'default-text-editor-document',
    parent: 'sw-catalogue',
    position: 100
  }]
});


