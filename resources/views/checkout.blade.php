@extends('layouts.app')

@section('content')
    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h1 class="h3 mb-1">Checkout</h1>
                            <p class="text-muted mb-4">Complete your shipping and payment details to place the order.</p>

                            <form action="{{ route('checkout.place') }}" method="POST" novalidate>
                                @csrf

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="full_name" class="form-label">Full Name</label>
                                        <input type="text"
                                            id="full_name"
                                            name="full_name"
                                            value="{{ old('full_name') }}"
                                            class="form-control @error('full_name') is-invalid @enderror"
                                            required>
                                        @error('full_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="phone" class="form-label">Phone</label>
                                        <input type="text"
                                            id="phone"
                                            name="phone"
                                            value="{{ old('phone') }}"
                                            class="form-control @error('phone') is-invalid @enderror"
                                            required>
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <label for="email" class="form-label">Email (Optional)</label>
                                        <input type="email"
                                            id="email"
                                            name="email"
                                            value="{{ old('email') }}"
                                            class="form-control @error('email') is-invalid @enderror">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <label for="address" class="form-label">Address</label>
                                        <textarea id="address"
                                            name="address"
                                            rows="3"
                                            class="form-control @error('address') is-invalid @enderror"
                                            required>{{ old('address') }}</textarea>
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="city" class="form-label">City</label>
                                        <input type="text"
                                            id="city"
                                            name="city"
                                            value="{{ old('city') }}"
                                            class="form-control @error('city') is-invalid @enderror"
                                            required>
                                        @error('city')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="area" class="form-label">Area (Optional)</label>
                                        <input type="text"
                                            id="area"
                                            name="area"
                                            value="{{ old('area') }}"
                                            class="form-control @error('area') is-invalid @enderror">
                                        @error('area')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <label for="order_note" class="form-label">Order Note (Optional)</label>
                                        <textarea id="order_note"
                                            name="order_note"
                                            rows="3"
                                            class="form-control @error('order_note') is-invalid @enderror">{{ old('order_note') }}</textarea>
                                        @error('order_note')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label d-block">Payment Method</label>
                                        <div class="d-flex flex-column gap-3">
                                            <label class="border rounded-3 p-3 d-flex justify-content-between align-items-center">
                                                <span>
                                                    <strong>Cash on Delivery</strong>
                                                    <small class="d-block text-muted">Pay when the order is delivered to you.</small>
                                                </span>
                                                <input type="radio"
                                                    name="payment_method"
                                                    value="cod"
                                                    class="form-check-input"
                                                    {{ old('payment_method', 'cod') === 'cod' ? 'checked' : '' }}>
                                            </label>

                                            <label class="border rounded-3 p-3 d-flex justify-content-between align-items-center">
                                                <span>
                                                    <strong>bKash</strong>
                                                    <small class="d-block text-muted">Send money first, then submit the transaction ID.</small>
                                                </span>
                                                <input type="radio"
                                                    name="payment_method"
                                                    value="bkash"
                                                    class="form-check-input"
                                                    {{ old('payment_method') === 'bkash' ? 'checked' : '' }}>
                                            </label>
                                        </div>
                                        @error('payment_method')
                                            <div class="text-danger small mt-2">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 {{ old('payment_method') === 'bkash' ? '' : 'd-none' }}"
                                        id="bkash-instructions-card">
                                        <div class="alert alert-warning mb-0">
                                            <h2 class="h6 fw-bold mb-3">bKash Payment Instructions</h2>
                                            <p class="mb-2"><strong>Send money to this bKash number:</strong> {{ $bkashNumber }}</p>
                                            <ol class="mb-3 ps-3">
                                                <li>এই নাম্বারে bKash থেকে Send Money করুন।</li>
                                                <li>পেমেন্ট সম্পন্ন হলে নিচের ঘরে Transaction ID লিখুন।</li>
                                                <li>আমরা পেমেন্ট যাচাই করে আপনার অর্ডার কনফার্ম করব।</li>
                                            </ol>

                                            <label for="transaction_id" class="form-label fw-semibold">Transaction ID</label>
                                            <input type="text"
                                                id="transaction_id"
                                                name="transaction_id"
                                                value="{{ old('transaction_id') }}"
                                                class="form-control @error('transaction_id') is-invalid @enderror"
                                                placeholder="Enter your bKash transaction ID">
                                            @error('transaction_id')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary mt-4 w-100" data-place-order-button>
                                    Place Order
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm sticky-top" style="top: 2rem;">
                        <div class="card-body p-4">
                            <h2 class="h5 mb-3">Order Summary</h2>

                            <div data-checkout-items>
                                @foreach ($cartData['items'] as $item)
                                    <div class="d-flex gap-3 py-3 border-bottom">
                                        <img src="{{ $item['featured_image'] }}"
                                            alt="{{ $item['name'] }}"
                                            width="72"
                                            height="72"
                                            class="rounded-3 object-fit-cover">
                                        <div class="flex-grow-1">
                                            <h3 class="h6 mb-1">{{ $item['name'] }}</h3>
                                            <p class="text-muted small mb-1">Qty: {{ $item['quantity'] }}</p>
                                            <div class="d-flex justify-content-between">
                                                <span class="small text-muted">Unit: ৳{{ number_format($item['unit_price'], 2) }}</span>
                                                <strong>৳{{ number_format($item['line_total'], 2) }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="pt-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal</span>
                                    <strong data-checkout-subtotal>৳{{ number_format($cartData['subtotal'], 2) }}</strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Total</span>
                                    <strong data-checkout-total>৳{{ number_format($cartData['subtotal'], 2) }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const paymentInputs = document.querySelectorAll('input[name="payment_method"]');
            const bkashCard = document.getElementById('bkash-instructions-card');

            const toggleBkashInstructions = () => {
                const selectedValue = document.querySelector('input[name="payment_method"]:checked')?.value;
                bkashCard?.classList.toggle('d-none', selectedValue !== 'bkash');
            };

            paymentInputs.forEach((input) => {
                input.addEventListener('change', toggleBkashInstructions);
            });

            toggleBkashInstructions();
        });
    </script>
@endpush
