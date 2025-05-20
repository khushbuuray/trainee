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







