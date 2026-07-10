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

// Auth endpoints that require session cookies / CSRF. Wrap with `web` so session and cookies are available.
Route::middleware('web')->group(function () {
    // Public auth endpoints (frontend should call /sanctum/csrf-cookie first)
    Route::post('/signup', [AuthController::class, 'signup']);
    Route::post('/login', [AuthController::class, 'login']);

    // Logout and user endpoints rely on session cookie
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');

    // Password reset endpoints
    Route::post('/password/forgot', [AuthController::class, 'forgotPassword']);
    Route::post('/password/reset', [AuthController::class, 'resetPassword']);
});
// Bookings: public read, but creation/modification requires authenticated client
Route::get('/bookings', [BookingController::class, 'index']);
Route::get('/bookings/{id}', [BookingController::class, 'show']);
// Protected booking routes (require session auth and client role) — use `web` middleware so session is available
Route::post('/bookings', [BookingController::class, 'store'])->middleware(['web','auth:sanctum', \App\Http\Middleware\ClientMiddleware::class]);
Route::put('/bookings/{id}', [BookingController::class, 'update'])->middleware(['web','auth:sanctum', \App\Http\Middleware\ClientMiddleware::class]);
Route::delete('/bookings/{id}', [BookingController::class, 'destroy'])->middleware(['web','auth:sanctum', \App\Http\Middleware\ClientMiddleware::class]);

// Public product endpoints (App clients will use these)
// Route::apiResource('/products', ProductsController::class);

// Admin API routes (unauthenticated for now — add middleware as needed)
// Admin API routes: require authenticated admin role; include `web` for session handling
Route::middleware(['web','auth:sanctum', \App\Http\Middleware\AdminMiddleware::class])->prefix('admin')->group(function () {
    Route::get('/stats/overview', [StatsController::class, 'overview']);
    Route::apiResource('/orders', OrdersController::class);
    Route::apiResource('/clients', ClientsController::class);
    Route::apiResource('/products', ProductsController::class);
    Route::apiResource('/payments', PaymentsController::class);
});
