import template from './sw-product-detail-base.html.twig'
import './sw-product-detail-base.scss'

const { Component, Context, Utils } = Shopware
const { mapPropertyErrors, mapState } = Shopware.Component.getComponentHelper()
const { EntityCollection, Criteria } = Shopware.Data

Component.override('sw-product-detail-base', {
    template,

    inject: ['systemConfigApiService', 'repositoryFactory'],

    data() {
        return {
            bundleConfig: {},
            bundleConnectionLimit: 100,
            productsInBundle: [],
            productIdsInBundle: [],
            priceModes: [
                { label: this.$tc('sw-product.bundle.field.option.priceModeInherit'), value: null },
                { label: this.$tc('sw-product.bundle.field.option.priceModeMeet'), value: 'meetProductPrice' },
                { label: this.$tc('sw-product.bundle.field.option.priceModeSum'), value: 'sumProductPrices' },
                { label: this.$tc('sw-product.bundle.field.option.priceModeDisabled'), value: 'noCalculation' }
            ]
        }
    },

    watch: {
        '$route.params.id': function () {
            this.loadBundleProducts()
        },
    },

    computed: {
        ...mapState('swProductDetail', ['loading']),

        ...mapPropertyErrors('product', ['extensions']),

        bundleProductId() {
            return this.$route.params.id
        },

        zeobvBundleProductsShowOnStorefront: {
            get: function () {
                const product = Shopware.State.get('swProductDetail').product

                if (!product.customFields || product.customFields.length === 0) {
                    product.customFields = {}
                }

                if (typeof product.customFields.zeobvBundleProductsShowOnStorefront === 'undefined') {
                    product.customFields.zeobvBundleProductsShowOnStorefront = false
                }

                return product.customFields.zeobvBundleProductsShowOnStorefront == 1
            },

            set: function (value) {
                const product = Shopware.State.get('swProductDetail').product

                if (product.customFields.length === 0) product.customFields = {}

                product.customFields.zeobvBundleProductsShowOnStorefront = value == 1
            },
        },

        zeobvBundleProductsOfferBundleOptionally: {
            get: function () {
                const product = Shopware.State.get('swProductDetail').product

                if (!product.customFields || product.customFields.length === 0) {
                    product.customFields = {}
                }

                if (typeof product.customFields.zeobvBundleProductsOfferBundleOptionally === 'undefined') {
                    product.customFields.zeobvBundleProductsOfferBundleOptionally = false
                }

                return product.customFields.zeobvBundleProductsOfferBundleOptionally == 1
            },

            set: function (value) {
                const product = Shopware.State.get('swProductDetail').product

                if (product.customFields.length === 0) product.customFields = {}

                product.customFields.zeobvBundleProductsOfferBundleOptionally = value == 1
            },
        },

        zeobvBundleProductsAllowItemSelection: {
            get: function () {
                const product = Shopware.State.get('swProductDetail').product

                if (!product.customFields || product.customFields.length === 0) {
                    product.customFields = {}
                }

                if (typeof product.customFields.zeobvBundleProductsAllowItemSelection === 'undefined') {
                    product.customFields.zeobvBundleProductsAllowItemSelection = false
                }

                return product.customFields.zeobvBundleProductsAllowItemSelection == 1
            },

            set: function (value) {
                const product = Shopware.State.get('swProductDetail').product

                if (product.customFields.length === 0) product.customFields = {}

                product.customFields.zeobvBundleProductsAllowItemSelection = value == 1
            },
        },

        zeobvBundleProductsAllowQtySelection: {
            get: function () {
                const product = Shopware.State.get('swProductDetail').product

                if (!product.customFields || product.customFields.length === 0) {
                    product.customFields = {}
                }

                if (typeof product.customFields.zeobvBundleProductsAllowQtySelection === 'undefined') {
                    product.customFields.zeobvBundleProductsAllowQtySelection = false
                }

                return product.customFields.zeobvBundleProductsAllowQtySelection == 1
            },

            set: function (value) {
                const product = Shopware.State.get('swProductDetail').product

                if (product.customFields.length === 0) product.customFields = {}

                product.customFields.zeobvBundleProductsAllowQtySelection = value == 1
            },
        },

        zeobvBundleProductsShowItemPricesOnStorefront: {
            get: function () {
                const product = Shopware.State.get('swProductDetail').product

                if (!product.customFields || product.customFields.length === 0) {
                    product.customFields = {}
                }

                if (typeof product.customFields.zeobvBundleProductsShowItemPricesOnStorefront === 'undefined') {
                    product.customFields.zeobvBundleProductsShowItemPricesOnStorefront = false
                }

                return product.customFields.zeobvBundleProductsShowItemPricesOnStorefront == 1
            },

            set: function (value) {
                const product = Shopware.State.get('swProductDetail').product

                if (product.customFields.length === 0) product.customFields = {}

                product.customFields.zeobvBundleProductsShowItemPricesOnStorefront = value == 1
            },
        },

        zeobvBundleProductsShowItemsTotalPriceOnStorefront: {
            get: function () {
                const product = Shopware.State.get('swProductDetail').product

                if (!product.customFields || product.customFields.length === 0) {
                    product.customFields = {}
                }

                if (typeof product.customFields.zeobvBundleProductsShowItemsTotalPriceOnStorefront === 'undefined') {
                    product.customFields.zeobvBundleProductsShowItemsTotalPriceOnStorefront = false
                }

                return product.customFields.zeobvBundleProductsShowItemsTotalPriceOnStorefront == 1
            },

            set: function (value) {
                const product = Shopware.State.get('swProductDetail').product

                if (product.customFields.length === 0) product.customFields = {}

                product.customFields.zeobvBundleProductsShowItemsTotalPriceOnStorefront = value == 1
            },
        },

        zeobvBundleProductsDiscountPercentage: {
            get: function () {
                const product = Shopware.State.get('swProductDetail').product
                if (!product.customFields || product.customFields.length === 0) {
                    product.customFields = {}
                }

                if (typeof product.customFields.zeobvBundleProductsDiscountPercentage === 'undefined') {
                    product.customFields.zeobvBundleProductsDiscountPercentage = null
                }

                return product.customFields.zeobvBundleProductsDiscountPercentage
            },

            set: function (value) {
                const product = Shopware.State.get('swProductDetail').product

                if (product.customFields.length === 0) product.customFields = {}

                product.customFields.zeobvBundleProductsDiscountPercentage = value
            },
        },

        zeobvBundleProductPriceMode: {
            get: function () {
                const product = Shopware.State.get('swProductDetail').product
                if (!product.customFields || product.customFields.length === 0) {
                    product.customFields = {}
                }

                return product.customFields.zeobvBundleProductPriceMode || null
            },

            set: function (value) {
                const product = Shopware.State.get('swProductDetail').product

                if (product.customFields.length === 0) product.customFields = {}

                product.customFields.zeobvBundleProductPriceMode = value
            },
        },

        actualPriceMode() {
            return this.zeobvBundleProductPriceMode || this.bundleConfig['bundleProductPriceMode']
        },

        productRepository() {
            return this.repositoryFactory.create('product')
        },

        productBundleConnectionRepository() {
            return this.repositoryFactory.create('zeobv_product_bundle_connection')
        },

        pickwareProductRepository() {
            try {
                return this.repositoryFactory.create('pickware_erp_pickware_product')
            } catch (e) {
                return undefined
            }
        },

        productCriteria() {
            const productCriteria = new Criteria()

            productCriteria.addFilter(Criteria.equals('parentId', null))

            return productCriteria
        },

        showDiscountField() {
            return this.actualPriceMode === 'sumProductPrices';
        },

        showOfferBundleOptionallySwitch() {
            return this.zeobvBundleProductsShowOnStorefront
                && this.productsInBundle.length > 0;
        },

        showAllowItemSelectionSwitch() {
            return this.actualPriceMode === 'sumProductPrices'
                && this.zeobvBundleProductsShowOnStorefront
                && this.productsInBundle.length > 0;
        },

        showAllowQtySelectionSwitch() {
            return this.actualPriceMode === 'sumProductPrices'
            && this.zeobvBundleProductsShowOnStorefront
            && this.productsInBundle.length > 0;
        },

        showShowBundleProductsOnStorefrontSwitch() {
            return this.productsInBundle.length > 0;
        },

        showShowItemPricesOnStorefrontSwitch() {
            return this.zeobvBundleProductsShowOnStorefront
                && this.productsInBundle.length > 0;
        },

        showShowTotalPriceOnStorefrontSwitch() {
            return this.zeobvBundleProductsShowItemPricesOnStorefront
            && this.showShowItemPricesOnStorefrontSwitch;
        },

        pickwareFound() {
            return this.pickwareProductRepository !== undefined
        }
    },

    created() {
        this.createdComponentBundleProduct();
    },

    mounted() {
        this.loadBundleProducts()
    },

    destroyed() {
        this._unregisterEventListeners()
    },

    methods: {

        createdComponentBundleProduct() {

            this._registerEventListeners()

            this.isLoading = true;

            this.readConfig().then((config) => {
                for (const key in config) {
                    this.bundleConfig = {
                        ...this.bundleConfig,
                        [key.replace('ZeobvBundleProducts.config.', '')]: config[key]
                    };
                }

                this.isLoading = false;
            });
        },

        readConfig() {
            return this.systemConfigApiService.getValues('ZeobvBundleProducts.config', null);
        },

        loadBundleProducts() {
            if (!this.bundleProductId) {
                return
            }

            this.productsInBundle = new EntityCollection(
                this.productRepository.route,
                this.productRepository.entityName,
                Context.api
            )

            const criteria = new Criteria()
            criteria.addFilter(Criteria.equals('bundleProductId', this.bundleProductId))
            criteria.addAssociation('product')
            criteria.addSorting(Criteria.sort('position', 'ASC', false))
            criteria.setLimit(this.bundleConnectionLimit)

            this.productBundleConnectionRepository
                .search(criteria, { ...Context.api, inheritance: true })
                .then((bundleConnections) => {
                    if (bundleConnections.length <= 0) {
                        return Promise.resolve()
                    }

                    let count = 0
                    this.productsInBundle = bundleConnections.map((bundleConnection) => {
                        if (bundleConnection.position === 0) {
                            bundleConnection.position = ++count
                        }

                        return bundleConnection
                    })

                    this.productIdsInBundle = bundleConnections.map((bundleConnection) => {
                        return bundleConnection.productId
                    })
                })
        },

        onAddNewRow() {
            const item = this.productBundleConnectionRepository.create()

            const sortedProducts = [...this.productsInBundle].sort((itemA, itemB) => {
                if (itemA.position < itemB.position) {
                    return 1
                }

                if (itemA.position > itemB.position) {
                    return -1
                }

                return 0
            })

            item.id = Utils.createId()
            item.bundleProductId = this.bundleProductId
            item.productId = null
            item.quantity = 1
            item.position = sortedProducts.length > 0 ? sortedProducts[0].position + 1 : 1
            item.comment = null

            this.productsInBundle = [...this.productsInBundle, item]
        },

        onItemDelete(item) {
            this.productsInBundle = this.productsInBundle.filter((connection) => {
                return connection.id != item.id
            })
        },

        onDiscountUpdated(value) {
            this.zeobvBundleProductsDiscountPercentage = value
        },

        onBundleConnectionsUpdated(bundleConnections) {
            const positions = []
            this.productsInBundle = bundleConnections.map((item) => {
                // Fix (legacy) position errors
                if (positions.includes(item.position)) {
                    item.position = item.position + 1
                }

                positions.push(item.position)

                return item
            })
        },

        getProductDetailComponent: function () {
            let component = this.$parent

            while (component.$options.name !== 'sw-product-detail') {
                if (component.$parent) {
                    component = component.$parent
                } else {
                    return undefined
                }
            }

            return component
        },

        saveBundleProductConnections: function () {
            if (this.productsInBundle.length) {
                Shopware.State.commit('swProductDetail/setLoading', ['variants', true])
            }

            let copyBundleProducts = this.productsInBundle

            this._deleteExistingBundleConnections(copyBundleProducts).then(() => {
                const promises = copyBundleProducts.map((productBundleConnection) => {
                    return this.productBundleConnectionRepository
                        .save(productBundleConnection, Context.api)
                        .then(() => {
                            productBundleConnection._isNew = false
                        })
                        .catch((e) => {
                            Shopware.State.commit('swProductDetail/setLoading', ['variants', false])
                            this.createNotificationError({
                                title: this.$tc('sw-product.bundle.message.saveError'),
                                message: e,
                            })
                        })
                })

                if (promises.length < 1) {
                    this._changePickwareStockManagementForBundleProduct(false)
                    return
                }

                if (this.pickwareFound) {
                    this._changePickwareStockManagementForBundleProduct(true)
                }

                return Promise.all(promises).then(() => {
                    Shopware.State.commit('swProductDetail/setLoading', ['variants', false])
                    this.loadBundleProducts()

                    this.createNotificationSuccess({
                        title: this.$tc('global.default.success'),
                        message: this.$tc('sw-product.bundle.message.saveSuccess'),
                    })
                })
            })
        },

        async _changePickwareStockManagementForBundleProduct(isStockManagementDisabled) {
            const result = await this.pickwareProductRepository.search(
                new Criteria().addFilter(Criteria.equals('productId', this.bundleProductId)),
                Context.api
            )
            
            if (!result.length) {
                return
            }

            const pickwareProduct = result.first()

            if (pickwareProduct.isStockManagementDisabled === isStockManagementDisabled) {
                return
            }

            pickwareProduct.isStockManagementDisabled = isStockManagementDisabled

            this.pickwareProductRepository.save(pickwareProduct, Context.api)
        },

        async _deleteExistingBundleConnections(copyBundleProducts) {
            const response = await this.productBundleConnectionRepository.searchIds(
                new Criteria().addFilter(Criteria.equals('bundleProductId', this.bundleProductId)),
                Context.api
            )

            const connectionsToKeep = copyBundleProducts.map((connection) => {
                return connection.id
            })

            if (response.data.length) {
                return await Promise.all(
                    response.data.map((id) => {
                        if (!connectionsToKeep.includes(id)) {
                            this.productBundleConnectionRepository.delete(id, Context.api)
                        }
                    })
                )
            }
        },

        _registerEventListeners() {
            const ProductDetailComponent = this.getProductDetailComponent()

            this.saveBundleProductConnections = this.saveBundleProductConnections.bind(this)

            if (!this.registeredEventListeners && ProductDetailComponent !== undefined) {
                ProductDetailComponent.$on('save-product-bundle-connections', this.saveBundleProductConnections)
            }

            this.registeredEventListeners = true
        },

        _unregisterEventListeners() {
            const ProductDetailComponent = this.getProductDetailComponent()

            if (ProductDetailComponent !== undefined) {
                ProductDetailComponent.$off('save-product-bundle-connections')
            }

            this.registeredEventListeners = false
        },
    },
})
