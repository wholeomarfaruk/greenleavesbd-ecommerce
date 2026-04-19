<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h6 class="mb-1">Your cart</h6>
        <small class="text-muted">Items: <span data-cart-total-items>0</span></small>
    </div>
    <button type="button" class="btn btn-sm btn-outline-danger" data-clear-cart>Clear</button>
</div>

<div data-mini-cart-items class="d-grid gap-3">
    <div class="text-center text-muted py-5 border rounded-3 bg-light">
        Your cart is empty.
    </div>
</div>

<div class="cart-summary mt-4 border-top pt-3">
    <div class="d-flex justify-content-between mb-2">
        <span>Subtotal</span>
        <strong data-cart-subtotal>৳0.00</strong>
    </div>
    <div class="d-flex justify-content-between mb-3">
        <span>Total items</span>
        <strong data-cart-total-items>0</strong>
    </div>
    <div class="d-grid gap-2">
        <a href="{{ route('cart.view') }}" class="btn btn-outline-primary">View Cart</a>
        <a href="{{ route('cart.checkout') }}" class="btn btn-danger">Checkout</a>
    </div>
</div>
