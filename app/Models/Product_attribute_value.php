<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product_attribute_value extends Model
{
    protected $table = 'product_attribute_values';

    protected $fillable = [
        'product_id',
        'attribute_id',
        'attribute_value_id'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function attribute()
    {
        return $this->belongsTo(Attribute::class, 'attribute_id');
    }

    public function attribute_value()
    {
        return $this->belongsTo(Attribute_value::class, 'attribute_value_id');
    }

}
