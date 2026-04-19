@props([
    'product',
    'quantity' => 1,
    'label' => 'Add to Cart',
    'buttonClass' => 'btn btn-success',
])

<div class="d-flex align-items-center gap-2">
    <input type="number"
        min="1"
        value="{{ $quantity }}"
        class="form-control"
        style="max-width: 90px;"
        data-product-quantity="{{ $product->id }}">

    <button type="button"
        class="{{ $buttonClass }}"
        data-add-to-cart
        data-product-id="{{ $product->id }}"
        data-quantity-selector="[data-product-quantity='{{ $product->id }}']">
        {{ $label }}
    </button>
</div>
