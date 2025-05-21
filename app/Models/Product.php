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
        'category_id',
        'image',
        'is_active',
        'sku',
        'weight',
        'dimensions',
    ];

    protected $casts = [
        'price' => 'float',
        'discount_price' => 'float',
        'stock' => 'integer',
        'is_active' => 'boolean',
        'weight' => 'float',
        'dimensions' => 'json',
    ];
    public function variations()
    {
        return $this->belongsTo(ProductVariation::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

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