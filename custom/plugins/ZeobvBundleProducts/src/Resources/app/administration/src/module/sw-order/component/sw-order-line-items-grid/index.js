import template from './sw-order-line-items-grid.html.twig'
import './sw-order-line-items-grid.scss'

const { Component } = Shopware
const { Criteria } = Shopware.Data

Component.override('sw-order-line-items-grid', {
    template,

    data() {
        return {
            bundleConnectionModalOpen: false,
            bundleConnectionModalItem: null
        }
    },

    created() {
        this.updateLineItemsWithBundleConnections()
    },

    computed: {
        orderLineItems() {
            const orderLineItems = this.$super('orderLineItems')
            orderLineItems.forEach((item) => {
                if (!item.children) {
                    return
                }

                item.children.sort((a, b) => {
                    if (a.position > b.position) {
                        return 1
                    }
                    if (a.position < b.position) {
                        return -1
                    }
                    return 0
                })
            })

            if (!this.searchTerm) {
                return orderLineItems.filter((item) => {
                    if (item.parentId && item.type === 'bundle_product_item') {
                        return false
                    }

                    return true
                })
            }

            // Filter based on the product label is not blank and contains the search term or not
            const keyWords = this.searchTerm.split(/[\W_]+/gi)
            return orderLineItems.filter((item) => {
                if (!item.label) {
                    return false
                }

                if (item.parentId) {
                    return false
                }

                return keyWords.every((key) =>
                    item.label.toLowerCase().includes(key.toLowerCase())
                )
            })
        }
    },

    methods: {
        isBundleItem(item) {
            return (
                item.payload != undefined && 'bundleRelations' in item.payload
            )
        },

        openBundleConnectionModalForItem(item) {
            this.bundleConnectionModalOpen = true
            this.bundleConnectionModalItem = item
        },

        closeBundleConnectionModal() {
            this.bundleConnectionModalOpen = false
            this.bundleConnectionModalItem = null
        },

        updateLineItemsWithBundleConnections() {
            this.orderLineItems.forEach((item) => {
                if (item.type !== 'product' || !this.isBundleItem(item)) {
                    return
                }

                this._addChildLineItems(item)
            })
        },

        _addChildLineItems(parent) {
            const criteria = new Criteria()
            criteria.addFilter(Criteria.equals('parentId', parent.id))

            this.orderLineItemRepository
                .search(criteria, Shopware.Context.api)
                .then((results) => {
                    if (results.length < 1) {
                        return
                    }

                    parent.children = results
                    parent.hasBundleRelations = true
                })
        },

        _createLineItemForProduct(productData) {
            const orderLineItem = this.orderLineItemRepository.create(
                Shopware.Context.api
            )

            orderLineItem.id = productData.id
            orderLineItem.productId = productData.productId
            orderLineItem.label = productData.productName
            orderLineItem.quantity = productData.quantityInBundle
            orderLineItem.comment = productData.comment
            orderLineItem.unitPrice =
                this.order.taxStatus === 'net'
                    ? productData.productPrice.net
                    : productData.productPrice.gross
            orderLineItem.totalPrice =
                this.order.taxStatus === 'net'
                    ? productData.productPrice.net
                    : productData.productPrice.gross

            return orderLineItem
        }
    }
})
