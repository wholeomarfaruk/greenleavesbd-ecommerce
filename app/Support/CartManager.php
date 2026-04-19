<?php

namespace App\Support;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Device;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartManager
{
    public function resolveCurrentCart(Request $request, bool $create = true): ?Cart
    {
        $sessionId = $request->session()->getId();
        $device = $this->resolveDevice($request);
        $guestCart = $this->findGuestCart($sessionId, $device?->id);

        if (auth()->check()) {
            $userCart = Cart::query()
                ->with('items.product')
                ->where('user_id', auth()->id())
                ->first();

            if (! $userCart && $guestCart) {
                $guestCart->forceFill([
                    'user_id' => auth()->id(),
                    'device_id' => $device?->id,
                    'session_id' => $sessionId,
                ])->save();

                return $guestCart->load('items.product');
            }

            if (! $userCart && ! $create) {
                return null;
            }

            if (! $userCart) {
                $userCart = Cart::create([
                    'user_id' => auth()->id(),
                    'device_id' => $device?->id,
                    'session_id' => $sessionId,
                ]);
            }

            if ($guestCart && $guestCart->id !== $userCart->id) {
                $this->mergeCartItems($guestCart, $userCart);
                $guestCart->delete();
            }

            $userCart->forceFill([
                'device_id' => $device?->id,
                'session_id' => $sessionId,
            ])->save();

            return $userCart->load('items.product');
        }

        if ($guestCart) {
            $guestCart->forceFill([
                'device_id' => $device?->id,
                'session_id' => $sessionId,
            ])->save();

            return $guestCart->load('items.product');
        }

        if (! $create) {
            return null;
        }

        return Cart::create([
            'user_id' => null,
            'device_id' => $device?->id,
            'session_id' => $sessionId,
        ])->load('items.product');
    }

    public function resolveDevice(Request $request): ?Device
    {
        $userAgent = trim((string) $request->userAgent());

        if ($userAgent === '') {
            return null;
        }

        $device = Device::query()->firstOrCreate(
            ['user_agent' => $userAgent],
            [
                'name' => 'Guest Device',
                'model' => substr($userAgent, 0, 255),
                'ip_address' => $request->ip(),
                'status' => 'active',
                'last_seen' => now(),
            ],
        );

        $device->forceFill([
            'ip_address' => $request->ip(),
            'last_seen' => now(),
        ])->save();

        return $device;
    }

    public function addProduct(Cart $cart, Product $product, int $quantity): CartItem
    {
        return DB::transaction(function () use ($cart, $product, $quantity): CartItem {
            $item = CartItem::query()
                ->where('cart_id', $cart->id)
                ->where('product_id', $product->id)
                ->lockForUpdate()
                ->first();

            $price = $this->resolveProductPrice($product);

            if ($item) {
                $item->quantity += $quantity;
                $item->price = $price;
                $item->save();

                return $item->load('product');
            }

            return CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'price' => $price,
            ])->load('product');
        });
    }

    public function updateQuantity(CartItem $cartItem, int $quantity): CartItem
    {
        $cartItem->update(['quantity' => $quantity]);

        return $cartItem->fresh('product');
    }

    public function removeItem(CartItem $cartItem): void
    {
        $cartItem->delete();
    }

    public function clearCart(Cart $cart): void
    {
        $cart->items()->delete();
    }

    public function ownsCartItem(Cart $cart, CartItem $cartItem): bool
    {
        return $cartItem->cart_id === $cart->id;
    }

    public function getCartPayload(?Cart $cart): array
    {
        if (! $cart) {
            return [
                'cart_id' => null,
                'items' => [],
                'subtotal' => 0.0,
                'cart_subtotal' => 0.0,
                'total_items' => 0,
                'cart_count' => 0,
            ];
        }

        $cart->loadMissing('items.product');

        $items = $cart->items
            ->sortByDesc('id')
            ->values()
            ->map(fn (CartItem $item): array => [
                'cart_item_id' => $item->id,
                'product_id' => $item->product_id,
                'name' => $item->product?->name ?? 'Product unavailable',
                'slug' => $item->product?->slug,
                'featured_image' => $this->resolveProductImage($item->product),
                'unit_price' => round((float) $item->price, 2),
                'quantity' => $item->quantity,
                'line_total' => $item->lineTotal(),
            ]);

        $subtotal = round($items->sum('line_total'), 2);
        $totalItems = (int) $items->sum('quantity');

        return [
            'cart_id' => $cart->id,
            'items' => $items->all(),
            'subtotal' => $subtotal,
            'cart_subtotal' => $subtotal,
            'total_items' => $totalItems,
            'cart_count' => $totalItems,
        ];
    }

    public function resolveProductPrice(Product $product): float
    {
        $salePrice = $product->sale_price ?? $product->discount_price;
        $regularPrice = $product->regular_price ?? $product->price;

        return round((float) ($salePrice ?: $regularPrice), 2);
    }

    public function resolveProductImage(?Product $product): string
    {
        if (! $product) {
            return asset('frontend/img/logo-transparent.png');
        }

        if (! empty($product->image) && file_exists(public_path('storage/images/products/thumbnails/' . $product->image))) {
            return asset('storage/images/products/thumbnails/' . $product->image);
        }

        return $product->featured_image;
    }

    public function resolveProductImagePath(?Product $product): ?string
    {
        if (! $product) {
            return null;
        }

        if (! empty($product->image)) {
            return 'storage/images/products/thumbnails/' . $product->image;
        }

        return null;
    }

    protected function findGuestCart(?string $sessionId, ?int $deviceId): ?Cart
    {
        if (! $sessionId && ! $deviceId) {
            return null;
        }

        $query = Cart::query()
            ->with('items.product')
            ->whereNull('user_id');

        if ($sessionId && $deviceId) {
            $query->where(function (Builder $builder) use ($sessionId, $deviceId): void {
                $builder
                    ->where('session_id', $sessionId)
                    ->orWhere('device_id', $deviceId);
            });
        } elseif ($sessionId) {
            $query->where('session_id', $sessionId);
        } else {
            $query->where('device_id', $deviceId);
        }

        return $query
            ->latest('updated_at')
            ->first();
    }

    protected function mergeCartItems(Cart $sourceCart, Cart $targetCart): void
    {
        $sourceCart->loadMissing('items.product');
        $targetCart->loadMissing('items.product');

        foreach ($sourceCart->items as $item) {
            $existingTargetItem = $targetCart->items
                ->firstWhere('product_id', $item->product_id);

            if ($existingTargetItem) {
                $existingTargetItem->quantity += $item->quantity;
                $existingTargetItem->price = $item->price;
                $existingTargetItem->save();
            } else {
                $targetCart->items()->create([
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                ]);
            }
        }
    }
}
