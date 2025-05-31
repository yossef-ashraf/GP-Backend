<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'discount_price',
        'stock',
        'sale_price',
        'type',
        'stock_status',
        'stock_qty',
        'slug',
        'author',
        'image',
        'is_active',
        'sku',
        'weight',
        'dimensions',
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        return $this->image
            ? asset('storage/' . $this->image)
            : null;
    }

    // protected $casts = [
    //     'price' => 'float',
    //     'discount_price' => 'float',
    //     'stock' => 'integer',
    //     'is_active' => 'boolean',
    //     'weight' => 'float',
    //     'dimensions' => 'json',
    // ];
    
    public function variations()
    {
        return $this->hasMany(ProductVariation::class);
    }

    // public function cartItems()
    // {
    //     return $this->hasMany(CartItem::class);
    // }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the categories for the product.
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_categories', 'product_id', 'category_id');
    }
}