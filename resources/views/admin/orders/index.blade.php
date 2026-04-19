@extends('layouts.admin')

@php
    $statusRoutes = [
        'pending' => route('admin.orders.pending'),
        'confirmed' => route('admin.orders.confirmed'),
        'processing' => route('admin.orders.processing'),
        'ready' => route('admin.orders.ready'),
        'in_review' => route('admin.orders.in_review'),
        'in_transit' => route('admin.orders.in_transit'),
        'delivered' => route('admin.orders.delivered'),
        'delivery_in_review' => route('admin.orders.delivery_in_review'),
        'on_hold' => route('admin.orders.on_hold'),
        'cancelled' => route('admin.orders.cancelled'),
        'returned' => route('admin.orders.returned'),
        'deleted' => route('admin.orders.deleted'),
    ];
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
    $currentStatus = $activeStatus !== '' ? $activeStatus : null;
@endphp

@push('styles')
    <style>
        .order-summary-card {
            border: 1px solid rgba(0, 0, 0, 0.08);
            border-radius: 12px;
            padding: 18px;
            height: 100%;
            background: #fff;
        }

        .order-summary-card .value {
            font-size: 28px;
            font-weight: 700;
            line-height: 1.1;
        }

        .order-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
        }

        .order-search-grid {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        }

        .order-tab-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .order-actions {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .order-meta {
            color: #6c757d;
            font-size: 12px;
        }

        .select-item {
            width: 18px;
            height: 18px;
        }
    </style>
@endpush

@section('content')
    <div class="main-content-inner">
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <div>
                    <h3>{{ $pageTitle }}</h3>
                    <div class="text-tiny">Live storefront ordering is unchanged. This is the new shared admin view.</div>
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
                        <div class="text-tiny">Orders</div>
                    </li>
                </ul>
            </div>

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="order-summary-card">
                        <div class="text-tiny mb-2">Active Orders</div>
                        <div class="value">{{ $orders_count }}</div>
                        <div class="order-meta mt-2">Excludes deleted and old autosave records.</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="order-summary-card">
                        <div class="text-tiny mb-2">Deleted Orders</div>
                        <div class="value">{{ $deleted_orders_count }}</div>
                        <div class="order-meta mt-2">Still available for review from the deleted orders tab.</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="order-summary-card">
                        <div class="text-tiny mb-2">Current Filter</div>
                        <div class="value" style="font-size: 22px;">
                            {{ $currentStatus ? ($statusLabels[$currentStatus] ?? ucfirst(str_replace('_', ' ', $currentStatus))) : 'All Orders' }}
                        </div>
                        <div class="order-meta mt-2">Search works across order number, customer, payment info, and product name.</div>
                    </div>
                </div>
            </div>

            <div class="wg-box mb-4">
                <div class="order-search-grid">
                    <form method="GET" action="{{ url()->current() }}">
                        <div class="mb-2 body-title-2">Search Orders</div>
                        <div class="d-flex gap-2">
                            <input type="text" name="search" value="{{ $search }}" class="form-control"
                                placeholder="Order no, phone, payment, product">
                            <button class="btn btn-primary" type="submit">Search</button>
                        </div>
                    </form>
                    <form method="GET" action="{{ route('admin.orders.export') }}">
                        <div class="mb-2 body-title-2">Export</div>
                        <div class="d-flex gap-2">
                            <select name="order_status" class="form-control">
                                <option value="">All Active Orders</option>
                                @foreach ($status_group as $group)
                                    <option value="{{ $group->status }}" @selected($currentStatus === $group->status)>
                                        {{ $statusLabels[$group->status] ?? ucfirst(str_replace('_', ' ', $group->status)) }}
                                    </option>
                                @endforeach
                                <option value="deleted" @selected($currentStatus === 'deleted')>Deleted Orders</option>
                            </select>
                            <button class="btn btn-outline-secondary" type="submit">Export</button>
                        </div>
                    </form>
                    <div class="d-flex align-items-end">
                        <a class="btn btn-success" href="{{ route('admin.orders.add') }}">
                            <i class="icon-plus"></i> Add New Order
                        </a>
                    </div>
                </div>
            </div>

            <div class="wg-box mb-4">
                <div class="order-tab-list">
                    <a class="tf-button style-1 {{ $currentStatus === null ? 'bg-primary text-white' : '' }}"
                        href="{{ route('admin.orders') }}">
                        All ({{ $orders_count }})
                    </a>

                    @foreach ($status_group as $group)
                        <a class="tf-button style-1 {{ $currentStatus === $group->status ? 'bg-primary text-white' : '' }}"
                            href="{{ $statusRoutes[$group->status] ?? route('admin.orders', ['order_status' => $group->status]) }}">
                            {{ $statusLabels[$group->status] ?? ucfirst(str_replace('_', ' ', $group->status)) }}
                            ({{ $group->count }})
                        </a>
                    @endforeach

                    <a class="tf-button style-1 {{ $currentStatus === 'deleted' ? 'bg-primary text-white' : '' }}"
                        href="{{ route('admin.orders.deleted') }}">
                        Deleted ({{ $deleted_orders_count }})
                    </a>
                </div>
            </div>

            <div class="wg-box mb-4">
                <form id="bulk-action-form">
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <button type="button" class="btn btn-outline-secondary" id="toggle-selection">Toggle Selection</button>
                        <button type="button" class="btn btn-outline-secondary" id="select-all">Select All Visible</button>
                        <select class="form-control" style="width: 220px;" name="status" id="bulk-action-status">
                            <option value="">Select Status</option>
                            @foreach ($statusLabels as $status => $label)
                                <option value="{{ $status }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <button id="bulk-action-button" type="submit" class="btn btn-primary">Update Status</button>
                    </div>
                </form>
            </div>

            <div class="wg-box">
                <div class="divider"></div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered align-middle">
                        <thead>
                            <tr>
                                <th style="width: 44px;"></th>
                                <th>Order</th>
                                <th>Customer</th>
                                <th>Items</th>
                                <th>Payment</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Placed</th>
                                <th style="width: 180px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orders as $order)
                                @php
                                    $status = $order->status ?: 'pending';
                                    $paymentStatus = $order->payment_status ?: 'unpaid';
                                    $itemPreview = $order->items
                                        ->take(2)
                                        ->map(function ($item) {
                                            $name = $item->product_name ?? $item->product?->name ?? 'Deleted product';
                                            return $name . ' x ' . $item->quantity;
                                        })
                                        ->implode(', ');
                                @endphp
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox" class="form-check-input select-item" name="ids[]"
                                            value="{{ $order->id }}">
                                    </td>
                                    <td>
                                        <div class="body-title-2">{{ $order->order_number_label }}</div>
                                        <div class="order-meta">Internal ID: {{ $order->id }}</div>
                                        @if ($order->consignment_id)
                                            <div class="order-meta">Consignment: {{ $order->consignment_id }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="body-title-2">{{ $order->full_name ?? $order->name }}</div>
                                        <div>{{ $order->phone }}</div>
                                        @if ($order->email)
                                            <div class="order-meta">{{ $order->email }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="body-title-2">{{ $order->items_count }} item(s)</div>
                                        @if ($itemPreview !== '')
                                            <div class="order-meta">{{ $itemPreview }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="mb-1 text-capitalize">{{ $order->payment_method ?: 'cod' }}</div>
                                        <span class="badge {{ $paymentBadgeClasses[$paymentStatus] ?? 'bg-secondary' }}">
                                            {{ ucfirst($paymentStatus) }}
                                        </span>
                                        @if ($order->transaction_id)
                                            <div class="order-meta mt-1">Txn: {{ $order->transaction_id }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="body-title-2">Tk {{ number_format((float) $order->total, 2) }}</div>
                                        <div class="order-meta">
                                            Subtotal {{ number_format((float) $order->subtotal, 2) }}
                                            @if ((float) $order->fee > 0)
                                                | Delivery {{ number_format((float) $order->fee, 2) }}
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $statusBadgeClasses[$status] ?? 'bg-secondary' }}">
                                            {{ $statusLabels[$status] ?? ucfirst(str_replace('_', ' ', $status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div>{{ optional($order->created_at)->format('d M Y') }}</div>
                                        <div class="order-meta">{{ optional($order->created_at)->format('h:i A') }}</div>
                                        @if ($order->delivery_date)
                                            <div class="order-meta mt-1">Delivered: {{ \Carbon\Carbon::parse($order->delivery_date)->format('d M Y') }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="order-actions">
                                            <a href="{{ route('admin.orders.details', $order->id) }}" title="View Order">
                                                <div class="list-icon-function view-icon">
                                                    <div class="item eye">
                                                        <i class="icon-eye"></i>
                                                    </div>
                                                </div>
                                            </a>

                                            @if ($status !== 'deleted')
                                                <a href="{{ route('admin.orders.delete.soft', $order->id) }}"
                                                    title="Move To Deleted">
                                                    <div class="list-icon-function">
                                                        <div class="item trash">
                                                            <i class="icon-trash"></i>
                                                        </div>
                                                    </div>
                                                </a>
                                            @endif

                                            @if (Route::has('admin.steadfast.place_order') && $status !== 'deleted')
                                                <a class="send-courier" data-id="{{ $order->id }}"
                                                    href="{{ route('admin.steadfast.place_order', $order->id) }}"
                                                    title="Send To Courier">
                                                    <div class="list-icon-function">
                                                        <div class="item send">
                                                            <i class="icon-send"></i>
                                                        </div>
                                                    </div>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">No orders found for this filter.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="divider"></div>
                <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                    {{ $orders->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (() => {
            const selectAllButton = document.getElementById('select-all');
            const toggleSelectionButton = document.getElementById('toggle-selection');
            const bulkForm = document.getElementById('bulk-action-form');
            const statusField = document.getElementById('bulk-action-status');

            if (selectAllButton) {
                selectAllButton.addEventListener('click', () => {
                    document.querySelectorAll('.select-item').forEach((checkbox) => {
                        checkbox.checked = true;
                    });
                });
            }

            if (toggleSelectionButton) {
                toggleSelectionButton.addEventListener('click', () => {
                    document.querySelectorAll('.select-item').forEach((checkbox) => {
                        checkbox.checked = !checkbox.checked;
                    });
                });
            }

            if (bulkForm) {
                bulkForm.addEventListener('submit', async (event) => {
                    event.preventDefault();

                    const ids = Array.from(document.querySelectorAll('.select-item:checked')).map((checkbox) => checkbox.value);
                    const status = statusField.value;

                    if (!ids.length) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'No orders selected',
                            text: 'Select at least one order before updating status.',
                        });
                        return;
                    }

                    if (!status) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'No status selected',
                            text: 'Choose a status for the selected orders.',
                        });
                        return;
                    }

                    const result = await Swal.fire({
                        title: 'Update selected orders?',
                        text: 'This will change the status of the selected orders.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, update',
                    });

                    if (!result.isConfirmed) {
                        return;
                    }

                    try {
                        const response = await fetch("{{ route('admin.orders.status.update.bulk') }}", {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                ids,
                                status,
                            }),
                        });

                        const data = await response.json();

                        if (!response.ok || !data.success) {
                            throw new Error(data.message || 'Unable to update order statuses.');
                        }

                        await Swal.fire({
                            icon: 'success',
                            title: data.message,
                            timer: 1500,
                            showConfirmButton: false,
                        });

                        window.location.reload();
                    } catch (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Update failed',
                            text: error.message,
                        });
                    }
                });
            }

            document.querySelectorAll('.send-courier').forEach((button) => {
                button.addEventListener('click', async (event) => {
                    event.preventDefault();

                    const orderId = button.dataset.id;
                    const confirmResult = await Swal.fire({
                        title: 'Send order to courier?',
                        text: `Order ID: ${orderId}`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, send it',
                    });

                    if (!confirmResult.isConfirmed) {
                        return;
                    }

                    try {
                        const response = await fetch(button.href, {
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                        });

                        const data = await response.json();

                        if (!response.ok || data.status !== 'success') {
                            throw new Error(data.message || 'Courier request failed.');
                        }

                        await Swal.fire({
                            icon: 'success',
                            title: data.message,
                            timer: 1500,
                            showConfirmButton: false,
                        });

                        window.location.reload();
                    } catch (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Courier request failed',
                            text: error.message,
                        });
                    }
                });
            });
        })();
    </script>
@endpush
