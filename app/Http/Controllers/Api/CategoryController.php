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

    /**
     * Display a listing of categories.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $categories = Category::with(['parent', 'children'])->get();
        return $this->successResponse($categories, 'Categories retrieved successfully');
    }

    /**
     * Store a newly created category.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'parent_id' => 'nullable|exists:categories,id',
            'data' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422, $validator->errors());
        }

        $category = Category::create($request->all());
        return $this->successResponse($category, 'Category created successfully', 201);
    }

    /**
     * Display the specified category.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $category = Category::with(['parent', 'children', 'products'])->find($id);
        
        if (!$category) {
            return $this->errorResponse('Category not found', 404);
        }
        
        return $this->successResponse($category, 'Category retrieved successfully');
    }

    /**
     * Update the specified category.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $category = Category::find($id);
        
        if (!$category) {
            return $this->errorResponse('Category not found', 404);
        }
        
        $validator = Validator::make($request->all(), [
            'parent_id' => 'nullable|exists:categories,id',
            'data' => 'sometimes|required',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422, $validator->errors());
        }

        $category->update($request->all());
        return $this->successResponse($category, 'Category updated successfully');
    }

    /**
     * Remove the specified category.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $category = Category::find($id);
        
        if (!$category) {
            return $this->errorResponse('Category not found', 404);
        }
        
        // Check if category has children
        if ($category->children()->count() > 0) {
            return $this->errorResponse('Cannot delete category with children', 422);
        }
        
        $category->delete();
        return $this->successResponse(null, 'Category deleted successfully');
    }

    /**
     * Get products for a specific category.
     *
     * @param  int  $categoryId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCategoryProducts($categoryId)
    {
        $category = Category::find($categoryId);
        
        if (!$category) {
            return $this->errorResponse('Category not found', 404);
        }
        
        $products = $category->products;
        return $this->successResponse($products, 'Category products retrieved successfully');
    }
}