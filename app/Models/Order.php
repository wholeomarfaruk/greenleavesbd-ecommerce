<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class Order extends Model
{
    use Notifiable;

    protected $fillable = [
        'order_number',
        'user_id',
        'device_id',
        'session_id',
        'full_name',
        'name',
        'phone',
        'email',
        'address',
        'city',
        'area',
        'note',
        'payment_method',
        'transaction_id',
        'payment_status',
        'order_status',
        'status',
        'subtotal',
        'discount',
        'fee',
        'total',
        'is_paid',
        'ip_address',
        'user_agent',
        'json_data',
        'delivery_area_id',
    ];

    protected $casts = [
        'json_data' => 'array',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'fee' => 'decimal:2',
        'total' => 'decimal:2',
        'is_paid' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (Order $order): void {
            if ($order->isDirty('name')) {
                $order->full_name = $order->name;
            }

            if ($order->isDirty('full_name')) {
                $order->name = $order->full_name;
            }

            if ($order->isDirty('status')) {
                $order->order_status = $order->status;
            }

            if ($order->isDirty('order_status')) {
                $order->status = $order->order_status;
            }

            if ($order->isDirty('is_paid')) {
                $order->payment_status = $order->is_paid ? 'paid' : ($order->payment_method === 'bkash' ? 'pending' : 'unpaid');
            }

            if ($order->isDirty('payment_status')) {
                $order->is_paid = $order->payment_status === 'paid';
            }

            if (blank($order->full_name) && filled($order->name)) {
                $order->full_name = $order->name;
            }

            if (blank($order->name) && filled($order->full_name)) {
                $order->name = $order->full_name;
            }

            if (blank($order->order_status) && filled($order->status)) {
                $order->order_status = $order->status;
            }

            if (blank($order->status) && filled($order->order_status)) {
                $order->status = $order->order_status;
            }

            if (blank($order->payment_status)) {
                $order->payment_status = $order->is_paid ? 'paid' : ($order->payment_method === 'bkash' ? 'pending' : 'unpaid');
            }
        });
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function Order_Item(): HasMany
    {
        return $this->items();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class, 'device_id');
    }

    public function delivery_area(): BelongsTo
    {
        return $this->belongsTo(delivery_areas::class, 'delivery_area_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'phone', 'phone');
    }

    protected function orderNumberLabel(): Attribute
    {
        return Attribute::get(fn (): string => $this->order_number ?: ('ORD-' . $this->id));
    }
}
