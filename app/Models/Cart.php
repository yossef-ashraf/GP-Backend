<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'variation_id',
        'quantity',
        'total',
    ];

    // public function getTotalAttribute()
    // {
    //     return 0 ;
    //     // return round($this->total) ?? 0 ;
    // }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function variation()
    {
        return $this->belongsTo(ProductVariation::class, 'variation_id');
    }


}