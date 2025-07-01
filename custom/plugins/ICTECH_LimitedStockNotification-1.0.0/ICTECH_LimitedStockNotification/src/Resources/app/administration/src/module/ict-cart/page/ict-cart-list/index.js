import template from './ict-cart-list.html.twig';
import './ict-cart-list.scss';

const { Component, Mixin } = Shopware;
const { Criteria } = Shopware.Data;

Component.register('ict-cart-list', {
    template,

    inject: ['repositoryFactory'],

    mixins: [
        Mixin.getByName('notification'),
        Mixin.getByName('listing')
    ],

    data() {
        return {
            entities: null,
            currencies: {},
            sortBy: 'created_at',
            sortDirection: 'DESC',
            naturalSorting: true,
            isLoading: false,
            isBulkLoading: false,
            total: 0,
            ictCartDetailsModalOpened: false,
            ictCartDetailItems: [],
            ictCartDetailCart: null,
        };
    },

    metaInfo() {
    },

    computed: {

        repository() {
            return this.repositoryFactory.create('ict_cart_wishlist');
        },

        columns() {
            return this.getColumns();
        },
    },

    methods: {
        getList() {
            this.isLoading = true;

            const criteria = new Criteria();
            criteria.addAssociation('salesChannel');

            criteria.setTerm(this.term);
            criteria.addSorting(Criteria.sort('createdAt', this.sortDirection, this.naturalSorting));

            return Promise.all([
                this.repository.search(criteria, Shopware.Context.api),
            ]).then((result) => {
                const entities = result[0];

                this.total = entities.total;
                this.entities = entities;

                this.isLoading = false;
                this.selection = {};
            }).catch(() => {
                this.isLoading = false;
            });
        },

        updateTotal({ total }) {
            this.total = total;
        },

        getColumns() {
            return [{
                property: 'email',
                dataIndex: 'email',
                label: this.$t('ict-cart.list.column.email'),
                allowResize: true,
                primary: true
            }, {
                property: 'wishlistId',
                dataIndex: 'wishlistId',
                label: this.$t('ict-cart.list.column.wishlistId'),
                allowResize: true
            }, {
                property: 'salesChannel.name',
                label: this.$t('ict-cart.list.column.salesChannel'),
                allowResize: true
            }, {
                property: 'createdAt',
                dataIndex: 'createdAt',
                label: this.$t('ict-cart.list.column.createdAt'),
                allowResize: true
            }, {
                property: 'updatedAt',
                dataIndex: 'updatedAt',
                label: this.$t('ict-cart.list.column.updatedAt'),
                allowResize: true
            }];
        },

        onOpenDetailModel(item) {
            this.ictCartDetailsModalOpened = true;
            this.ictCartDetailCart = item;
            this.ictCartDetailItems = item.items;
        },

        onCloseIctCartDetailsModal() {
            this.ictCartDetailsModalOpened = false;
        }
    }
});
