<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ProductController;


use Illuminate\Http\Request;
use App\Http\Controllers\Admin\OrdersController;
use App\Http\Controllers\Admin\ClientsController;
use App\Http\Controllers\Admin\StatsController;
use App\Http\Controllers\Admin\ProductsController;
use App\Http\Controllers\Admin\PaymentsController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Here is where you register API routes for your application.
| These routes are loaded by the RouteServiceProvider within a group
| which is assigned the "api" middleware group.
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/bookings', [BookingController::class, 'store']);
Route::get('/bookings', [BookingController::class, 'index']);
Route::get('/bookings/{id}', [BookingController::class, 'show']);
Route::put('/bookings/{id}', [BookingController::class, 'update']);
Route::delete('/bookings/{id}', [BookingController::class, 'destroy']);

// Public product endpoints (App clients will use these)
// Route::apiResource('/products', ProductsController::class);

// Admin API routes (unauthenticated for now — add middleware as needed)
Route::prefix('admin')->group(function () {
    Route::get('/stats/overview', [StatsController::class, 'overview']);
    Route::apiResource('/orders', OrdersController::class);
    Route::apiResource('/clients', ClientsController::class);
    Route::apiResource('/products', ProductsController::class);
    Route::apiResource('/payments', PaymentsController::class);
});
// Route::get('/admin/orders', [OrdersController::class, 'index']);
// Route::get('/admin/orders/{id}', [OrdersController::class, 'show']);
// Route::post('/admin/orders', [OrdersController::class, 'store']);
// Route::put('/admin/orders/{id}/status', [OrdersController::class, 'updateStatus']);
// Route::delete('/admin/orders/{id}', [OrdersController::class, 'destroy']);


// use Illuminate\Support\Facades\Route;
// use Illuminate\Http\Request;
// use App\Http\Controllers\AuthController;
// use App\Http\Controllers\ProductController;
// use App\Http\Controllers\OrderController;
// use App\Http\Controllers\ClientController;

// // Admin Controllers
// use App\Http\Controllers\Admin\OrdersController;
// use App\Http\Controllers\Admin\ClientsController;
// use App\Http\Controllers\Admin\StatsController;
// use App\Http\Controllers\Admin\ProductsController;
// use App\Http\Controllers\Admin\PaymentsController;

// /*
// |--------------------------------------------------------------------------
// | PUBLIC ROUTES (No auth required)
// |--------------------------------------------------------------------------
// */
// Route::post('/signup', [AuthController::class, 'signup']);
// Route::post('/login', [AuthController::class, 'login']);

// /*
// |--------------------------------------------------------------------------
// | AUTHENTICATED ROUTES (All users - clients & admins)
// |--------------------------------------------------------------------------
// */
// Route::middleware('auth:sanctum')->group(function () {
    
//     // Get current user
//     Route::get('/user', function (Request $request) {
//         return $request->user();
//     });
    
//     // Logout
//     Route::post('/logout', [AuthController::class, 'logout']);
    
//     /*
//     | CLIENT ROUTES - User-specific actions
//     | These automatically use the authenticated user's ID
//     */
    
//     // User's own products
//     // Route::get('/my-products', [ProductController::class, 'myProducts']);
//     // Route::post('/products', [ProductController::class, 'store']);
//     // Route::put('/products/{id}', [ProductController::class, 'update']);
//     // Route::delete('/products/{id}', [ProductController::class, 'destroy']);
    
//     // // User's own orders
//     // Route::get('/my-orders', [OrderController::class, 'myOrders']);
//     // Route::post('/orders', [OrderController::class, 'store']);
    
//     // // User's own services
//     // Route::get('/my-services', [ServiceController::class, 'myServices']);
//     // Route::post('/services', [ServiceController::class, 'store']);
    
//     // Profile
//     Route::get('/profile', [AuthController::class, 'profile']);
//     Route::put('/profile', [AuthController::class, 'updateProfile']);
// });

// /*
// |--------------------------------------------------------------------------
// | ADMIN ROUTES (Auth + Admin role required)
// |--------------------------------------------------------------------------
// */
// Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    
//     Route::get('/stats/overview', [StatsController::class, 'overview']);
    
//     Route::apiResource('/orders', OrdersController::class);
//     Route::apiResource('/clients', ClientsController::class);
//     Route::apiResource('/products', ProductsController::class);
//     Route::apiResource('/payments', PaymentsController::class);
    
//     Route::put('/orders/{id}/status', [OrdersController::class, 'updateStatus']);
// });