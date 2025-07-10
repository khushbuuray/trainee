Shopware.Component.override('sw-product-detail', {
    methods: {
        onSave() {
            const saveProductReturn = this.$super('onSave')

            this.$emit('save-product-bundle-connections')

            return saveProductReturn
        }
    }
})
