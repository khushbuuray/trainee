
import './page/blog-bundle-list';
import './page/blog-category-create';
import enGB from './snippet/en-GB.json';
import deDE from './snippet/de-DE.json';


Shopware.Module.register('blog-bundle', {
  type: 'plugin',
  name: 'BlogBundle',
  title: 'blog-bundle.general.mainMenuTitle',
  description: 'blog-bundle.general.descriptionTextModule',
  color: '#ff3d58',
  icon: 'default-text-editor-document',

  snippets: {
    'en-GB': enGB,
    'de-DE': deDE
  },

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


