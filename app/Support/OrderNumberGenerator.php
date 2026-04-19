<?php

namespace App\Support;

use App\Models\Order;

class OrderNumberGenerator
{
    public static function generate(): string
    {
        do {
            $orderNumber = 'GL-' . now()->format('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
        } while (Order::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }
}
