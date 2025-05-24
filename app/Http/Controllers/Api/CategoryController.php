<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        $categories = Category::with(['parent', 'children'])->get();
        return $this->successResponse($categories, 'Categories retrieved successfully');
    }
    public function show($id)
    {
        $category = Category::with(['parent', 'children', 'products'])->find($id);
        return $this->successResponse($category, 'Category retrieved successfully');
    }
    
}