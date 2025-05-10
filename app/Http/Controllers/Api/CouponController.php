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

    /**
     * Display a listing of the coupons.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $coupons = Coupon::all();
        return $this->successResponse($coupons, 'Coupons retrieved successfully');
    }

    /**
     * Store a newly created coupon.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:50|unique:coupons,code',
            'type' => 'required|string|in:fixed,percentage',
            'value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'is_active' => 'boolean',
            'usage_limit' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422, $validator->errors());
        }

        // Validate percentage value
        if ($request->type === 'percentage' && $request->value > 100) {
            return $this->errorResponse('Percentage value cannot exceed 100', 422);
        }

        $coupon = Coupon::create([
            'code' => strtoupper($request->code),
            'type' => $request->type,
            'value' => $request->value,
            'min_order_amount' => $request->min_order_amount,
            'starts_at' => $request->starts_at,
            'expires_at' => $request->expires_at,
            'is_active' => $request->is_active ?? true,
            'usage_limit' => $request->usage_limit,
            'usage_count' => 0,
        ]);

        return $this->successResponse($coupon, 'Coupon created successfully', 201);
    }

    /**
     * Display the specified coupon.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $coupon = Coupon::find($id);
        
        if (!$coupon) {
            return $this->errorResponse('Coupon not found', 404);
        }
        
        return $this->successResponse($coupon, 'Coupon retrieved successfully');
    }

    /**
     * Update the specified coupon.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $coupon = Coupon::find($id);
        
        if (!$coupon) {
            return $this->errorResponse('Coupon not found', 404);
        }
        
        $validator = Validator::make($request->all(), [
            'code' => 'sometimes|required|string|max:50|unique:coupons,code,' . $id,
            'type' => 'sometimes|required|string|in:fixed,percentage',
            'value' => 'sometimes|required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'is_active' => 'boolean',
            'usage_limit' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422, $validator->errors());
        }

        // Validate percentage value
        if ($request->has('type') && $request->type === 'percentage' && $request->has('value') && $request->value > 100) {
            return $this->errorResponse('Percentage value cannot exceed 100', 422);
        }

        if ($request->has('code')) {
            $request->merge(['code' => strtoupper($request->code)]);
        }

        $coupon->update($request->all());
        
        return $this->successResponse($coupon, 'Coupon updated successfully');
    }

    /**
     * Remove the specified coupon.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $coupon = Coupon::find($id);
        
        if (!$coupon) {
            return $this->errorResponse('Coupon not found', 404);
        }
        
        $coupon->delete();
        
        return $this->successResponse(null, 'Coupon deleted successfully');
    }

    /**
     * Validate a coupon code.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
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
        if ($coupon->type === 'fixed') {
            $discount = $coupon->value;
        } else { // percentage
            $discount = ($request->order_amount * $coupon->value) / 100;
        }
        
        return $this->successResponse([
            'coupon' => $coupon,
            'discount' => $discount,
            'final_amount' => max(0, $request->order_amount - $discount)
        ], 'Coupon is valid');
    }

    /**
     * Apply a coupon to an order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
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