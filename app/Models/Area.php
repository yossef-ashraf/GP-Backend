<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Area extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name'];

    protected $appends = ['shipping_value'];

    // Accessor for shipping_value
    public function getShippingValueAttribute()
    {
        // Assuming you want the latest shipping value
        return $this->shippingValues()->latest()->value('value');
    }

    public function shippingValues()
    {
        return $this->hasMany(ShippingValue::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }
}
