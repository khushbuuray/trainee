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
                { property: 'name', label: 'Name', primary: true, routerLink: 'sw.blog.detail'},
                { property: 'description', label: 'description', primary: true},
                { property: 'release_date', label: 'release Date', primary: true},
                { property: 'active', label: 'Active', primary: true},
                // { property: 'blogcategories', label: 'Categories', primary: true},
                { property: 'author', label: 'Author', primary: true},
                // { property: 'products', label: 'Product', primary: true},
                
            ]
        }
    },
      created() {
        this.repository = this.repositoryFactory.create('blog');
        this.loadBlogs();
    },

    methods: {
        loadBlogs() {
            this.isLoading = true;

            const criteria = new Criteria();
            this.repository.search(criteria, Shopware.Context.api).then((result) => {
                this.blogs = result;
                this.isLoading = false;
            });
        },
        onChangeLanguage(languageId) {
            Shopware.State.commit('context/setApiLanguageId', languageId);
            this.loadBlogs();
        }
    }
});



