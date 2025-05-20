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
               categories: new EntityCollection(
               'blog_category', // entity
               'id',             // entity primary key
                Shopware.Context.api, // context
                new Criteria()    // optional criteria
            ),
            author: '',
            products: new EntityCollection('product', 'id', Shopware.Context.api, new Criteria()),
         }
        }   
    },


    created() {
    const id = this.$route.params.id;
    const repository = this.repositoryFactory.create('blog');

    if (id) {
        repository.get(id, Shopware.Context.api).then((blog) => {
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
            criteria.addAssociation('blogCategories');

            criteria.addFilter(
                Criteria.equals('active', true)
            );
            criteria.addSorting(Criteria.sort('name', 'ASC'));
            return criteria;
        },
          productCriteria() {
        const criteria = new Criteria(1, 25);
        criteria.addFilter(Criteria.equals('active', true)); // optional: only active products
        criteria.addSorting(Criteria.sort('name', 'ASC'));
        return criteria;
    }
    },

    methods: {
    onSave() {
    this.isLoading = true;
    console.log('Saving blog with data:', this.blog);


    const repository = this.repositoryFactory.create('blog');
    this.blog.release_date = new Date().toISOString();
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
    onCategoryChange(categories) {        
    // this.blog.categories = categories;
    this.blog.categories = new EntityCollection(
        'blog_category',
        'id',
        Shopware.Context.api,
        new Criteria(),
        categories
    );
    },
    onProductChange(products) {
    this.blog.products = products;
    // this.blog.products = new EntityCollection(
    //     'product',
    //     'id',
    //     Shopware.Context.api,
    //     new Criteria(),
    //     products
    // )    
},

    }

  });
