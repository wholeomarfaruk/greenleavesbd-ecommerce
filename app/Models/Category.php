<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'parent_id',
        'image',
        'is_homepage_show',
        'display_order',
        'is_show_in_menu',
        'is_active',
    ];
    public function children()
{
    return $this->hasMany(Category::class, 'parent_id');
}

public function parent()
{
    return $this->belongsTo(Category::class, 'parent_id');
}
public function products()
    {
        return $this->belongsToMany(products::class, 'product_category');
    }
}
