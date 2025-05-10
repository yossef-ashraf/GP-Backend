<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'type',
        'value',
        'min_order_amount',
        'starts_at',
        'expires_at',
        'is_active',
        'usage_limit',
        'usage_count',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'value' => 'float',
        'min_order_amount' => 'float',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'usage_limit' => 'integer',
        'usage_count' => 'integer',
    ];

    /**
     * Get the orders that used this coupon.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}