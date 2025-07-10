import Plugin from 'src/plugin-system/plugin.class';

export default class DiztechBundleConfiguratorPlugin extends Plugin {
    static options = {
        checkboxSelector: '.bundle-product-item-selection',
        qtySelectBoxSelector: '.zeobv-bundle-item-qty-select',
        variantSelectBoxSelector: '.zeobv-bundle-item-variant-select',
        bundleItemPriceSelectorTemplate: '[data-price-%connectionId%]',
        calculateBundlePriceUrl: null
    };

    init() {
        const checkboxes = document.querySelectorAll(this.options.checkboxSelector);
        this.itemSelection = {};

        for (const checkbox of checkboxes) {
            const productId = checkbox.dataset.productId;
            const connectionId = checkbox.dataset.connectionId;
            const productQuantity = this._getQuantity(connectionId)

            this.itemSelection[connectionId] = {};
            this.itemSelection[connectionId]['productId'] = productId;
            this.itemSelection[connectionId]['qty'] = productQuantity;

            if (checkbox.type === 'checkbox') {
                this.registerCheckboxEvent(checkbox);
            }
        }

        const qtySelectBoxes = document.querySelectorAll(this.options.qtySelectBoxSelector);
        for (const qtySelectBox of qtySelectBoxes) {
            this.registerQtySelectBoxEvent(qtySelectBox);
        }

        const variantSelectBoxes = document.querySelectorAll(this.options.variantSelectBoxSelector);
        for (const variantSelectBox of variantSelectBoxes) {
            this.registerVariantSelectBoxEvent(variantSelectBox);
        }
    }

    registerCheckboxEvent(checkbox) {
        checkbox.addEventListener('change', this.handleCheckboxChange.bind(this));
    }

    registerQtySelectBoxEvent(qtySelectBox) {
        qtySelectBox.addEventListener('change', this.handleQtySelectBoxChange.bind(this));
    }

    registerVariantSelectBoxEvent(qtySelectBox) {
        qtySelectBox.addEventListener('change', this.handleVariantSelectBoxChange.bind(this));
    }

    handleCheckboxChange(event) {
        const checkbox = event.target;
        const isChecked = checkbox.checked;
        const connectionId = checkbox.dataset.connectionId
        const productQuantity = this._getQuantity(connectionId);

        if (isChecked) {
            if (this.itemSelection[connectionId]['qty'] !== null) {
                return;
            }

            this.itemSelection[connectionId]['qty'] = productQuantity;
        } else {
            this.itemSelection[connectionId]['qty'] = null;

            if (Object.values(this.itemSelection).filter(qty => qty !== null).length === 0) {
                // recheck the checkbox
                checkbox.checked = true;
                this.itemSelection[connectionId]['qty'] = productQuantity;
            }
        }

        this.determineCalculatedBundlePrice()
    }

    handleQtySelectBoxChange(event) {
        const qtySelectBox = event.target;
        const connectionId = qtySelectBox.dataset.connectionId;
        const productQuantity = parseInt(qtySelectBox.value);
        const qtyInput = this._getQuantityInput(connectionId);
        
        qtyInput.value = productQuantity

        if (this.itemSelection[connectionId]['qty'] === null) {
            return;
        }

        this.itemSelection[connectionId]['qty'] = productQuantity;

        this.determineCalculatedBundlePrice()
    }

    handleVariantSelectBoxChange(event) {
        const variantSelectBox = event.target;
        const connectionId = variantSelectBox.dataset.connectionId;
        const variantId = variantSelectBox.value;

        this.itemSelection[connectionId]['productId'] = variantId;

        this.determineCalculatedBundlePrice()
    }

