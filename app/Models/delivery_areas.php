<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class delivery_areas extends Model
{
    protected $fillable = ['name', 'charge'];

    public function orders()
    {
        return $this->hasMany(Order::class, 'delivery_area_id');
    }
}
