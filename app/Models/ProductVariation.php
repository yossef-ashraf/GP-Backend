<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'slug',
        'price',
        'sale_price',
        'sku',
        'stock_status',
        'stock_qty',
    ];

    protected $casts = [
        'variation_data' => 'array',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'variation_id');
    }

    // public function cartItems()
    // {
    //     return $this->hasMany(CartItem::class, 'variation_id');
    // }
}