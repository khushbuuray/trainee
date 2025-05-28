import template from './sw-cms-el-my-image-text-reversed.html.twig';
import './sw-cms-el-my-image-text-reversed.scss';

Shopware.Component.register('sw-cms-el-my-image-text-reversed', {
    template,

    mixins: ['cms-element'],

    computed: {
        dailyUrl() {
               return `https://www.dailymotion.com/embed/video/${this.element.config.dailyUrl.value}`;
        }
    },

    created() {
        this.initElementConfig('my-image-text-reversed');
        this.initElementData('my-image-text-reversed');
        if (!this.element.config) {
        this.$set(this, 'element', { config: {} });
    }

    if (!this.element.config.dailyUrl) {
        this.$set(this.element.config, 'dailyUrl', {
            source: 'static',
            value: ''
        });
    }
    }
});
