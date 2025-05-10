<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    use ApiResponseTrait;

    /**
     * Get the current user's cart
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCart(Request $request)
    {
        $user = $request->user();
        $cart = Cart::with(['items.product', 'coupon'])->where('user_id', $user->id)->first();
        
        if (!$cart) {
            $cart = Cart::create([
                'user_id' => $user->id,
                'total' => 0,
                'discount' => 0,
            ]);
        }
        
        return $this->successResponse($cart, 'Cart retrieved successfully');
    }

    /**
     * Add item to cart
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addItem(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422, $validator->errors());
        }

        $user = $request->user();
        $cart = Cart::where('user_id', $user->id)->first();
        
        if (!$cart) {
            $cart = Cart::create([
                'user_id' => $user->id,
                'total' => 0,
                'discount' => 0,
            ]);
        }
        
        $product = Product::findOrFail($request->product_id);
        
        // Check if product is in stock
        if ($product->stock < $request->quantity) {
            return $this->errorResponse('Product is out of stock or not enough quantity available', 422);
        }
        
        // Check if item already exists in cart
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $request->product_id)
            ->first();
            
        if ($cartItem) {
            // Update quantity
            $cartItem->quantity += $request->quantity;
            $cartItem->save();
        } else {
            // Create new cart item
            $cartItem = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'price' => $product->discount_price ?? $product->price,
            ]);
        }
        
        // Recalculate cart total
        $cart->calculateTotal();
        
        return $this->successResponse(
            Cart::with(['items.product', 'coupon'])->find($cart->id),
            'Item added to cart successfully'
        );
    }

    /**
     * Update cart item quantity
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $itemId
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateItem(Request $request, $itemId)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422, $validator->errors());
        }

        $user = $request->user();
        $cart = Cart::where('user_id', $user->id)->first();
        
        if (!$cart) {
            return $this->errorResponse('Cart not found', 404);
        }
        
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('id', $itemId)
            ->first();
            
        if (!$cartItem) {
            return $this->errorResponse('Cart item not found', 404);
        }
        
        // Check if product is in stock
        $product = Product::findOrFail($cartItem->product_id);
        if ($product->stock < $request->quantity) {
            return $this->errorResponse('Product is out of stock or not enough quantity available', 422);
        }
        
        // Update quantity
        $cartItem->quantity = $request->quantity;
        $cartItem->save();
        
        // Recalculate cart total
        $cart->calculateTotal();
        
        return $this->successResponse(
            Cart::with(['items.product', 'coupon'])->find($cart->id),
            'Cart item updated successfully'
        );
    }

    /**
     * Remove item from cart
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $itemId
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeItem(Request $request, $itemId)
    {
        $user = $request->user();
        $cart = Cart::where('user_id', $user->id)->first();
        
        if (!$cart) {
            return $this->errorResponse('Cart not found', 404);
        }
        
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('id', $itemId)
            ->first();
            
        if (!$cartItem) {
            return $this->errorResponse('Cart item not found', 404);
        }
        
        // Delete cart item
        $cartItem->delete();
        
        // Recalculate cart total
        $cart->calculateTotal();
        
        return $this->successResponse(
            Cart::with(['items.product', 'coupon'])->find($cart->id),
            'Cart item removed successfully'
        );
    }

    /**
     * Clear cart
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function clearCart(Request $request)
    {
        $user = $request->user();
        $cart = Cart::where('user_id', $user->id)->first();
        
        if (!$cart) {
            return $this->errorResponse('Cart not found', 404);
        }
        
        // Delete all cart items
        CartItem::where('cart_id', $cart->id)->delete();
        
        // Reset cart totals
        $cart->total = 0;
        $cart->discount = 0;
        $cart->coupon_id = null;
        $cart->save();
        
        return $this->successResponse(null, 'Cart cleared successfully');
    }

    /**
     * Apply coupon to cart
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function applyCoupon(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|exists:coupons,code',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422, $validator->errors());
        }

        $user = $request->user();
        $cart = Cart::where('user_id', $user->id)->first();
        
        if (!$cart) {
            return $this->errorResponse('Cart not found', 404);
        }
        
        $coupon = Coupon::where('code', $request->code)->first();
        
        // Check if coupon is valid
        if (!$coupon->isValid($cart->total)) {
            return $this->errorResponse('Coupon is invalid or expired', 422);
        }
        
        // Apply coupon to cart
        $cart->coupon_id = $coupon->id;
        $cart->save();
        
        // Recalculate cart total with coupon
        $cart->calculateTotal();
        
        return $this->successResponse(
            Cart::with(['items.product', 'coupon'])->find($cart->id),
            'Coupon applied successfully'
        );
    }

    /**
     * Remove coupon from cart
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeCoupon(Request $request)
    {
        $user = $request->user();
        $cart = Cart::where('user_id', $user->id)->first();
        
        if (!$cart) {
            return $this->errorResponse('Cart not found', 404);
        }
        
        // Remove coupon from cart
        $cart->coupon_id = null;
        $cart->save();
        
        // Recalculate cart total without coupon
        $cart->calculateTotal();
        
        return $this->successResponse(
            Cart::with(['items.product', 'coupon'])->find($cart->id),
            'Coupon removed successfully'
        );
    }
}