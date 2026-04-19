<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));

        $query = Cart::query()
            ->with(['user', 'device', 'items.product'])
            ->withCount('items')
            ->orderByDesc('updated_at');

        if ($search !== '') {
            $query->where(function (Builder $builder) use ($search): void {
                $builder
                    ->where('id', 'like', '%' . $search . '%')
                    ->orWhere('session_id', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function (Builder $userQuery) use ($search): void {
                        $userQuery
                            ->where('name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('device', function (Builder $deviceQuery) use ($search): void {
                        $deviceQuery
                            ->where('user_agent', 'like', '%' . $search . '%')
                            ->orWhere('ip_address', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('items.product', function (Builder $productQuery) use ($search): void {
                        $productQuery->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        $carts = $query->paginate(20)->withQueryString();
        $cartCount = Cart::query()->count();
        $activeCartCount = Cart::query()->has('items')->count();

        return view('admin.carts.index', compact(
            'carts',
            'cartCount',
            'activeCartCount',
            'search',
        ));
    }

    public function show(Cart $cart): View
    {
        $cart->load(['user', 'device', 'items.product']);

        return view('admin.carts.show', compact('cart'));
    }
}
