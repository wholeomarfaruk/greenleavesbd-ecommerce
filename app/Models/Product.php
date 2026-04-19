<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'name',
        'price',
        'discount_price',
        'description',
        'short_description',
        'stock_status',
        'quantity',
        'status',
        'slug',
        'image',
        'sku',
        'featured',
    ];

    protected $appends = [
        'featured_image',
        'sizechart',
    ];

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'product_id');
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class, 'product_id');
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    public function sizes(): HasMany
    {
        return $this->hasMany(Size::class, 'products_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'product_category');
    }

    public function getFeaturedImageAttribute(): string
    {
        if (! empty($this->image) && file_exists(public_path('storage/images/products/thumbnails/' . $this->image))) {
            return asset('storage/images/products/thumbnails/' . $this->image);
        }

        $media = $this->media?->where('category', 'featured_image')->first();

        if ($media && file_exists(public_path('uploads/' . $media->path))) {
            return asset('uploads/' . $media->path);
        }

        return asset('frontend/img/logo-transparent.png');
    }

    public function getSizechartAttribute(): ?string
    {
        $media = $this->media?->where('category', 'sizechart')->first();

        if ($media && file_exists(public_path($media->path))) {
            return asset($media->path);
        }

        return null;
    }
}
