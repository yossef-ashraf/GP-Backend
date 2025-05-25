<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'address_id',
        'coupon_id',
        'order_number',
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

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function addresses()
    {
        return $this->address();
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
    /**
     * Calculate the order total
     */
    // في دالة calculateTotal
    public function calculateTotal()
    {
        // Calculate subtotal from items
        $subtotal = 0;
        foreach ($this->items as $item) {
            $subtotal += $item->quantity * $item->price;
        }
        $this->subtotal = $subtotal;
        
        // Add shipping cost
        $this->total = $subtotal + ($this->shipping_cost ?? 0);
        
        // Apply coupon discount if available
        if ($this->coupon) {
            if ($this->coupon->discount_type === 'percentage') {
                $this->discount = $subtotal * ($this->coupon->discount_value / 100);
            } else {
                $this->discount = $this->coupon->discount_value;
            }
            
            // Ensure discount doesn't exceed total
            $this->discount = min($this->discount, $this->total);
            $this->total -= $this->discount;
        } else {
            $this->discount = 0;
        }
        
        // Add tax if applicable
        if ($this->tax) {
            $this->total += $this->tax;
        }
        
        $this->save();
        
        return $this->total;
    }
}