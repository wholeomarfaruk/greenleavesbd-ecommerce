<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\AddToCartRequest;
use App\Http\Requests\Cart\RemoveCartItemRequest;
use App\Http\Requests\Cart\UpdateCartItemRequest;
use App\Models\CartItem;
use App\Models\Product;
use App\Support\CartManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(Request $request, CartManager $cartManager): View
    {
        $cartData = $cartManager->getCartPayload(
            $cartManager->resolveCurrentCart($request, false)
        );

        return view('cart', compact('cartData'));
    }

    public function items(Request $request, CartManager $cartManager): JsonResponse
    {
        $cartData = $cartManager->getCartPayload(
            $cartManager->resolveCurrentCart($request, false)
        );

        return response()->json([
            'status' => true,
            'message' => 'Cart loaded successfully.',
            ...$cartData,
        ]);
    }

    public function store(AddToCartRequest $request, CartManager $cartManager): JsonResponse
    {
        $product = Product::query()
            ->whereKey($request->integer('product_id'))
            ->first();

        if (! $product || ! $product->status) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found.',
            ], 404);
        }

        if ($product->stock_status === 'out_of_stock') {
            return response()->json([
                'status' => false,
                'message' => 'This product is currently out of stock.',
            ], 422);
        }

        $cart = $cartManager->resolveCurrentCart($request);

        $cartManager->addProduct(
            $cart,
            $product,
            (int) ($request->validated('quantity') ?? 1),
        );

        $cartData = $cartManager->getCartPayload($cart->fresh('items.product'));

        return response()->json([
            'status' => true,
            'message' => 'Product added to cart successfully.',
            ...$cartData,
        ]);
    }

    public function update(
        UpdateCartItemRequest $request,
        CartItem $cartItem,
        CartManager $cartManager
    ): JsonResponse {
        $cart = $cartManager->resolveCurrentCart($request, false);

        if (! $cart || ! $cartManager->ownsCartItem($cart, $cartItem)) {
            return response()->json([
                'status' => false,
                'message' => 'Cart item not found.',
            ], 404);
        }

        $cartManager->updateQuantity($cartItem, $request->integer('quantity'));

        $cartData = $cartManager->getCartPayload($cart->fresh('items.product'));

        return response()->json([
            'status' => true,
            'message' => 'Cart quantity updated successfully.',
            ...$cartData,
        ]);
    }

    public function destroy(
        Request $request,
        CartItem $cartItem,
        CartManager $cartManager
    ): JsonResponse {
        $cart = $cartManager->resolveCurrentCart($request, false);

        if (! $cart || ! $cartManager->ownsCartItem($cart, $cartItem)) {
            return response()->json([
                'status' => false,
                'message' => 'Cart item not found.',
            ], 404);
        }

        $cartManager->removeItem($cartItem);

        $cartData = $cartManager->getCartPayload($cart->fresh('items.product'));

        return response()->json([
            'status' => true,
            'message' => 'Cart item removed successfully.',
            ...$cartData,
        ]);
    }

    public function remove(
        RemoveCartItemRequest $request,
        CartManager $cartManager
    ): JsonResponse {
        $cartItem = CartItem::query()->find($request->integer('cart_item_id'));

        if (! $cartItem) {
            return response()->json([
                'status' => false,
                'message' => 'Cart item not found.',
            ], 404);
        }

        return $this->destroy($request, $cartItem, $cartManager);
    }

    public function clear(Request $request, CartManager $cartManager): JsonResponse
    {
        $cart = $cartManager->resolveCurrentCart($request, false);

        if ($cart) {
            $cartManager->clearCart($cart);
        }

        return response()->json([
            'status' => true,
            'message' => 'Cart cleared successfully.',
            ...$cartManager->getCartPayload($cart?->fresh('items.product')),
        ]);
    }
}
