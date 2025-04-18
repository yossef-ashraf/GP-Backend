<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shipping_value extends Model
{
    use SoftDeletes;

    protected $table = 'shipping_values';

    protected $fillable = [
        'area_id',
        'value'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

}
