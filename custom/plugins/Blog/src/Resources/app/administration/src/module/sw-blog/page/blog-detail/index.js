import template from './blog-detail.html.twig';


const { Criteria, EntityCollection } = Shopware.Data;

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

    computed: {
        categoryCriteria() {
            const criteria = new Criteria(1, 25);
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
    // onSave() {
    //     this.isLoading = true;

    //     const repository = this.repositoryFactory.create('blog');
    //     repository.save(this.blog, Shopware.Context.api).then(() => {
    //         this.isLoading = false;
    //             this.$router.push({ name: 'sw.blog.index' });
    //             // this.$router.back();

    //         // this.createNotificationSuccess({ title: 'Saved', message: 'Blog category saved.' });
    //     }).catch(() => {
    //         this.isLoading = false;
    //         // this.createNotificationError({ title: 'Error', message: 'Save failed.' });
    //     });
    // },
    onSave() {
    this.isLoading = true;

    const blogRepository = this.repositoryFactory.create('blog');

    blogRepository.save(this.blog, Shopware.Context.api).then(() => {
        this.isLoading = false;

        // Navigate to blog listing or another page
        this.$router.push({ name: 'sw.blog.index' });

        // Optional: show success notification
        // this.createNotificationSuccess({ title: 'Success', message: 'Blog saved successfully.' });
    }).catch((error) => {
        this.isLoading = false;

        // Optional: show error notification
        // this.createNotificationError({ title: 'Error', message: 'Saving blog failed.' });

        console.error('Save failed:', error);
    });
}
,

    onCancel() {
        this.$router.back();
    },
    onCategoryChange(categories) {
    this.blog.categories = categories;
    },
    onProductChange(products) {
    this.blog.products = products;
    },

    }

});
