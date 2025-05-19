import template from './blog-list.html.twig';

const { Criteria } = Shopware.Data;

Shopware.Component.register('blog-list', {
    template,

    inject : ['repositoryFactory'],

    data() {
        return {
            blogs: null,
            repository: null,
            isLoading: false,
            total: 0,
            columns: [
                { property: 'name', label: 'Name', primary: true, routerLink: 'blog.detail'},
                { property: 'description', label: 'description', primary: true},
                { property: 'release_date', label: 'release Date', primary: true},
                { property: 'active', label: 'Active', primary: true},
                { property: 'Categories', label: 'Categories', primary: true},
                { property: 'author', label: 'Author', primary: true},
                { property: 'product', label: 'Product', primary: true},
                
            ]
        }
    },
});



