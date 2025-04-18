<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Variation_attribute_value extends Model
{
    use SoftDeletes;

    protected $table = 'variation_attribute_values';

    protected $fillable = [
        'variation_id',
        'attribute_value_id'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function variation()
    {
        return $this->belongsTo(Variation::class, 'variation_id');
    }

    public function attribute_value()
    {
        return $this->belongsTo(Attribute_value::class, 'attribute_value_id');
    }

}
