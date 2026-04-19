<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Models\Order;
use App\Support\CartManager;
use App\Support\OrderNumberGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function show(Request $request, CartManager $cartManager): View|RedirectResponse
    {
        $cart = $cartManager->resolveCurrentCart($request, false);
        $cartData = $cartManager->getCartPayload($cart);

        if (empty($cartData['items'])) {
            return redirect()
                ->route('cart.index')
                ->with('cart_error', 'Your cart is empty. Add some products before checkout.');
        }

        $bkashNumber = config('services.bkash.number');

        return view('checkout', compact('cartData', 'bkashNumber'));
    }

    public function store(CheckoutRequest $request, CartManager $cartManager): RedirectResponse
    {
        $cart = $cartManager->resolveCurrentCart($request, false);
        $cartData = $cartManager->getCartPayload($cart);
        $validated = $request->validated();

        if (! $cart || empty($cartData['items'])) {
            return redirect()
                ->route('cart.index')
                ->with('cart_error', 'Your cart is empty. Add some products before checkout.');
        }

        $device = $cartManager->resolveDevice($request);

        $order = DB::transaction(function () use ($request, $validated, $cart, $cartData, $cartManager, $device): Order {
            $paymentMethod = $validated['payment_method'];
            $paymentStatus = $paymentMethod === 'bkash' ? 'pending' : 'unpaid';

            $order = Order::create([
                'order_number' => OrderNumberGenerator::generate(),
                'user_id' => auth()->id(),
                'device_id' => $device?->id ?? $cart->device_id,
                'session_id' => $request->session()->getId(),
                'full_name' => $validated['full_name'],
                'name' => $validated['full_name'],
                'phone' => $validated['phone'],
                'email' => $validated['email'] ?? null,
                'address' => $validated['address'],
                'city' => $validated['city'],
                'area' => $validated['area'] ?? null,
                'note' => $validated['order_note'] ?? null,
                'payment_method' => $paymentMethod,
                'transaction_id' => $validated['transaction_id'] ?? null,
                'payment_status' => $paymentStatus,
                'order_status' => 'pending',
                'status' => 'pending',
                'subtotal' => $cartData['subtotal'],
                'discount' => 0,
                'fee' => 0,
                'total' => $cartData['subtotal'],
                'is_paid' => false,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'json_data' => [
                    'source' => 'cart_checkout',
                    'cart' => $cartData,
                ],
            ]);

            $orderItems = $cart->items
                ->loadMissing('product')
                ->map(fn ($item): array => [
                    'product_id' => $item->product_id,
                    'product_name' => $item->product?->name ?? 'Deleted product',
                    'product_image' => $cartManager->resolveProductImagePath($item->product),
                    'unit_price' => (float) $item->price,
                    'price' => (float) $item->price,
                    'quantity' => $item->quantity,
                    'line_total' => round(((float) $item->price) * $item->quantity, 2),
                    'options' => null,
                    'rstatus' => false,
                ])
                ->all();

            $order->items()->createMany($orderItems);

            $cartManager->clearCart($cart);

            return $order->load('items.product');
        });

        return redirect()
            ->route('checkout.confirmation', ['order' => $order->order_number])
            ->with('checkout_success', 'Your order has been placed successfully.');
    }

    public function confirmation(Order $order): View
    {
        $order->loadMissing('items.product', 'user', 'device');
        $bkashNumber = config('services.bkash.number');

        return view('orders.confirmation', compact('order', 'bkashNumber'));
    }
}
