<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ShippingMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    use ApiResponseTrait;

    /**
     * Display a listing of the user's orders.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $orders = Order::with(['items.product', 'address', 'shippingMethod', 'coupon'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
            
        return $this->successResponse($orders, 'Orders retrieved successfully');
    }

    /**
     * Store a newly created order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address_id' => 'required|exists:addresses,id',
            'shipping_method_id' => 'required|exists:shipping_methods,id',
            'payment_method' => 'required|string|in:credit_card,paypal,cash_on_delivery',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422, $validator->errors());
        }

        $user = $request->user();
        $cart = Cart::with('items.product')->where('user_id', $user->id)->first();
        
        if (!$cart || $cart->items->isEmpty()) {
            return $this->errorResponse('Cart is empty', 422);
        }
        
        // Check if all products are in stock
        foreach ($cart->items as $item) {
            if ($item->product->stock < $item->quantity) {
                return $this->errorResponse("Product '{$item->product->name}' is out of stock or not enough quantity available", 422);
            }
        }
        
        $shippingMethod = ShippingMethod::findOrFail($request->shipping_method_id);
        
        try {
            DB::beginTransaction();
            
            // Create order
            $order = Order::create([
                'user_id' => $user->id,
                'address_id' => $request->address_id,
                'shipping_method_id' => $request->shipping_method_id,
                'coupon_id' => $cart->coupon_id,
                'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'status' => 'pending',
                'subtotal' => $cart->total + $cart->discount, // Pre-discount total
                'shipping_cost' => $shippingMethod->price,
                'discount' => $cart->discount,
                'tax' => 0, // Can be calculated based on your tax rules
                'total' => $cart->total + $shippingMethod->price,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'notes' => $request->notes,
            ]);
            
            // Create order items
            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->discount_price ?? $item->product->price,
                    'total' => $item->quantity * ($item->product->discount_price ?? $item->product->price),
                ]);
                
                // Reduce product stock
                $product = $item->product;
                $product->stock -= $item->quantity;
                $product->save();
            }
            
            // Recalculate order total
            $order->calculateTotal();
            
            // Increment coupon usage if used
            if ($cart->coupon_id) {
                $coupon = $cart->coupon;
                $coupon->usage_count += 1;
                $coupon->save();
            }
            
            // Clear the cart
            CartItem::where('cart_id', $cart->id)->delete();
            $cart->total = 0;
            $cart->discount = 0;
            $cart->coupon_id = null;
            $cart->save();
            
            DB::commit();
            
            return $this->successResponse(
                Order::with(['items.product', 'address', 'shippingMethod', 'coupon'])->find($order->id),
                'Order created successfully',
                201
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to create order: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $order = Order::with(['items.product', 'address', 'shippingMethod', 'coupon'])
            ->where('user_id', $user->id)
            ->where('id', $id)
            ->first();
            
        if (!$order) {
            return $this->errorResponse('Order not found', 404);
        }
        
        return $this->successResponse($order, 'Order retrieved successfully');
    }

    /**
     * Cancel an order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancel(Request $request, $id)
    {
        $user = $request->user();
        $order = Order::with('items.product')
            ->where('user_id', $user->id)
            ->where('id', $id)
            ->first();
            
        if (!$order) {
            return $this->errorResponse('Order not found', 404);
        }
        
        // Check if order can be canceled
        if (!in_array($order->status, ['pending', 'processing'])) {
            return $this->errorResponse('Order cannot be canceled', 422);
        }
        
        try {
            DB::beginTransaction();
            
            // Update order status
            $order->status = 'canceled';
            $order->save();
            
            // Restore product stock
            foreach ($order->items as $item) {
                $product = $item->product;
                $product->stock += $item->quantity;
                $product->save();
            }
            
            // Decrement coupon usage if used
            if ($order->coupon_id) {
                $coupon = $order->coupon;
                $coupon->usage_count -= 1;
                $coupon->save();
            }
            
            DB::commit();
            
            return $this->successResponse($order, 'Order canceled successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to cancel order: ' . $e->getMessage(), 500);
        }
    }
}