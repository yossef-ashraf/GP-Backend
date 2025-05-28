<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CouponController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        $coupons = Coupon::all();
        return $this->successResponse($coupons, 'Coupons retrieved successfully');
    }
    public function show($id)
    {
        $coupon = Coupon::find($id);
        
        if (!$coupon) {
            return $this->errorResponse('Coupon not found', 404);
        }
        
        return $this->successResponse($coupon, 'Coupon retrieved successfully');
    }

    public function validate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string',
            'order_amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422, $validator->errors());
        }

        $code = strtoupper($request->code);
        $coupon = Coupon::where('code', $code)->first();
        
        if (!$coupon) {
            return $this->errorResponse('Invalid coupon code', 404);
        }
        
        // Check if coupon is active
        if (!$coupon->is_active) {
            return $this->errorResponse('Coupon is not active', 422);
        }
        
        // Check if coupon has expired
        if ($coupon->expires_at && now()->isAfter($coupon->expires_at)) {
            return $this->errorResponse('Coupon has expired', 422);
        }
        
        // Check if coupon has started
        if ($coupon->starts_at && now()->isBefore($coupon->starts_at)) {
            return $this->errorResponse('Coupon is not yet valid', 422);
        }
        
        // Check if coupon has reached usage limit
        if ($coupon->usage_limit && $coupon->usage_count >= $coupon->usage_limit) {
            return $this->errorResponse('Coupon usage limit reached', 422);
        }
        
        // Check minimum order amount
        if ($coupon->min_order_amount && $request->order_amount < $coupon->min_order_amount) {
            return $this->errorResponse('Order amount does not meet minimum requirement for this coupon', 422, [
                'min_order_amount' => $coupon->min_order_amount
            ]);
        }
        
        // Calculate discount
        $discount = 0;
        if ($coupon->discount_type === 'fixed') {
            $discount = $coupon->discount_value;
        } else { // percentage
            $discount = ($request->order_amount * $coupon->discount_value) / 100;
        }
        
        return $this->successResponse([
            'coupon' => $coupon,
            'discount' => $discount,
            'final_amount' => round( max(0, $request->order_amount - $discount))
        ], 'Coupon is valid');
    }

    public function apply(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string',
            'order_id' => 'required|exists:orders,id',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422, $validator->errors());
        }

        $code = strtoupper($request->code);
        $coupon = Coupon::where('code', $code)->first();
        
        if (!$coupon) {
            return $this->errorResponse('Invalid coupon code', 404);
        }
        
        // Increment usage count
        $coupon->increment('usage_count');
        
        // Logic to apply coupon to order would go here
        // This would typically involve updating the order with the coupon ID and discount amount
        
        return $this->successResponse($coupon, 'Coupon applied successfully');
    }
}