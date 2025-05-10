<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    use ApiResponseTrait;

    /**
     * Display a listing of products.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $products = Product::with('categories')->get();
        return $this->successResponse($products, 'Products retrieved successfully');
    }

    /**
     * Store a newly created product.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'slug' => 'required|string|max:255|unique:products',
            'type' => 'required|string|max:255',
            'sku' => 'required|string|max:255|unique:products',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'sold_individually' => 'boolean',
            'stock_status' => 'required|string|max:255',
            'stock_qty' => 'required|integer|min:0',
            'total_sales' => 'integer|min:0',
            'categories' => 'array',
            'categories.*' => 'exists:categories,id',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422, $validator->errors());
        }

        $product = Product::create($request->except('categories'));
        
        // Attach categories if provided
        if ($request->has('categories')) {
            $product->categories()->attach($request->categories);
        }
        
        return $this->successResponse($product->load('categories'), 'Product created successfully', 201);
    }

    /**
     * Display the specified product.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $product = Product::with('categories')->find($id);
        
        if (!$product) {
            return $this->errorResponse('Product not found', 404);
        }
        
        return $this->successResponse($product, 'Product retrieved successfully');
    }

    /**
     * Update the specified product.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        
        if (!$product) {
            return $this->errorResponse('Product not found', 404);
        }
        
        $validator = Validator::make($request->all(), [
            'slug' => 'sometimes|required|string|max:255|unique:products,slug,' . $id,
            'type' => 'sometimes|required|string|max:255',
            'sku' => 'sometimes|required|string|max:255|unique:products,sku,' . $id,
            'price' => 'sometimes|required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'sold_individually' => 'boolean',
            'stock_status' => 'sometimes|required|string|max:255',
            'stock_qty' => 'sometimes|required|integer|min:0',
            'total_sales' => 'integer|min:0',
            'categories' => 'array',
            'categories.*' => 'exists:categories,id',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422, $validator->errors());
        }

        $product->update($request->except('categories'));
        
        // Sync categories if provided
        if ($request->has('categories')) {
            $product->categories()->sync($request->categories);
        }
        
        return $this->successResponse($product->load('categories'), 'Product updated successfully');
    }

    /**
     * Remove the specified product.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $product = Product::find($id);
        
        if (!$product) {
            return $this->errorResponse('Product not found', 404);
        }
        
        $product->categories()->detach();
        $product->delete();
        
        return $this->successResponse(null, 'Product deleted successfully');
    }

    /**
     * Get categories for a specific product.
     *
     * @param  int  $productId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProductCategories($productId)
    {
        $product = Product::find($productId);
        
        if (!$product) {
            return $this->errorResponse('Product not found', 404);
        }
        
        $categories = $product->categories;
        return $this->successResponse($categories, 'Product categories retrieved successfully');
    }
}