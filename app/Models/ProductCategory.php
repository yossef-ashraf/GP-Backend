<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductCategory extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'product_categories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'category_id'
    ];

    /**
     * Get the product that owns the product category.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the category that owns the product category.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}