import template from './blog-bundle-list.html.twig';
const { Criteria } = Shopware.Data;


Shopware.Component.register('blog-bundle-list', {
    template,

    inject: ['repositoryFactory'],

    data() {
        return {
            blogCategories: null,
            repository: null,
            isLoading: false,
            total: 0,
            columns: [
                { property: 'name', label: 'Name', primary: true, routerLink: 'blog.bundle.detail'}
            ]
        };
    },

    computed: {
        criteria() {
            const criteria = new Criteria();
            criteria.setPage(1);
            criteria.setLimit(25);
            return criteria;
        }
    },

    created() {
        this.repository = this.repositoryFactory.create('blog_category');
        this.loadCategories();
    },

    methods: {
        loadCategories() {
            this.isLoading = true;

            this.repository.search(this.criteria, Shopware.Context.api).then((result) => {
                this.blogCategories = result;
                this.total = result.total;
            }).finally(() => {
                this.isLoading = false;
            });
        },        
    }
});
