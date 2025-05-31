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
        $products = Product::with(['categories','variations'])->get();
        return $this->successResponse($products, 'Products retrieved successfully');
    }

    public function show($id)
    {
        $product = Product::with(['categories','variations'])->find($id);
        
        if (!$product) {
            return $this->errorResponse('Product not found', 404);
        }
        
        return $this->successResponse($product, 'Product retrieved successfully');
    }

    public function showbycategory($id)
    {
        $product = Product::whereHas(['categories','variations'], function($query) use ($id) {
            $query->where('categories.id', $id);
        })->with(['categories','variations'])->get();
        
        if (!$product) {
            return $this->errorResponse('Product not found', 404);
        }
        
        return $this->successResponse($product, 'Product retrieved successfully');
    }

}