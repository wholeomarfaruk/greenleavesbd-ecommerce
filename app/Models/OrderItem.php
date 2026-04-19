<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $table = 'order_items';

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'product_image',
        'unit_price',
        'price',
        'quantity',
        'line_total',
        'options',
        'rstatus',
    ];

    protected $casts = [
        'options' => 'array',
        'unit_price' => 'decimal:2',
        'price' => 'decimal:2',
        'line_total' => 'decimal:2',
        'rstatus' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (OrderItem $item): void {
            if ($item->isDirty('price') && ! $item->isDirty('unit_price')) {
                $item->unit_price = $item->price;
            }

            if ($item->isDirty('unit_price') && ! $item->isDirty('price')) {
                $item->price = $item->unit_price;
            }

            if ($item->unit_price === null && $item->price !== null) {
                $item->unit_price = $item->price;
            }

            if ($item->price === null && $item->unit_price !== null) {
                $item->price = $item->unit_price;
            }

            if ($item->product_name === null && $item->product) {
                $item->product_name = $item->product->name;
            }

            if ($item->quantity > 0 && $item->unit_price !== null) {
                $item->line_total = round(((float) $item->unit_price) * $item->quantity, 2);
            }
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
