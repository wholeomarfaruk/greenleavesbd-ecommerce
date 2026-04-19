@extends('layouts.admin')

@push('styles')
    <style>
        .cart-detail-card {
            border: 1px solid rgba(0, 0, 0, 0.08);
            border-radius: 12px;
            padding: 20px;
            background: #fff;
            height: 100%;
        }

        .cart-detail-grid {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        }

        .cart-detail-label {
            color: #6c757d;
            font-size: 12px;
            margin-bottom: 4px;
        }

        .cart-detail-value {
            font-weight: 600;
        }

        .cart-item-thumb {
            width: 56px;
            height: 56px;
            border-radius: 10px;
            object-fit: cover;
            background: #f5f5f5;
        }
    </style>
@endpush

@section('content')
    <div class="main-content-inner">
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <div>
                    <h3>Cart Details</h3>
                    <div class="text-tiny">Cart #{{ $cart->id }}</div>
                </div>
                <a class="btn btn-outline-secondary" href="{{ route('admin.carts') }}">Back To Carts</a>
            </div>

            <div class="cart-detail-grid mb-4">
                <div class="cart-detail-card">
                    <div class="body-title-2 mb-3">Cart Summary</div>
                    <p><span class="cart-detail-label d-block">Cart ID</span><span class="cart-detail-value">{{ $cart->id }}</span></p>
                    <p><span class="cart-detail-label d-block">Item Rows</span><span class="cart-detail-value">{{ $cart->items->count() }}</span></p>
                    <p><span class="cart-detail-label d-block">Total Quantity</span><span class="cart-detail-value">{{ $cart->totalItems() }}</span></p>
                    <p><span class="cart-detail-label d-block">Subtotal</span><span class="cart-detail-value">Tk {{ number_format($cart->subtotal(), 2) }}</span></p>
                    <p><span class="cart-detail-label d-block">Created</span><span class="cart-detail-value">{{ optional($cart->created_at)->format('d M Y h:i A') }}</span></p>
                    <p><span class="cart-detail-label d-block">Last Updated</span><span class="cart-detail-value">{{ optional($cart->updated_at)->format('d M Y h:i A') }}</span></p>
                </div>

                <div class="cart-detail-card">
                    <div class="body-title-2 mb-3">Customer</div>
                    @if ($cart->user)
                        <p><span class="cart-detail-label d-block">User</span><span class="cart-detail-value">{{ $cart->user->name }}</span></p>
                        <p><span class="cart-detail-label d-block">Email</span><span class="cart-detail-value">{{ $cart->user->email }}</span></p>
                        <p><span class="cart-detail-label d-block">User ID</span><span class="cart-detail-value">{{ $cart->user->id }}</span></p>
                    @else
                        <p><span class="cart-detail-label d-block">User</span><span class="cart-detail-value">Guest</span></p>
                    @endif

                    <p><span class="cart-detail-label d-block">Session ID</span><span class="cart-detail-value">{{ $cart->session_id ?: 'N/A' }}</span></p>
                </div>

                <div class="cart-detail-card">
                    <div class="body-title-2 mb-3">Device</div>
                    <p><span class="cart-detail-label d-block">Device ID</span><span class="cart-detail-value">{{ $cart->device_id ?: 'N/A' }}</span></p>
                    <p><span class="cart-detail-label d-block">IP Address</span><span class="cart-detail-value">{{ $cart->device?->ip_address ?: 'N/A' }}</span></p>
                    <p><span class="cart-detail-label d-block">Last Seen</span><span class="cart-detail-value">{{ $cart->device?->last_seen ?: 'N/A' }}</span></p>
                    <p><span class="cart-detail-label d-block">User Agent</span><span class="cart-detail-value" style="word-break: break-word;">{{ $cart->device?->user_agent ?: 'N/A' }}</span></p>
                </div>
            </div>

            <div class="wg-box">
                <div class="flex items-center justify-between gap10 flex-wrap mb-3">
                    <h5 class="mb-0">Cart Items</h5>
                    <div class="text-tiny">{{ $cart->items->count() ? 'Live cart snapshot' : 'This cart is currently empty.' }}</div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Unit Price</th>
                                <th>Quantity</th>
                                <th>Line Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($cart->items as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <img class="cart-item-thumb"
                                                src="{{ $item->product?->featured_image ?: asset('frontend/img/logo-transparent.png') }}"
                                                alt="{{ $item->product?->name ?: 'Product' }}">
                                            <div>
                                                <div class="body-title-2">{{ $item->product?->name ?: 'Deleted product' }}</div>
                                                @if ($item->product?->sku)
                                                    <div class="text-tiny">SKU: {{ $item->product->sku }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>Tk {{ number_format((float) $item->price, 2) }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>Tk {{ number_format($item->lineTotal(), 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">No items in this cart.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
