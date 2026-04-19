@extends('layouts.admin')

@php
    $statusLabels = [
        'pending' => 'Pending',
        'confirmed' => 'Confirmed',
        'processing' => 'Processing',
        'ready' => 'Ready',
        'in_review' => 'In Review',
        'in_transit' => 'In Transit',
        'delivered' => 'Delivered',
        'delivery_in_review' => 'Delivery Review',
        'on_hold' => 'On Hold',
        'cancelled' => 'Cancelled',
        'returned' => 'Returned',
        'deleted' => 'Deleted',
    ];
    $statusBadgeClasses = [
        'pending' => 'bg-warning text-dark',
        'confirmed' => 'bg-info text-dark',
        'processing' => 'bg-primary',
        'ready' => 'bg-secondary',
        'in_review' => 'bg-dark',
        'in_transit' => 'bg-primary',
        'delivered' => 'bg-success',
        'delivery_in_review' => 'bg-dark',
        'on_hold' => 'bg-secondary',
        'cancelled' => 'bg-danger',
        'returned' => 'bg-secondary',
        'deleted' => 'bg-danger',
    ];
    $paymentBadgeClasses = [
        'unpaid' => 'bg-secondary',
        'pending' => 'bg-warning text-dark',
        'paid' => 'bg-success',
        'failed' => 'bg-danger',
    ];
    $currentStatus = $order->status ?: 'pending';
    $currentPaymentStatus = $order->payment_status ?: 'unpaid';
@endphp

