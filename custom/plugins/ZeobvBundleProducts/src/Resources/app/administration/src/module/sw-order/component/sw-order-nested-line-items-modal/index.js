const { Component, Filter } = Shopware

Component.override('sw-order-nested-line-items-modal', {
    computed: {
        modalTitle() {
            const price = Filter.getByName('currency')(this.lineItem.totalPrice.toFixed(2), this.order.currency.shortName);

            return this.$tc('sw-order.nestedLineItemsModal.titlePrefix', 0, {
                lineItemLabel: this.lineItem.label,
                price,
            });
        },
    },
})
