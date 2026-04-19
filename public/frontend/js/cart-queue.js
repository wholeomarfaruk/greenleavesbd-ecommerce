(function () {
    class CartQueue {
        constructor(config) {
            this.config = config || {};
            this.queue = Promise.resolve();
            this.payload = null;
        }

        bootstrap() {
            this.bindEvents();
            this.loadCart(false);
        }

        enqueue(task) {
            const run = this.queue.then(() => task());
            this.queue = run.catch(() => undefined);

            return run;
        }

        headers() {
            return {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.config.csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            };
        }

        async request(url, options = {}) {
            const response = await fetch(url, {
                method: options.method || 'GET',
                headers: options.headers || this.headers(),
                credentials: 'same-origin',
                body: options.body ? JSON.stringify(options.body) : undefined,
            });

            const text = await response.text();
            let payload = {};

            try {
                payload = text ? JSON.parse(text) : {};
            } catch (error) {
                payload = {};
            }

            if (!response.ok || payload.status === false) {
                const validationMessage = payload.errors
                    ? Object.values(payload.errors).flat()[0]
                    : null;

                throw new Error(payload.message || validationMessage || 'Something went wrong.');
            }

            return payload;
        }

        loadCart(showErrors = false) {
            return this.request(this.config.routes.items, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            })
                .then((payload) => {
                    this.render(payload);

                    return payload;
                })
                .catch((error) => {
                    if (showErrors) {
                        this.notifyError(error.message);
                    }
                });
        }

        addToCart(productId, quantity = 1) {
            return this.enqueue(async () => {
                const payload = await this.request(this.config.routes.add, {
                    method: 'POST',
                    body: {
                        product_id: productId,
                        quantity: this.normalizeQuantity(quantity),
                    },
                });

                this.render(payload);
                this.notifySuccess(payload.message || 'Product added to cart.');

                return payload;
            }).catch((error) => {
                this.notifyError(error.message);
                throw error;
            });
        }

        updateQuantity(cartItemId, quantity) {
            return this.enqueue(async () => {
                const payload = await this.request(
                    this.config.routes.updateTemplate.replace('__CART_ITEM__', cartItemId),
                    {
                        method: 'PATCH',
                        body: {
                            quantity: this.normalizeQuantity(quantity),
                        },
                    }
                );

                this.render(payload);
                this.notifySuccess(payload.message || 'Cart updated.');

                return payload;
            }).catch((error) => {
                this.notifyError(error.message);
                throw error;
            });
        }

        removeFromCart(cartItemId) {
            return this.enqueue(async () => {
                const payload = await this.request(
                    this.config.routes.destroyTemplate.replace('__CART_ITEM__', cartItemId),
                    {
                        method: 'DELETE',
                    }
                );

                this.render(payload);
                this.notifySuccess(payload.message || 'Item removed from cart.');

                return payload;
            }).catch((error) => {
                this.notifyError(error.message);
                throw error;
            });
        }

        clearCart() {
            return this.enqueue(async () => {
                const payload = await this.request(this.config.routes.clear, {
                    method: 'DELETE',
                });

                this.render(payload);
                this.notifySuccess(payload.message || 'Cart cleared.');

                return payload;
            }).catch((error) => {
                this.notifyError(error.message);
                throw error;
            });
        }

        bindEvents() {
            document.addEventListener('click', (event) => {
                const addButton = event.target.closest('[data-add-to-cart]');
                const removeButton = event.target.closest('[data-remove-cart-item]');
                const clearButton = event.target.closest('[data-clear-cart]');
                const stepButton = event.target.closest('[data-cart-qty-step]');

                if (addButton) {
                    event.preventDefault();
                    const productId = Number(addButton.dataset.productId);
                    const quantity = this.resolveAddQuantity(addButton);
                    this.addToCart(productId, quantity);
                    return;
                }

                if (removeButton) {
                    event.preventDefault();
                    this.removeFromCart(Number(removeButton.dataset.cartItemId));
                    return;
                }

                if (clearButton) {
                    event.preventDefault();
                    this.clearCart();
                    return;
                }

                if (stepButton) {
                    event.preventDefault();
                    const cartItemId = Number(stepButton.dataset.cartItemId);
                    const quantityInput = document.querySelector(
                        `[data-cart-quantity][data-cart-item-id="${cartItemId}"]`
                    );

                    if (!quantityInput) {
                        return;
                    }

                    const currentQuantity = this.normalizeQuantity(quantityInput.value);
                    const nextQuantity = stepButton.dataset.cartQtyStep === 'increase'
                        ? currentQuantity + 1
                        : Math.max(1, currentQuantity - 1);

                    quantityInput.value = nextQuantity;
                    this.updateQuantity(cartItemId, nextQuantity);
                }
            });

            document.addEventListener('change', (event) => {
                const quantityInput = event.target.closest('[data-cart-quantity]');

                if (!quantityInput) {
                    return;
                }

                const cartItemId = Number(quantityInput.dataset.cartItemId);
                this.updateQuantity(cartItemId, quantityInput.value);
            });
        }

        render(payload) {
            this.payload = payload;
            this.renderCounts(payload);
            this.renderMiniCart(payload);
            this.renderCartPage(payload);
            this.renderCheckoutSummary(payload);
        }

        renderCounts(payload) {
            document.querySelectorAll('[data-cart-count-badge]').forEach((element) => {
                element.textContent = payload.cart_count || 0;
            });

            document.querySelectorAll('[data-cart-total-items]').forEach((element) => {
                element.textContent = payload.total_items || 0;
            });

            document.querySelectorAll('[data-cart-subtotal]').forEach((element) => {
                element.textContent = this.formatMoney(payload.subtotal || 0);
            });
        }

        renderMiniCart(payload) {
            const container = document.querySelector('[data-mini-cart-items]');

            if (!container) {
                return;
            }

            if (!payload.items || payload.items.length === 0) {
                container.innerHTML = `
                    <div class="text-center text-muted py-5 border rounded-3 bg-light">
                        Your cart is empty.
                    </div>
                `;
                return;
            }

            container.innerHTML = payload.items.map((item) => this.buildMiniCartItem(item)).join('');
        }

        renderCartPage(payload) {
            const container = document.querySelector('[data-cart-page-items]');

            if (!container) {
                return;
            }

            if (!payload.items || payload.items.length === 0) {
                container.innerHTML = `
                    <div class="text-center text-muted py-5 border rounded-3 bg-light">
                        Your cart is empty.
                    </div>
                `;
            } else {
                container.innerHTML = payload.items.map((item) => this.buildCartRow(item)).join('');
            }

            document.querySelectorAll('[data-cart-summary-subtotal]').forEach((element) => {
                element.textContent = this.formatMoney(payload.subtotal || 0);
            });

            document.querySelectorAll('[data-cart-summary-count]').forEach((element) => {
                element.textContent = payload.total_items || 0;
            });

            document.querySelectorAll('[data-cart-page-checkout]').forEach((element) => {
                if (payload.items && payload.items.length > 0) {
                    element.classList.remove('disabled');
                    element.removeAttribute('aria-disabled');
                } else {
                    element.classList.add('disabled');
                    element.setAttribute('aria-disabled', 'true');
                }
            });
        }

        renderCheckoutSummary(payload) {
            const container = document.querySelector('[data-checkout-items]');

            if (!container) {
                return;
            }

            if (!payload.items || payload.items.length === 0) {
                container.innerHTML = `
                    <div class="text-center text-muted py-4 border rounded-3 bg-light">
                        Your cart is empty.
                    </div>
                `;
            } else {
                container.innerHTML = payload.items.map((item) => `
                    <div class="d-flex gap-3 py-3 border-bottom">
                        <img src="${item.featured_image}"
                            alt="${this.escapeHtml(item.name)}"
                            width="72"
                            height="72"
                            class="rounded-3 object-fit-cover">
                        <div class="flex-grow-1">
                            <h3 class="h6 mb-1">${this.escapeHtml(item.name)}</h3>
                            <p class="text-muted small mb-1">Qty: ${item.quantity}</p>
                            <div class="d-flex justify-content-between">
                                <span class="small text-muted">Unit: ${this.formatMoney(item.unit_price)}</span>
                                <strong>${this.formatMoney(item.line_total)}</strong>
                            </div>
                        </div>
                    </div>
                `).join('');
            }

            document.querySelectorAll('[data-checkout-subtotal], [data-checkout-total]').forEach((element) => {
                element.textContent = this.formatMoney(payload.subtotal || 0);
            });

            const placeOrderButton = document.querySelector('[data-place-order-button]');
            if (placeOrderButton) {
                placeOrderButton.disabled = !payload.items || payload.items.length === 0;
            }
        }

        buildMiniCartItem(item) {
            return `
                <div class="d-flex gap-3 border rounded-3 p-3">
                    <img src="${item.featured_image}"
                        alt="${this.escapeHtml(item.name)}"
                        width="64"
                        height="64"
                        class="rounded-3 object-fit-cover">
                    <div class="flex-grow-1">
                        <h3 class="h6 mb-1">${this.escapeHtml(item.name)}</h3>
                        <p class="small text-muted mb-1">Qty: ${item.quantity}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <strong>${this.formatMoney(item.line_total)}</strong>
                            <button type="button"
                                class="btn btn-sm btn-outline-danger"
                                data-remove-cart-item
                                data-cart-item-id="${item.cart_item_id}">
                                Remove
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }

        buildCartRow(item) {
            const productLink = item.slug ? `/product/${item.slug}` : '#';

            return `
                <div class="border rounded-3 p-3 mb-3" data-cart-row="${item.cart_item_id}">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-2 col-4">
                            <img src="${item.featured_image}"
                                alt="${this.escapeHtml(item.name)}"
                                class="img-fluid rounded-3 w-100">
                        </div>
                        <div class="col-md-4 col-8">
                            <h3 class="h6 mb-1">${this.escapeHtml(item.name)}</h3>
                            <p class="text-muted mb-2">Unit price: ${this.formatMoney(item.unit_price)}</p>
                            ${item.slug ? `<a href="${productLink}" class="small">View product</a>` : ''}
                        </div>
                        <div class="col-md-3 col-12">
                            <label class="form-label small text-muted">Quantity</label>
                            <div class="input-group">
                                <button class="btn btn-outline-secondary"
                                    type="button"
                                    data-cart-qty-step="decrease"
                                    data-cart-item-id="${item.cart_item_id}">-</button>
                                <input type="number"
                                    min="1"
                                    value="${item.quantity}"
                                    class="form-control text-center"
                                    data-cart-quantity
                                    data-cart-item-id="${item.cart_item_id}">
                                <button class="btn btn-outline-secondary"
                                    type="button"
                                    data-cart-qty-step="increase"
                                    data-cart-item-id="${item.cart_item_id}">+</button>
                            </div>
                        </div>
                        <div class="col-md-3 col-12 text-md-end">
                            <p class="fw-semibold mb-2">${this.formatMoney(item.line_total)}</p>
                            <button type="button"
                                class="btn btn-outline-danger btn-sm"
                                data-remove-cart-item
                                data-cart-item-id="${item.cart_item_id}">
                                Remove
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }

        resolveAddQuantity(button) {
            const selector = button.dataset.quantitySelector;

            if (selector) {
                const quantityField = document.querySelector(selector);
                if (quantityField) {
                    return quantityField.value;
                }
            }

            const nearbyField = button.parentElement?.querySelector('[data-product-quantity]');
            return nearbyField ? nearbyField.value : 1;
        }

        normalizeQuantity(value) {
            const parsedValue = Number.parseInt(value, 10);
            return Number.isNaN(parsedValue) || parsedValue < 1 ? 1 : parsedValue;
        }

        formatMoney(value) {
            return `${this.config.currencySymbol}${Number(value || 0).toFixed(2)}`;
        }

        escapeHtml(value) {
            return String(value || '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        notifySuccess(message) {
            if (window.appNotyf) {
                window.appNotyf.success(message);
            }
        }

        notifyError(message) {
            if (window.appNotyf) {
                window.appNotyf.error(message);
            }
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        window.cartQueue = new CartQueue(window.cartConfig || {});
        window.cartQueue.bootstrap();
    });
})();
