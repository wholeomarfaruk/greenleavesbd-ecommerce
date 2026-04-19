@extends('layouts.app')

@section('content')
    <section class="py-5">
        <div class="container">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4 p-lg-5">
                    <div class="row g-4 align-items-start">
                        <div class="col-lg-8">
                            <span class="badge bg-success-subtle text-success mb-3">Order Confirmed</span>
                            <h1 class="h2 mb-2">Thank you for your order.</h1>

                            @if ($order->payment_method === 'bkash')
                                <p class="text-muted mb-0">
                                    আপনার অর্ডারটি আমরা গ্রহণ করেছি। bKash Transaction ID
                                    <strong>{{ $order->transaction_id }}</strong> আমরা যাচাই করে খুব দ্রুত অর্ডার কনফার্ম করব।
                                </p>
                            @else
                                <p class="text-muted mb-0">
                                    আপনার অর্ডারটি সফলভাবে নেওয়া হয়েছে। ডেলিভারির সময় পেমেন্ট সংগ্রহ করা হবে।
                                </p>
                            @endif
                        </div>

                        <div class="col-lg-4">
                            <div class="border rounded-4 p-4 bg-light">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Order Number</span>
                                    <strong>{{ $order->order_number }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Order Status</span>
                                    <strong class="text-capitalize">{{ $order->order_status }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Payment Method</span>
                                    <strong class="text-uppercase">{{ $order->payment_method }}</strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Payment Status</span>
                                    <strong class="text-capitalize">{{ $order->payment_status }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h2 class="h5 mb-3">Ordered Items</h2>

                            @foreach ($order->items as $item)
                                <div class="d-flex gap-3 py-3 border-bottom">
                                    <img src="{{ $item->product_image ? asset($item->product_image) : ($item->product?->featured_image ?? asset('frontend/img/logo-transparent.png')) }}"
                                        alt="{{ $item->product_name }}"
                                        width="72"
                                        height="72"
                                        class="rounded-3 object-fit-cover">

                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between gap-3">
                                            <div>
                                                <h3 class="h6 mb-1">{{ $item->product_name }}</h3>
                                                <p class="small text-muted mb-1">Quantity: {{ $item->quantity }}</p>
                                                <p class="small text-muted mb-0">Unit Price: ৳{{ number_format((float) $item->unit_price, 2) }}</p>
                                            </div>
                                            <strong>৳{{ number_format((float) $item->line_total, 2) }}</strong>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h2 class="h5 mb-3">Customer Information</h2>
                            <p class="mb-2"><strong>Name:</strong> {{ $order->full_name }}</p>
                            <p class="mb-2"><strong>Phone:</strong> {{ $order->phone }}</p>
                            @if ($order->email)
                                <p class="mb-2"><strong>Email:</strong> {{ $order->email }}</p>
                            @endif
                            <p class="mb-2"><strong>Address:</strong> {{ $order->address }}</p>
                            <p class="mb-2"><strong>City:</strong> {{ $order->city }}</p>
                            @if ($order->area)
                                <p class="mb-0"><strong>Area:</strong> {{ $order->area }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h2 class="h5 mb-3">Totals</h2>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal</span>
                                <strong>৳{{ number_format((float) $order->subtotal, 2) }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Total</span>
                                <strong>৳{{ number_format((float) $order->total, 2) }}</strong>
                            </div>

                            @if ($order->payment_method === 'cod')
                                <div class="alert alert-info mt-3 mb-0">
                                    Customer will pay on delivery.
                                </div>
                            @else
                                <div class="alert alert-warning mt-3 mb-0">
                                    bKash payment is pending verification. Number: <strong>{{ $bkashNumber }}</strong>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
