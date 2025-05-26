<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShippingMethod;
use App\Models\Coupon;

class OrderController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        $orders = $this->getUserOrders($request->user()->id);
        return $this->successResponse($orders, 'Orders retrieved successfully');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_method' => 'required|string|in:cash,credit_card',
            'address' => 'required|string',
            'area_id' => 'required|exists:areas,id',
            'coupon_code' => 'nullable|string|exists:coupons,code',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422, $validator->errors());
        }

        try {
            $order = $this->createOrder($request->user()->id, $request->all());
            return $this->successResponse($order, 'Order created successfully', 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create order: ' . $e->getMessage(), 500);
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $order = $this->getOrder(auth()->id(), $id);
            return $this->successResponse($order, 'Order retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }
    }

    public function cancel(Request $request, $id)
    {
        try {
            $order = $this->cancelOrder($request->user()->id, $id);
            return $this->successResponse($order, 'Order canceled successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }

    public function getUserOrders($userId)
    {
        return Order::with(['items.product', 'items.variation', 'coupon'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getOrder($userId, $orderId)
    {
        // dump($userId , $orderId);
        $order = Order::with(['items.product', 'items.variation', 'coupon'])
            ->where('user_id', $userId)
            ->where('id', (int) $orderId)
            ->first();

        if (!$order) {
            throw new \Exception('Order not found');
        }

        return $order;
    }

    public function createOrder($userId, array $data)
    {
        $cartItems = Cart::where('user_id', $userId)->with(['product', 'variation'])->get();
        if ($cartItems->isEmpty()) {
            throw new \Exception('Cart is empty');
        }

        // Calculate shipping cost based on area
        $shippingValue = \App\Models\ShippingValue::where('area_id', $data['area_id'])->first();
        $shippingCost = $shippingValue ? $shippingValue->value : 0;

        $coupon = null;
        if (isset($data['coupon_code'])) {
            $coupon = Coupon::where('code', $data['coupon_code'])->first();
            if (!$coupon || !$coupon->is_active ||
                ($coupon->valid_from && $coupon->valid_from->isFuture()) ||
                ($coupon->valid_to && $coupon->valid_to->isPast()) ||
                ($coupon->usage_limit && $coupon->usage_count >= $coupon->usage_limit)) {
                throw new \Exception('Invalid or expired coupon');
            }
        }

        foreach ($cartItems as $item) {
            if ($item->variation_id) {
                $variation = $item->variation()->lockForUpdate()->first();
                if (!$variation || $variation->stock_qty < $item->quantity) {
                    throw new \Exception("Product variation '{$variation->sku}' is out of stock or insufficient quantity");
                }
            } else {
                $product = $item->product()->lockForUpdate()->first();
                if (!$product || $product->stock_qty < $item->quantity) {
                    throw new \Exception("Product '{$product->name}' is out of stock or insufficient quantity");
                }
            }
        }

        try {
            DB::beginTransaction();

            $subtotal = $cartItems->sum(function ($item) {
                $price = $item->variation_id ? ($item->variation->sale_price ?? $item->variation->price) : ($item->product->discount_price ?? $item->product->price);
                return $item->quantity * $price;
            });

            $discount = 0;
            if ($coupon) {
                $discount = $coupon->discount_type === 'percentage'
                    ? $subtotal * ($coupon->discount_value / 100)
                    : $coupon->discount_value;

                $discount = min($discount, $subtotal);
                $subtotal -= $discount;
            }

            $order = Order::create([
                'user_id' => $userId,
                'address' => $data['address'],
                'coupon_id' => $coupon?->id,
                'tracking_number' => 'ORD-' . strtoupper(Str::random(10)),
                'total_amount' => $subtotal,
                'area_id' => $data['area_id'],
                'shipping_cost' => $shippingCost,
                'payment_method' => $data['payment_method'],
                'status' => $data['payment_method'] === 'cash' ? 'pending' : 'pre-pay',
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($cartItems as $item) {
                $price = $item->variation_id ? ($item->variation->sale_price ?? $item->variation->price) : ($item->product->sale_price ?? $item->product->price);
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'variation_id' => $item->variation_id,
                    'quantity' => $item->quantity,
                    'price' => $price,
                    'total_amount' => $item->quantity * $price,
                    'variation_data' => $item->variation_id ? $item->variation->variation_data : null,
                ]);

                if ($item->variation_id) {
                    $item->variation->decrement('stock_qty', $item->quantity);
                } else {
                    $item->product->decrement('stock_qty', $item->quantity);
                }
            }

            Cart::where('user_id', $userId)->delete();

            DB::commit();
            return Order::with(['area', 'coupon'])->find($order->id);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function cancelOrder($userId, $orderId)
    {
        $order = Order::with(['items.product', 'items.variation'])
            ->where('user_id', $userId)
            ->where('id', $orderId)
            ->first();

        if (!$order) {
            throw new \Exception('Order not found');
        }

        if (!in_array($order->status, ['pending', 'processing'])) {
            throw new \Exception('Order cannot be canceled');
        }

        try {
            DB::beginTransaction();

            $order->status = 'canceled';
            $order->save();

            foreach ($order->items as $item) {
                if ($item->variation_id) {
                    $item->variation->increment('stock_qty', $item->quantity);
                } else {
                    $item->product->increment('stock_qty', $item->quantity);
                }
            }

            DB::commit();
            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
