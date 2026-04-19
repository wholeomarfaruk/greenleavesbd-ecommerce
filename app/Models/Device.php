<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Device extends Model
{
    protected $fillable = [
        'name',
        'model',
        'user_agent',
        'ip_address',
        'status',
        'customer_id',
        'last_seen'
    ];
    protected $appends = ['is_blocked'];


    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function blackLists(): MorphMany
    {
        return $this->morphMany(BlackList::class, 'black_listable');
    }

    public function getIsBlockedAttribute(): bool
    {
        return $this->blackLists()->exists();
    }

    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class, 'device_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'device_id');
    }
}
