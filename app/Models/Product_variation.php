<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product_variation extends Model
{
    use SoftDeletes;

    protected $table = 'product_variations';

    protected $fillable = [
        'slug',
        'product_id',
        'regular_price',
        'sale_price',
        'manage_stock',
        'stock_status',
        'stock_qtn',
        'total_sales',
        'backorder_limit',
        'sku'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

}
