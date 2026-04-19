@extends('layouts.admin')

@push('styles')
    <style>
        .cart-stat-card {
            border: 1px solid rgba(0, 0, 0, 0.08);
            border-radius: 12px;
            padding: 18px;
            background: #fff;
            height: 100%;
        }

        .cart-stat-card .value {
            font-size: 28px;
            font-weight: 700;
            line-height: 1.1;
        }

        .cart-meta {
            color: #6c757d;
            font-size: 12px;
        }
    </style>
@endpush

@section('content')
    <div class="main-content-inner">
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <div>
                    <h3>Carts</h3>
                    <div class="text-tiny">Inspect live customer carts without changing storefront behavior.</div>
                </div>
                <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                    <li>
                        <a href="{{ route('admin.index') }}">
                            <div class="text-tiny">Dashboard</div>
                        </a>
                    </li>
                    <li>
                        <i class="icon-chevron-right"></i>
                    </li>
                    <li>
                        <div class="text-tiny">Carts</div>
                    </li>
                </ul>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="cart-stat-card">
                        <div class="text-tiny mb-2">Total Carts</div>
                        <div class="value">{{ $cartCount }}</div>
                        <div class="cart-meta mt-2">All stored carts, including empty ones.</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="cart-stat-card">
                        <div class="text-tiny mb-2">Active Carts</div>
                        <div class="value">{{ $activeCartCount }}</div>
                        <div class="cart-meta mt-2">Only carts that currently contain one or more items.</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="cart-stat-card">
                        <div class="text-tiny mb-2">Current Search</div>
                        <div class="value" style="font-size: 22px;">{{ $search !== '' ? $search : 'All carts' }}</div>
                        <div class="cart-meta mt-2">Search by cart ID, session, user, device, IP, or product name.</div>
                    </div>
                </div>
            </div>

            <div class="wg-box mb-4">
                <form method="GET" action="{{ route('admin.carts') }}" class="d-flex gap-2">
                    <input type="text" name="search" class="form-control" value="{{ $search }}"
                        placeholder="Search carts by customer, session, device, product">
                    <button class="btn btn-primary" type="submit">Search</button>
                    @if ($search !== '')
                        <a class="btn btn-outline-secondary" href="{{ route('admin.carts') }}">Reset</a>
                    @endif
                </form>
            </div>

            <div class="wg-box">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>Cart</th>
                                <th>Owner</th>
                                <th>Device / Session</th>
                                <th>Items</th>
                                <th>Subtotal</th>
                                <th>Last Updated</th>
                                <th style="width: 120px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($carts as $cart)
                                <tr>
                                    <td>
                                        <div class="body-title-2">Cart #{{ $cart->id }}</div>
                                        <div class="cart-meta">Created {{ optional($cart->created_at)->diffForHumans() }}</div>
                                    </td>
                                    <td>
                                        @if ($cart->user)
                                            <div class="body-title-2">{{ $cart->user->name }}</div>
                                            <div>{{ $cart->user->email }}</div>
                                        @else
                                            <div class="body-title-2">Guest</div>
                                            <div class="cart-meta">User ID: {{ $cart->user_id ?: 'N/A' }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <div><strong>Session:</strong> {{ $cart->session_id ?: 'N/A' }}</div>
                                        <div><strong>Device:</strong> {{ $cart->device_id ?: 'N/A' }}</div>
                                        @if ($cart->device?->ip_address)
                                            <div class="cart-meta">IP: {{ $cart->device->ip_address }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="body-title-2">{{ $cart->items_count }} row(s)</div>
                                        <div class="cart-meta">{{ $cart->totalItems() }} total quantity</div>
                                    </td>
                                    <td>Tk {{ number_format($cart->subtotal(), 2) }}</td>
                                    <td>
                                        <div>{{ optional($cart->updated_at)->format('d M Y') }}</div>
                                        <div class="cart-meta">{{ optional($cart->updated_at)->format('h:i A') }}</div>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.carts.show', $cart) }}" class="btn btn-outline-primary btn-sm">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">No carts found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="divider"></div>
                <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                    {{ $carts->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
@endsection
