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

    public function show($id)
    {
        $product = Product::with('categories')->find($id);
        
        if (!$product) {
            return $this->errorResponse('Product not found', 404);
        }
        
        return $this->successResponse($product, 'Product retrieved successfully');
    }

    public function showbycategory($id)
    {
        $product = Product::with('categories')->where('category_id',$id);
        
        if (!$product) {
            return $this->errorResponse('Product not found', 404);
        }
        
        return $this->successResponse($product, 'Product retrieved successfully');
    }

}