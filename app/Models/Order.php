<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'shipping_cost',
        'area_id',
        'address', 
        'coupon_id',
        'tracking_number',
        'status',
        'subtotal',
        'total_amount',
        'payment_method',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function addresses()
    {
        return $this->belongsTo(Address::class);
    }
    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function coupons()
    {
        return $this->coupon();
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function notes()
    {
        return $this->hasMany(OrderNote::class);
    }
  
}