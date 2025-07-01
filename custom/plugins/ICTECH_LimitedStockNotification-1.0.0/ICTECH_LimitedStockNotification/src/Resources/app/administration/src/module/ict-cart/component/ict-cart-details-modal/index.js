import template from './ict-cart-details-modal.html.twig';

const {Component, Mixin} = Shopware;
const {Criteria} = Shopware.Data;

Component.register('ict-cart-details-modal', {
    template,

    inject: ['repositoryFactory'],

    mixins: [
        Mixin.getByName('notification'),
        Mixin.getByName('placeholder')
    ],

    props: [
        'ictCart',
        'isLoading',
        'onClose'
    ],
    computed: {
        customerWishlistRepository() {
            return this.repositoryFactory.create('customer_wishlist');
        },

        items() {
            if (this.ictCart.lineItems === null) {
                return Object.values(this.ictCart.lineItemsWishlist);
            } else {
                return Object.values(this.ictCart.lineItems);
            }
        },

        getLineItemColumns() {
            const columnDefinitions = [{
                property: 'label',
                dataIndex: 'label',
                label: this.$t('ict-cart.detailsModal.columnProductName'),
                allowResize: false,
                primary: true,
                inlineEdit: true,
                width: '200px'
            }, {
                property: 'quantity',
                dataIndex: 'quantity',
                label: this.$t('ict-cart.detailsModal.columnQuantity'),
                allowResize: false,
                align: 'right',
                inlineEdit: true,
                width: '120px'
            }];

            return columnDefinitions;
        },

        getLineItemWishlistColumns() {
            const columnWishlistDefinitions = [{
                property: 'name',
                dataIndex: 'name',
                label: this.$t('ict-cart.detailsModal.columnProductName'),
                allowResize: false,
                primary: true,
                inlineEdit: true,
                width: '200px'
            }, {
                property: 'quantity',
                dataIndex: 'quantity',
                label: this.$t('ict-cart.detailsModal.columnQuantity'),
                allowResize: false,
                align: 'right',
                inlineEdit: true,
                width: '120px'
            }];

            return columnWishlistDefinitions;
        },
    },

    methods: {
        createdComponent() {
            this.repository = this.repositoryFactory.create('ict_cart_wishlist');
            this.getBundle();
        },
        navigateToProduct(productId) {
            // First close the modal
            this.onClose();

            // Wait a little bit with starting the navigation so the modal can close.
            setTimeout(() => {
                this.$router.push({ name: 'sw.product.detail', params: { id: productId } });
            }, 500)
        }
    }
});
