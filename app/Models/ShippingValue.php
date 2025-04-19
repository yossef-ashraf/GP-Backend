<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShippingValue extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['area_id', 'value'];

    public function area()
    {
        return $this->belongsTo(Area::class);
    }
}