@push('styles')
    <style>
        .detail-card {
            border: 1px solid rgba(0, 0, 0, 0.08);
            border-radius: 12px;
            padding: 20px;
            height: 100%;
            background: #fff;
        }

        .detail-card p:last-child {
            margin-bottom: 0;
        }

        .detail-title {
            font-weight: 700;
            margin-bottom: 12px;
        }

        .detail-grid {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        }

        .detail-label {
            color: #6c757d;
            font-size: 12px;
            margin-bottom: 4px;
        }

        .detail-value {
            font-weight: 600;
        }

        .product-thumb {
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
                    <h3>Order Details</h3>
                    <div class="text-tiny">Order {{ $order->order_number_label }}</div>
                </div>
                <div class="d-flex gap-2">
                    <a class="btn btn-outline-secondary" href="{{ route('admin.orders') }}">Back To Orders</a>
                    @if ($order->status !== 'deleted')
                        <a class="btn btn-outline-danger" href="{{ route('admin.orders.delete.soft', $order->id) }}">Move To Deleted</a>
                    @endif
                </div>
            </div>

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="detail-grid mb-4">
                <div class="detail-card">
                    <div class="detail-title">Order Summary</div>
                    <p><span class="detail-label d-block">Order Number</span><span class="detail-value">{{ $order->order_number_label }}</span></p>
                    <p><span class="detail-label d-block">Internal ID</span><span class="detail-value">{{ $order->id }}</span></p>
                    <p>
                        <span class="detail-label d-block">Order Status</span>
                        <span class="badge {{ $statusBadgeClasses[$currentStatus] ?? 'bg-secondary' }}">
                            {{ $statusLabels[$currentStatus] ?? ucfirst(str_replace('_', ' ', $currentStatus)) }}
                        </span>
                    </p>
                    <p><span class="detail-label d-block">Placed On</span><span class="detail-value">{{ optional($order->created_at)->format('d M Y h:i A') }}</span></p>
                    <p><span class="detail-label d-block">Items</span><span class="detail-value">{{ $orderItems->count() }}</span></p>
                    @if ($order->consignment_id)
                        <p><span class="detail-label d-block">Consignment ID</span><span class="detail-value">{{ $order->consignment_id }}</span></p>
                    @endif
                    @if ($order->delivery_date)
                        <p><span class="detail-label d-block">Delivered On</span><span class="detail-value">{{ \Carbon\Carbon::parse($order->delivery_date)->format('d M Y h:i A') }}</span></p>
                    @endif
                    @if ($order->cancelled_date)
                        <p><span class="detail-label d-block">Cancelled On</span><span class="detail-value">{{ \Carbon\Carbon::parse($order->cancelled_date)->format('d M Y h:i A') }}</span></p>
                    @endif
                </div>

                <div class="detail-card">
                    <div class="detail-title">Payment Details</div>
                    <p><span class="detail-label d-block">Payment Method</span><span class="detail-value text-capitalize">{{ $order->payment_method ?: 'cod' }}</span></p>
                    <p>
                        <span class="detail-label d-block">Payment Status</span>
                        <span class="badge {{ $paymentBadgeClasses[$currentPaymentStatus] ?? 'bg-secondary' }}">
                            {{ ucfirst($currentPaymentStatus) }}
                        </span>
                    </p>
                    <p><span class="detail-label d-block">Transaction ID</span><span class="detail-value">{{ $order->transaction_id ?: 'Not provided' }}</span></p>
                    <p><span class="detail-label d-block">Subtotal</span><span class="detail-value">Tk {{ number_format((float) $order->subtotal, 2) }}</span></p>
                    <p><span class="detail-label d-block">Delivery Fee</span><span class="detail-value">Tk {{ number_format((float) $order->fee, 2) }}</span></p>
                    <p><span class="detail-label d-block">Discount</span><span class="detail-value">Tk {{ number_format((float) $order->discount, 2) }}</span></p>
                    <p><span class="detail-label d-block">Total</span><span class="detail-value">Tk {{ number_format((float) $order->total, 2) }}</span></p>
                    @if (($order->payment_method ?: 'cod') === 'cod')
                        <p class="mb-0 mt-3 text-muted">Customer pays on delivery.</p>
                    @elseif (($order->payment_method ?: null) === 'bkash')
                        <p class="mb-0 mt-3 text-muted">Manual bKash payment requires transaction verification before confirmation.</p>
                    @endif
                </div>

                <div class="detail-card">
                    <div class="detail-title">Customer & Delivery</div>
                    <p><span class="detail-label d-block">Customer Name</span><span class="detail-value">{{ $order->full_name ?? $order->name }}</span></p>
                    <p><span class="detail-label d-block">Phone</span><span class="detail-value">{{ $order->phone }}</span></p>
                    <p><span class="detail-label d-block">Email</span><span class="detail-value">{{ $order->email ?: 'N/A' }}</span></p>
                    <p><span class="detail-label d-block">City</span><span class="detail-value">{{ $order->city ?: 'N/A' }}</span></p>
                    <p><span class="detail-label d-block">Area</span><span class="detail-value">{{ $order->area ?: 'N/A' }}</span></p>
                    @if ($order->delivery_area)
                        <p><span class="detail-label d-block">Delivery Area</span><span class="detail-value">{{ $order->delivery_area->name }} (Tk {{ number_format((float) $order->delivery_area->charge, 2) }})</span></p>
                    @endif
                    <p><span class="detail-label d-block">Address</span><span class="detail-value">{{ $order->address }}</span></p>
                    <p><span class="detail-label d-block">Note</span><span class="detail-value">{{ $order->note ?: 'No note added.' }}</span></p>
                </div>
            </div>

            <div class="wg-box mb-4">
                <div class="flex items-center justify-between gap10 flex-wrap mb-3">
                    <h5 class="mb-0">Ordered Items</h5>
                    <div class="text-tiny">Snapshot data is shown even if a product is later changed or removed.</div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Unit Price</th>
                                <th>Quantity</th>
                                <th>Line Total</th>
                                <th>Options</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orderItems as $item)
                                @php
                                    $productImage = $item->product_image ?: null;
                                    if ($productImage) {
                                        if (\Illuminate\Support\Str::startsWith($productImage, ['http://', 'https://'])) {
                                            $productImage = $productImage;
                                        } else {
                                            $productImage = asset(ltrim($productImage, '/'));
                                        }
                                    } else {
                                        $productImage = $item->product?->featured_image;
                                    }

                                    $optionText = is_array($item->options)
                                        ? collect($item->options)
                                            ->filter()
                                            ->map(fn ($value, $key) => ucfirst($key) . ': ' . $value)
                                            ->implode(', ')
                                        : ($item->options ?: 'N/A');
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <img class="product-thumb"
                                                src="{{ $productImage ?: asset('frontend/img/logo-transparent.png') }}"
                                                alt="{{ $item->product_name ?? $item->product?->name ?? 'Product' }}">
                                            <div>
                                                <div class="body-title-2">{{ $item->product_name ?? $item->product?->name ?? 'Deleted product' }}</div>
                                                @if ($item->product?->sku)
                                                    <div class="text-tiny">SKU: {{ $item->product->sku }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>Tk {{ number_format((float) ($item->unit_price ?? $item->price), 2) }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>Tk {{ number_format((float) ($item->line_total ?? (($item->unit_price ?? $item->price) * $item->quantity)), 2) }}</td>
                                    <td>{{ $optionText }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">No order items found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="detail-card">
                        <div class="detail-title">Update Order Status</div>
                        <form action="{{ route('admin.orders.update', $order->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="order_id" value="{{ $order->id }}">

                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-control" required>
                                    @foreach ($statusLabels as $status => $label)
                                        <option value="{{ $status }}" @selected($currentStatus === $status)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary">Update Status</button>
                        </form>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="detail-card">
                        <div class="detail-title">Update Customer & Payment Info</div>
                        <form action="{{ route('admin.orders.update.details', $order->id) }}" method="POST" id="orderDetailForm">
                            @csrf
                            @method('PUT')

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Customer Name</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name', $order->full_name ?? $order->name) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone</label>
                                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $order->phone) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email', $order->email) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">City</label>
                                    <input type="text" name="city" class="form-control" value="{{ old('city', $order->city) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Area</label>
                                    <input type="text" name="area" class="form-control" value="{{ old('area', $order->area) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Payment Method</label>
                                    <select name="payment_method" id="payment_method" class="form-control" required>
                                        <option value="cod" @selected(old('payment_method', $order->payment_method ?: 'cod') === 'cod')>Cash on Delivery</option>
                                        <option value="bkash" @selected(old('payment_method', $order->payment_method) === 'bkash')>bKash</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Payment Status</label>
                                    <select name="payment_status" class="form-control" required>
                                        <option value="unpaid" @selected(old('payment_status', $currentPaymentStatus) === 'unpaid')>Unpaid</option>
                                        <option value="pending" @selected(old('payment_status', $currentPaymentStatus) === 'pending')>Pending</option>
                                        <option value="paid" @selected(old('payment_status', $currentPaymentStatus) === 'paid')>Paid</option>
                                        <option value="failed" @selected(old('payment_status', $currentPaymentStatus) === 'failed')>Failed</option>
                                    </select>
                                </div>
                                <div class="col-md-6" id="transaction_id_wrapper">
                                    <label class="form-label">Transaction ID</label>
                                    <input type="text" name="transaction_id" id="transaction_id" class="form-control"
                                        value="{{ old('transaction_id', $order->transaction_id) }}">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Address</label>
                                    <textarea name="address" class="form-control" rows="3" required>{{ old('address', $order->address) }}</textarea>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Note</label>
                                    <textarea name="note" class="form-control" rows="3">{{ old('note', $order->note) }}</textarea>
                                </div>
                            </div>

                            <div class="mt-3">
                                <button type="submit" class="btn btn-success">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="wg-box mt-4">
                <h5 class="mb-3">Extra Data</h5>
                <div class="detail-grid">
                    <div>
                        <div class="detail-label">Linked User</div>
                        <div class="detail-value">{{ $order->user?->name ?: 'Guest checkout' }}</div>
                    </div>
                    <div>
                        <div class="detail-label">Device ID</div>
                        <div class="detail-value">{{ $order->device_id ?: 'N/A' }}</div>
                    </div>
                    <div>
                        <div class="detail-label">Session ID</div>
                        <div class="detail-value">{{ $order->session_id ?: 'N/A' }}</div>
                    </div>
                    <div>
                        <div class="detail-label">IP Address</div>
                        <div class="detail-value">{{ $order->ip_address ?: 'N/A' }}</div>
                    </div>
                    <div style="grid-column: 1 / -1;">
                        <div class="detail-label">User Agent</div>
                        <div class="detail-value" style="word-break: break-word;">{{ $order->user_agent ?: ($order->device?->user_agent ?: 'N/A') }}</div>
                    </div>
                    @if (!empty($order->json_data))
                        <div style="grid-column: 1 / -1;">
                            <div class="detail-label">JSON Data</div>
                            <pre class="mb-0" style="white-space: pre-wrap;">{{ json_encode($order->json_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (() => {
            const paymentMethodField = document.getElementById('payment_method');
            const transactionWrapper = document.getElementById('transaction_id_wrapper');
            const transactionInput = document.getElementById('transaction_id');

            const syncBkashField = () => {
                const needsTransaction = paymentMethodField && paymentMethodField.value === 'bkash';

                if (transactionWrapper) {
                    transactionWrapper.style.display = needsTransaction ? 'block' : 'none';
                }

                if (transactionInput) {
                    transactionInput.required = needsTransaction;

                    if (!needsTransaction) {
                        transactionInput.value = '';
                    }
                }
            };

            if (paymentMethodField) {
                paymentMethodField.addEventListener('change', syncBkashField);
                syncBkashField();
            }
        })();
    </script>
@endpush
