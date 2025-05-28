import template from './sw-cms-el-config-my-image-text-reversed.html.twig';

Shopware.Component.register('sw-cms-el-config-my-image-text-reversed', {
    template,
     mixins: [
        'cms-element'
    ],

    computed: {
        dailyUrl: {
            get() {
                return this.element.config.dailyUrl.value;
            },

            set(value) {
                this.element.config.dailyUrl.value = value;
            }
        }
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            console.log('element');
            
            this.initElementConfig('my-image-text-reversed');
        },

        onElementUpdate(value) {
            this.element.config.dailyUrl.value = value;

            this.$emit('element-update', this.element);
        }
    }
});