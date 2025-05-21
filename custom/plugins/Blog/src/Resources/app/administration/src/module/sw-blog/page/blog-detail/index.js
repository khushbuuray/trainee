import template from './blog-detail.html.twig';


const { Component,Criteria, EntityCollection,Mixin } = Shopware.Data;
const { mapPropertyErrors } = Shopware.Component.getComponentHelper();

Shopware.Component.register('blog-detail', {
    template,

    inject: ['repositoryFactory'],

    mixins: [
        Shopware.Mixin.getByName('notification'),
        Shopware.Mixin.getByName('placeholder'),

    ],
    
    data() {
        return {
           isLoading: false,
           categoryCriteria: new Criteria(1, 25),
            blog: {
            name: '',
            description: '',
            release_date: null,
            active: false,         
            blogCategories: [],
            author: '',
            products: new EntityCollection('product', 'id', Shopware.Context.api, new Criteria()),
         }
        }   
    },


    created() {
    const id = this.$route.params.id;
    const repository = this.repositoryFactory.create('blog');

    if (id) {
        const criteria = new Criteria();
        criteria.addAssociation('products');
        criteria.addAssociation('blogCategories');

        repository.get(id, Shopware.Context.api, criteria).then((blog) => {
            this.blog = blog;
        }).catch(() => {
            console.log('error loading blog');
        });
    } else {
        this.blog = repository.create(Shopware.Context.api);
        this.blog.categories = [];
        this.blog.products = [];
    }
},


    computed: {
    ...mapPropertyErrors('blog', ['name', 'author', 'release_date', 'description']),

    categoryCriteria() {
    const criteria = new Criteria(1, 25);
    criteria.addFilter(Criteria.equals('active', true));
    criteria.addSorting(Criteria.sort('name', 'ASC'));
    return criteria;
    },
    productCriteria() {
    const criteria = new Criteria(1, 25);
    criteria.addFilter(Criteria.equals('active', true));
    criteria.addSorting(Criteria.sort('name', 'ASC'));
    return criteria;
     },
    },

    methods: {
    onSave() {
    this.isLoading = true;
     console.log('Saving blog with data:', this.blog);

        const blog = this.blog;
        if (!blog?.name || !blog.description || !blog.release_date || !blog.author || !blog.blogCategories) {
         if (!this.blog.blogCategories || this.blog.blogCategories.getIds().length === 0) {    
         console.log('in');
           this.createNotificationError({
                    title: this.$tc('sw-blog.general.ErrorTitle'),
                    message: this.$tc('sw-blog.general.ErrorMessage'),
                });
         this.isLoading = false;
         return;
        }
    }  

    const repository = this.repositoryFactory.create('blog');
    this.blog.release_date = new Date().toISOString();
    
    repository.save(this.blog, Shopware.Context.api).then(() => {
        this.isLoading = false;
        this.createNotificationSuccess({
                    title: this.$tc('sw-blog.general.successTitle'),
                    message: this.$tc('sw-blog.general.successMessage'),
                });
        this.$router.push({ name: 'sw.blog.index' });
    }).catch((e) => {
        this.isLoading = false;
        if (e.response?.data?.errors?.length) {
        console.error('Validation errors:', e.response.data.errors);
    }
    });
},
     onChangeLanguage(languageId) {
     Shopware.State.commit('context/setApiLanguageId', languageId);
     this.blog;
    },
  
    onCancel() {
        this.$router.back();
    },

    onCategoryChange(blogCategories) {        
    // this.blog.categories = blogCategories;
    this.blog.blogCategories = blogCategories;

    },
    onProductChange(products) {
    this.blog.products = products;      
    },
}

  });
