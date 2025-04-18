<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $table = 'orders';

    protected $fillable = [
        'user_id',
        'coupon_id',
        'address_id',
        'payment_method_id',
        'total_amount',
        'status',
        'notes'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class, 'coupon_id');
    }

    public function address()
    {
        return $this->belongsTo(Addres::class, 'address_id');
    }

    public function payment_method()
    {
        return $this->belongsTo(Payment_method::class, 'payment_method_id');
    }

}
