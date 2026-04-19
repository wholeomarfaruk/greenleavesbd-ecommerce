@extends('layouts.app')

@section('content')
    <section class="py-5">
        <div class="container">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                <div>
                    <h1 class="h3 mb-1">Shopping Cart</h1>
                    <p class="text-muted mb-0">Review your selected products before checkout.</p>
                </div>
                <a href="{{ route('shop') }}" class="btn btn-outline-secondary">Continue Shopping</a>
            </div>

            @if (session('cart_error'))
                <div class="alert alert-warning">{{ session('cart_error') }}</div>
            @endif

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h2 class="h5 mb-0">Cart Items</h2>
                                <button type="button" class="btn btn-sm btn-outline-danger" data-clear-cart>Clear cart</button>
                            </div>

                            <div data-cart-page-items>
                                @forelse ($cartData['items'] as $item)
                                    <div class="border rounded-3 p-3 mb-3" data-cart-row="{{ $item['cart_item_id'] }}">
                                        <div class="row g-3 align-items-center">
                                            <div class="col-md-2 col-4">
                                                <img src="{{ $item['featured_image'] }}"
                                                    alt="{{ $item['name'] }}"
                                                    class="img-fluid rounded-3 w-100">
                                            </div>
                                            <div class="col-md-4 col-8">
                                                <h3 class="h6 mb-1">{{ $item['name'] }}</h3>
                                                <p class="text-muted mb-2">Unit price: ৳{{ number_format($item['unit_price'], 2) }}</p>
                                                @if ($item['slug'])
                                                    <a href="{{ route('product.show', $item['slug']) }}" class="small">View product</a>
                                                @endif
                                            </div>
                                            <div class="col-md-3 col-12">
                                                <label class="form-label small text-muted">Quantity</label>
                                                <div class="input-group">
                                                    <button class="btn btn-outline-secondary"
                                                        type="button"
                                                        data-cart-qty-step="decrease"
                                                        data-cart-item-id="{{ $item['cart_item_id'] }}">-</button>
                                                    <input type="number"
                                                        min="1"
                                                        value="{{ $item['quantity'] }}"
                                                        class="form-control text-center"
                                                        data-cart-quantity
                                                        data-cart-item-id="{{ $item['cart_item_id'] }}">
                                                    <button class="btn btn-outline-secondary"
                                                        type="button"
                                                        data-cart-qty-step="increase"
                                                        data-cart-item-id="{{ $item['cart_item_id'] }}">+</button>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-12 text-md-end">
                                                <p class="fw-semibold mb-2">৳{{ number_format($item['line_total'], 2) }}</p>
                                                <button type="button"
                                                    class="btn btn-outline-danger btn-sm"
                                                    data-remove-cart-item
                                                    data-cart-item-id="{{ $item['cart_item_id'] }}">
                                                    Remove
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center text-muted py-5 border rounded-3 bg-light">
                                        Your cart is empty.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow-sm border-0 sticky-top" style="top: 2rem;">
                        <div class="card-body">
                            <h2 class="h5 mb-3">Order Summary</h2>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Total items</span>
                                <strong data-cart-summary-count>{{ $cartData['total_items'] }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-4">
                                <span>Subtotal</span>
                                <strong data-cart-summary-subtotal>৳{{ number_format($cartData['subtotal'], 2) }}</strong>
                            </div>

                            <a href="{{ route('cart.checkout') }}"
                                class="btn btn-primary w-100 {{ empty($cartData['items']) ? 'disabled' : '' }}"
                                data-cart-page-checkout>
                                Proceed to Checkout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
