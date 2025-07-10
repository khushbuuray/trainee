const { Component } = Shopware;

Component.override('sw-import-export-edit-profile-general', {
    computed: {
        supportedEntities() {
            const supportedEntities = this.$super('supportedEntities');

            supportedEntities.push({
                value: 'zeobv_product_bundle_connection',
                label: this.$tc('sw-import-export.profile.productBundleConnectionLabel'),
                type: 'import-export',
            });

            return supportedEntities;
        },
    },
});

