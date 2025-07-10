import template from './zeobv-bundle-connections-modal.html.twig';

const {Component, Mixin} = Shopware;

Component.register('zeobv-bundle-connections-modal', {
    template,

    inject: ['repositoryFactory'],

    mixins: [
        Mixin.getByName('notification'),
        Mixin.getByName('placeholder')
    ],

    props: [
        'order',
        'item',
        'isLoading',
        'onClose'
    ],

    computed: {
        currencyFilter() {
            return Shopware.Filter.getByName('currency');
        },

        getLineItemColumns() {
            return [{
                property: 'label',
                dataIndex: 'label',
                label: 'sw-order.detailBase.columnProductName',
                allowResize: false,
                primary: true,
                inlineEdit: true,
                width: '200px'
            }, {
                property: 'comment',
                dataIndex: 'comment',
                label: 'sw-order.zeobv-bundle-connections.modal.commentLabel',
                allowResize: false,
                inlineEdit: true,
                width: '60px'
            }, {
                property: 'unitPrice',
                dataIndex: 'unitPrice',
                label: this.order.taxStatus === 'net' ?
                    'sw-order.detailBase.columnPriceNet' :
                    'sw-order.detailBase.columnPriceGross',
                allowResize: false,
                align: 'right',
                inlineEdit: true,
                width: '120px'
            }, {
                property: 'quantity',
                dataIndex: 'quantity',
                label: 'sw-order.zeobv-bundle-connections.modal.quantityLabel',
                allowResize: false,
                inlineEdit: true,
                width: '60px'
            },{
                property: 'totalPrice',
                dataIndex: 'totalPrice',
                label: this.order.taxStatus === 'gross' ?
                    'sw-order.detailBase.columnTotalPriceGross' :
                    'sw-order.detailBase.columnTotalPriceNet',
                allowResize: false,
                align: 'right',
                inlineEdit: true,
                width: '120px'
            }];
        },
    },

    methods: {
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
