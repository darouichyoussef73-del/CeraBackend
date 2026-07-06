<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;

// Route::get('/', function () {
//     return view('welcome');
// });

// Simple API endpoints for signup and login
// Route::post('/api/signup', [AuthController::class, 'signup']);
// Route::post('/api/login', [AuthController::class, 'login']);

// Simple MVC listing for clients
Route::get('/clients', [ClientController::class, 'index']);