    determineCalculatedBundlePrice() {
        const url = this.options.calculateBundlePriceUrl;

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                itemSelection: this.itemSelection
            })
        })
        .then(response => response.json())
        .then(data => {
            if (!data.hasOwnProperty('calculatedBundlePrice')) {
                return;
            }

            this.rerenderPricing(data)
            this.rerenderBundleItemControlInputs(data.products)
        });
    }

    rerenderBundleItemControlInputs(productData) {
        for (const connectionId in productData) {
            const product = productData[connectionId];
            const qtySelectBoxesForConnection = document.querySelectorAll(`.zeobv-bundle-item-qty-select-${connectionId}`);
            const itemSelectBoxesForConnection = document.querySelectorAll(`.bundle-product-item-selection-${connectionId}`);
            const availabilityInputForConnection = document.querySelector(`#zeobv-bundle-item-availability-${connectionId}`);

            if (product.available) {
                availabilityInputForConnection.classList.remove('zeobv-bundle-item-availability--unavailable');
                availabilityInputForConnection.classList.add('zeobv-bundle-item-availability--available');
                for (const itemSelectBox of itemSelectBoxesForConnection) {
                    if (itemSelectBox.dataset.optional === 'false') {
                        continue;
                    }
                    
                    itemSelectBox.checked = true;
                    itemSelectBox.removeAttribute('disabled');
                }
                for (const qtySelectBox of qtySelectBoxesForConnection) {
                    qtySelectBox.removeAttribute('disabled');
                    this.rerenderQtySelectBoxOptions(qtySelectBox, product);
                }
            } else {
                availabilityInputForConnection.classList.remove('zeobv-bundle-item-availability--available');
                availabilityInputForConnection.classList.add('zeobv-bundle-item-availability--unavailable');
                for (const itemSelectBox of itemSelectBoxesForConnection) {
                    if (itemSelectBox.dataset.optional === 'false') {
                        continue;
                    }

                    itemSelectBox.checked = false;
                    itemSelectBox.setAttribute('disabled', 'disabled');
                }
                for (const qtySelectBox of qtySelectBoxesForConnection) {
                    qtySelectBox.setAttribute('disabled', 'disabled');
                }
            }
        }
    }

    rerenderQtySelectBoxOptions(qtySelectBox, product) {
        const maxQuantity = product.maxPurchase ? product.maxPurchase : 100;
        const minQuantity = product.minPurchase ? product.minPurchase : 1;
        const step = product.purchaseSteps ? product.purchaseSteps : 1;
        const selectedQuantity = parseInt(qtySelectBox.value);

        qtySelectBox.innerHTML = '';

        for (let i = minQuantity; i <= maxQuantity; i += step) {
            const option = document.createElement('option');
            option.value = i;
            option.text = i;
            qtySelectBox.appendChild(option);
        }
    
        let qtyChanged = false;
        qtySelectBox.value = selectedQuantity;

        if (selectedQuantity > maxQuantity) {
            qtyChanged = true;
            qtySelectBox.value = maxQuantity;
        } else if (selectedQuantity < minQuantity) {
            qtyChanged = true;
            qtySelectBox.value = minQuantity;
        }

        if (qtyChanged) {
            this.handleQtySelectBoxChange({
                target: qtySelectBox
            });
        }
    }

    rerenderPricing(priceData) {
        this._renderBundlePrice(priceData);

        this._renderBundleItemPrices(priceData);
    }

    _renderBundlePrice(priceData) {
        const bundleProductPriceEl = document.querySelector('.product-detail-price');

        if (!bundleProductPriceEl) {
            return;
        }

        bundleProductPriceEl.innerHTML = this._replacePrice(
            bundleProductPriceEl.innerHTML,
            priceData.calculatedBundlePrice.unitPrice
        )

        if (!bundleProductPriceEl.classList.contains('with-list-price')) {
            return;
        }

        const bundleProductListPriceEl = document.querySelector('.product-detail-list-price-wrapper .list-price-price');
        if (!bundleProductListPriceEl) {
            return;
        }

        bundleProductListPriceEl.innerHTML = this._replacePrice(
            bundleProductListPriceEl.innerHTML,
            priceData.calculatedBundlePrice.listPrice.price
        )
    }

    _renderBundleItemPrices(priceData) {
        let total = 0

        for (const connectionId in priceData.calculatedProductPrices) {
            const price = priceData.calculatedProductPrices[connectionId];
            const product = priceData.products[connectionId];

            if (!price) {
                continue;
            }

            const priceElements = document.querySelectorAll(
                this.options.bundleItemPriceSelectorTemplate.replace('%connectionId%', connectionId)
            );

            if (priceElements.length === 0) {
                continue;
            }

            const unitPrice = price.listPrice?.price || price.unitPrice;
            const qty = this._getQuantity(connectionId);
            const itemPrice = unitPrice * qty;

            if (qty !== null && qty > 0 && product?.available) {
                total += itemPrice;
            }

            for (const priceEl of priceElements) {
                priceEl.innerHTML = this._replacePrice(
                    priceEl.innerHTML,
                    itemPrice
                )
            }
        }

        const totalPriceElements = document.querySelectorAll('[data-price-total]');

        if (totalPriceElements.length === 0) {
            return;
        }
        
        for (const totalPriceEl of totalPriceElements) {
            totalPriceEl.innerHTML = this._replacePrice(
                totalPriceEl.innerHTML,
                total
            )
        }
    }

    _replacePrice(priceContent, newPrice) {
        // Replace everything after the price symbol
        return priceContent.replace(/\d.+[^\*]/, this._formatPrice(newPrice));
    }

    _getQuantity(connectionId) {
        return parseInt(this._getQuantityInput(connectionId).value);
    }

    _getQuantityInput(connectionId) {
        return document.getElementById(`zeobv-bundle-item-quantity-${connectionId}`);
    }

    _formatPrice(price) {
        function round(num) {
            return +(Math.round(num + 'e+2') + 'e-2');
        }

        const numberFormat = new Intl.NumberFormat(
            document.documentElement.lang || 'de-DE',
            {
                style: 'decimal',
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            }
        );

        const formattedPrice = numberFormat.format(round(price))

        return formattedPrice
    }
}
