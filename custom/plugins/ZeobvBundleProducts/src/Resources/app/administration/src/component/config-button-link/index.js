import template from './config-button-link.html.twig';
import './config-button-link.scss';

Shopware.Component.register('config-button-link', {
    template,

    methods: {
        openHelp() {
            window.open('https://diztech.freshdesk.com/support/solutions/categories/103000257433', '_blank');
        }
    }
});
