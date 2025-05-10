<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'coupon_id',
        'total',
        'discount',
    ];

    protected $casts = [
        'total' => 'float',
        'discount' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * Calculate the cart total
     */
    public function calculateTotal()
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item->quantity * ($item->product->discount_price ?? $item->product->price);
        }
        
        $this->total = $total;
        
        // Apply coupon discount if available
        if ($this->coupon) {
            if ($this->coupon->type === 'percentage') {
                $this->discount = $total * ($this->coupon->value / 100);
            } else {
                $this->discount = $this->coupon->value;
            }
            
            // Ensure discount doesn't exceed total
            $this->discount = min($this->discount, $this->total);
            $this->total -= $this->discount;
        } else {
            $this->discount = 0;
        }
        
        $this->save();
        
        return $this->total;
    }
}