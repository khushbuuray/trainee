
import './page/blog-bundle-list';
import './page/blog-category-create';
// import './page/blog-bundle-detail';

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
      component: 'blog-category-create', // for edit
      path: 'detail/:id',
      props: true
    },
    create :{
      component: 'blog-category-create',
      path: 'create',
    },
  },

  navigation: [{
    label: 'Blog Category',
    color: '#ff3d58',
    path: 'blog.bundle.index',
    icon: 'default-text-editor-document',
    parent: 'sw-catalogue',
    position: 100
  }]
});


