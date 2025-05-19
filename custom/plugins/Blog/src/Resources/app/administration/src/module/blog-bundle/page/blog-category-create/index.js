import template from './blog-category-create.html.twig';

const { Component, Mixin } = Shopware;

Shopware.Component.register('blog-category-create', {
    template,

    inject: [
        'repositoryFactory'
    ],
     mixins: [
        Mixin.getByName('notification')
    ],

    data() {
        return {
            category: {
                name: ''
            },
            isLoading: false,
            repository: null
        }
    },

    metaInfo() {
        return {
            title: this.$createTitle(this.identifier),
        };
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.repository = this.repositoryFactory.create('blog_category');
             const id = this.$route.params.id;

            if (id) {
                this.repository.get(id, Shopware.Context.api).then((category) => {
                    this.category = category;
                }).catch(() => {
                    this.createNotificationError({
                        title: 'Error',
                        message: 'Could not load blog category.'
                    });
                });
            }else{
            this.category = this.repository.create(Shopware.Context.api);
            }
        },

        onSave() {
            this.isLoading = true;

            this.repository.save(this.category, Shopware.Context.api).then(() => {
                 this.createNotificationSuccess({
                    title: 'Success',
                    message: 'Blog category saved successfully.'
                });
                this.$router.push({ name: 'blog.bundle.index' });
            }).finally(() => {
                this.isLoading = false;
            });
        },

        onCancel() {
            this.$router.back();
        }
    }
});













// /*
//  * @sw-package inventory
//  */

// import template from './blog-category-create.html.twig';


// const { mapPropertyErrors } = Shopware.Component.getComponentHelper();

// export default {
//     template,

//     inject: [
//         'repositoryFactory'
//     ],
//     data() {
//         return {
//             category: {
//                 name: ''
//             },
//             isLoading: false,
//             repository: null
//         }
//     },
//      metaInfo() {
//         return {
//             title: this.$createTitle(this.identifier),
//         };
//     },

//     computed: {
//     identifier (){
//         this.placeholder(this.category, 'name');
//     },
//     },
   


//     created() {
//             this.createdComponent();
//     }
//     ,
//     methods: {
//         createdComponent() {
//             this.repository = this.repositoryFactory.create('blog_category');
//         },
//         onSave(){
//             this.isLoading = true;
//             this.repository.save(this.category, Shopware.Context.api).then(() => {
//                 this.$router.push({ name: 'blog.bundle.index' });
//             }).finally(() => {
//                 this.isLoading = false;
//             });
//         },

//     },

//    watch: {
//         category: {
//             handler: function() {
//                 this.placeholder(this.category, 'name');
//             },
//         }       
//    }

// }
// 2 nd
// import template from './blog-category-create.html.twig';
import Criteria from 'src/core/data/criteria';


// Shopware.Component.register('blog-category-create', {
//     template,
//     inject: ['repositoryFactory'],

//     data() {
//         return {
//             category: null,
//             isLoading: false,
//             repository: null,
//         };
//     },

//     created() {
//         this.createdComponent();
//     },

//     methods: {
//         createdComponent() {
//             this.repository = this.repositoryFactory.create('blog_category');
//             // this.category = this.repository.create(Shopware.Context.api);
//            this.category =  this.repository.create(this.context);

//         },

//         onSave() {
//             this.isLoading = true;
//             this.repository.save(this.category, Shopware.Context.api).then(() => {
//                 this.$router.push({ name: 'blog.category.index' });
//             }).catch((e) => {
//                 console.error(e);
//             }).finally(() => {
//                 this.isLoading = false;
//             });
//         },

//         onCancel() {
//             this.$router.back();
//         }
//     },
//     computed: {
//          context() {
//         return Shopware.Context.api;
//     }
//     }
// });


// Shopware.Component.register('blog-category-create', {
//     template,

//     inject: [
//         'repositoryFactory'
//     ],
//     data() {
//         return {
//         category: {
//             name: ''
//         },            isLoading: false,
//             repository: null
//         }
//     },

//     created() {
//             this.createdComponent();

//     },

//     methods: {
//         createdComponent() {
//      this.repository = this.repositoryFactory.create('blog_category');
    
//      const newCategory = this.repository.create(Shopware.Context.api);
//             this.category = newCategory;
   

//         },
//         onSave(){
//             this.isLoading = true;
//            this.repository.save(this.category, Shopware.Context.api).then(() => {
//                 this.$router.push({ name: 'blog.bundle.index' });
//             }).finally(() => {
//                 this.isLoading = false;
//             });
//         }
//     }
// });

// import template from './blog-category-create.html.twig';

// Shopware.Component.register('blog-category-create', {
//     template,
//     inject: ['repositoryFactory'],

//     data() {
//         return {
//             category: null,
//             isLoading: false,
//             repository: null,
//         };
//     },

//     created() {
//         this.createdComponent();
//     },

//     methods: {
//         createdComponent() {
//             this.repository = this.repositoryFactory.create('blog_category');
//             try {
//                 this.category = this.repository.create(Shopware.Context.api);
//             } catch (e) {
//                 console.error('Error creating category', e);
//             }
//         },

//         onSave() {
//             this.isLoading = true;

//             this.repository.save(this.category, Shopware.Context.api).then(() => {
//                 this.$router.push({ name: 'blog.category.index' }); // Make sure this route exists
//             }).catch((e) => {
//                 console.error('Save failed:', e);
//             }).finally(() => {
//                 this.isLoading = false;
//             });
//         },

//         onCancel() {
//             this.$router.back();
//         }
//     }
// });
