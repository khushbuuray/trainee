import template from './zeobv-bundle-product-connections-grid.html.twig';
import './zeobv-bundle-product-connections-grid.scss';

const { Component, Context } = Shopware;
const { Criteria } = Shopware.Data;

Component.register('zeobv-bundle-product-connections-grid', {
    template,

    inject: ['acl', 'repositoryFactory'],

    props: {
        loading: {
            type: Boolean,
            required: false,
            default: false
        },
        product: {
            type: Object,
            required: true
        },
        showDiscountField: {
            type: Boolean,
            required: false,
            default: false
        },
        discount: {
            type: Number,
            required: false,
            default: null
        },
        productsInBundle: {
            type: Array,
            required: true
        },
        allowQuantitySelection: {
            type: Boolean,
            required: false,
            default: false
        },
        allowItemSelection: {
            type: Boolean,
            required: false,
            default: false
        },
        editable: {
            type: Boolean,
            required: false,
            default: true
        },
        onAddNewRow: {
            required: true
        }
    },

    data() {
        return {
            isLoading: false,
            bundleConnections: [],
            selectedItems: {}
        };
    },

    computed: {
        productSelectCriteria() {
            const criteria = new Criteria;

            const filters = [
                Criteria.equals('id', this.product.id),
            ];

            if (this.product.parentId) {
                filters.push(Criteria.equals('id', this.product.parentId));
            }

            criteria.addFilter(
                Criteria.not('or', filters)
            );

            return criteria;
        },

        context() {
            return { ...Context.api, inheritance: true };
        },

        productRepository() {
            return this.repositoryFactory.create('product');
        },

        columns() {
            const columns = [{
                property: 'position',
                dataIndex: 'position',
                label: 'zeobv-bundle-product.connection-grid.positionLabel',
                allowResize: true,
                align: 'center',
                inlineEdit: true,
                width: '60px'
            }, {
                property: 'product',
                dataIndex: 'product',
                label: 'zeobv-bundle-product.connection-grid.productLabel',
                allowResize: true,
                primary: true,
                inlineEdit: true,
                width: '200px'
            }, {
                property: 'quantity',
                dataIndex: 'quantity',
                label: 'zeobv-bundle-product.connection-grid.quantityLabel',
                allowResize: true,
                align: 'right',
                inlineEdit: true,
                width: '60px'
            }, {
                property: 'comment',
                dataIndex: 'comment',
                label: 'zeobv-bundle-product.connection-grid.commentLabel',
                allowResize: true,
                align: 'left',
                inlineEdit: true,
                width: '120px'
            }];
            
            if (this.allowQuantitySelection) {
                columns.push({
                    property: 'modifiable',
                    dataIndex: 'modifiable',
                    label: 'zeobv-bundle-product.connection-grid.modifiableLabel',
                    allowResize: true,
                    align: 'left',
                    inlineEdit: true,
                    width: '60px'
                });
            }
            
            if (this.allowItemSelection) {
                columns.push({
                    property: 'optional',
                    dataIndex: 'optional',
                    label: 'zeobv-bundle-product.connection-grid.optionalLabel',
                    allowResize: true,
                    align: 'left',
                    inlineEdit: true,
                    width: '60px'
                });
            }

            return columns
        }
    },

    watch: {
        productsInBundle: {
            handler(productsInBundle) {
                this.bundleConnections = productsInBundle;
            },
            deep: true,
            immediate: true
        }
    },

    methods: {
        onDeleteSelectedItems() {
            Object.values(this.selectedItems).forEach(object => {
                this.$emit('item-delete', object)
            });
        },

        onProductSelected(item, value) {
            item.productId = value;

            const criteria = new Criteria();
            criteria.setIds([value]);

            return this.productRepository.search(criteria, this.context).then((products) => {
                if (products.length) {
                    item.product = products[0];
                }
            });
        },

        onSelectionChanged(selection) {
            this.selectedItems = selection;
        },

        onPositionChanged(bundleConnections) {
            this.$emit('updated', bundleConnections);
        },

        onDiscountPercentageChanged(discount) {
            this.$emit('update:discount', discount);
        },

        productSelectionLabel(product) {
            if (!product) {
                return "";
            }

            return `${product.productNumber} | ${product.translated.name}`;
        },
    }
});
