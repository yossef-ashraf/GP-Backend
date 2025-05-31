<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        $user = $request->user();
        $cartItems = Cart::with(['product', 'variation'])->where('user_id', $user->id)->get();
        return $this->successResponse($cartItems, 'Cart items retrieved successfully');
    }

    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'product_id' => 'required|exists:products,id',
        'variation_id' => 'nullable|exists:product_variations,id',
        'quantity' => 'required|integer|min:1',
    ]);

    if ($validator->fails()) {
        return $this->errorResponse($validator->errors()->first(), 422);
    }

    $user = $request->user();
    $product = Product::findOrFail($request->product_id);

    // Look for existing cart item with same user, product, and variation
    $existingCartItem = Cart::where('user_id', $user->id)
        ->where('product_id', $product->id)
        ->where('variation_id', $request->variation_id)
        ->first();

    if ($existingCartItem) {
        // Check stock availability
        $newQuantity = $existingCartItem->quantity + $request->quantity;
        if ($product->stock_qty < $newQuantity) {
            return $this->errorResponse('Product is out of stock', 422);
        }

        // Update quantity
        $existingCartItem->quantity = $newQuantity;
        $existingCartItem->save();

        return $this->successResponse($existingCartItem->load(['product', 'variation']), 'Cart item quantity updated');
    } else {
        // New item
        if ($product->stock_qty < $request->quantity) {
            return $this->errorResponse('Product is out of stock', 422);
        }

        $cart = Cart::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'variation_id' => $request->variation_id,
            'quantity' => $request->quantity,
            'total' => 0,
        ]);

        return $this->successResponse($cart->load(['product', 'variation']), 'Cart item added successfully');
    }
}


    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422);
        }

        $user = $request->user();
        $cart = Cart::where('id', $id)->where('user_id', $user->id)->first();

        if (!$cart) {
            return $this->errorResponse('Cart item not found', 404);
        }

        if ($cart->product->stock_qty < $request->quantity) {
            return $this->errorResponse('Product is out of stock', 422);
        }

        $cart->quantity = $request->quantity;
        $cart->save();

        return $this->successResponse($cart->load(['product', 'variation']), 'Cart item updated successfully');
    }

    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $cart = Cart::where('id', $id)->where('user_id', $user->id)->first();

        if (!$cart) {
            return $this->errorResponse('Cart item not found', 404);
        }

        $cart->delete();

        return $this->successResponse(null, 'Cart item deleted successfully');
    }

    public function clear(Request $request)
    {
        $user = auth()->id();
        Cart::where('user_id',  $user)->delete();
        return $this->successResponse(null, 'Cart cleared successfully');
    }
}
