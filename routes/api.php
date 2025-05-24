<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\AreaController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Authentication Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Authenticated Routes
Route::middleware('auth:sanctum')->group(function () {
    // User Routes
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/update-profile', [AuthController::class, 'updateProfile']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);

    // Address Routes
    Route::apiResource('addresses', AddressController::class)->except(['store']);
    Route::post('addresses', [AddressController::class, 'store'])->middleware('can:create,App\Models\Address');

    // Cart Routes
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'getCart']);
        Route::post('/add-item', [CartController::class, 'addItem']);
        Route::put('/update-item/{itemId}', [CartController::class, 'updateItem']);
        Route::delete('/remove-item/{itemId}', [CartController::class, 'removeItem']);
        Route::delete('/clear', [CartController::class, 'clearCart']);
        Route::post('/apply-coupon', [CartController::class, 'applyCoupon']);
        Route::post('/remove-coupon', [CartController::class, 'removeCoupon']);
    });

    // Order Routes
    Route::apiResource('orders', OrderController::class)->except(['update']);
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel']);
});

// Public Routes
Route::apiResource('areas', AreaController::class)->only(['index', 'show']);
Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);
Route::apiResource('products', ProductController::class)->only(['index', 'show']);
Route::get('/products/category/{id}', [ProductController::class, 'showbycategory']);

// Coupon Routes
Route::prefix('coupons')->group(function () {
    Route::get('/', [CouponController::class, 'index']);
    Route::get('/{id}', [CouponController::class, 'show']);
    Route::post('/validate', [CouponController::class, 'validate']);
});