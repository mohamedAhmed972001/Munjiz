<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ProfileController;

Route::prefix('v1')->group(function () {
    
  // --- Public Routes (مفتوحة للكل) ---
  Route::post('/register', [AuthController::class, 'register']);
  Route::post('/login', [AuthController::class, 'login']);

  // --- Protected Routes (للمسجلين فقط) ---
  Route::middleware('auth:sanctum')->group(function () {
      
      Route::post('/logout', [AuthController::class, 'logout']);
      
      // Test User Data
      Route::get('/user', function (Request $request) {
          return $request->user();
      });
      // Profile Routes
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/profile', [ProfileController::class, 'update']);
  });
});