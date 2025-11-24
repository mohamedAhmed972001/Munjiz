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
    // Project Routes
    // index & show متاح للكل (المستقلين والعملاء)
    Route::get('/projects', [ProjectController::class, 'index']); 
    Route::get('/projects/{project}', [ProjectController::class, 'show']); 
    // store متاح للعملاء فقط (التحقق داخل الكونترولر)
    Route::post('/projects', [ProjectController::class, 'store']); 

    // Bid Routes
    Route::post('/bids', [BidController::class, 'store']); // تقديم عرض (للمستقلين)
    Route::post('/bids/{bid}/accept', [BidController::class, 'accept']); // قبول عرض (للعملاء)
  });
});