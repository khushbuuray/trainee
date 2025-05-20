import template from './blog-detail.html.twig';


const { Component,Criteria, EntityCollection } = Shopware.Data;

Shopware.Component.register('blog-detail', {
    template,

    inject: ['repositoryFactory'],
    

    

    data() {
        return {
           isLoading: false,
           categoryCriteria: new Criteria(1, 25),
            blog: {
            name: '',
            description: '',
            releaseDate: null,
            active: false,
            //    categories: new EntityCollection(
            //    'blogCategories', 
            //    'id',            
            //     Shopware.Context.api,
            //     new Criteria()   
            // ),
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
        console.log(this.blog);
    }

},


    computed: {
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
}
    },

    methods: {
    onSave() {
    this.isLoading = true;
    console.log('Saving blog with data:', this.blog);


    const repository = this.repositoryFactory.create('blog');
    this.blog.releaseDate = new Date().toISOString();
    console.log(this.blog);
    
    repository.save(this.blog, Shopware.Context.api).then(() => {
        this.isLoading = false;
        
        this.$router.push({ name: 'sw.blog.index' });
    }).catch((e) => {
        this.isLoading = false;
        console.error('Save failed:', e);
    });
}
,
  
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
