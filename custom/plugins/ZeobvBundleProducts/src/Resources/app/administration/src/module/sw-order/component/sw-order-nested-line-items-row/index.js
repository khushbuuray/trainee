import template from './sw-order-nested-line-items-row.html.twig'

const { Component } = Shopware

Component.override('sw-order-nested-line-items-row', {
    template,

    computed: {
        currencyFilter() {
            return Shopware.Filter.getByName('currency');
        },
    }
})
