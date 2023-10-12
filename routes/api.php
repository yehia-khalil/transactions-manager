<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\TransactionCategoryController;
use App\Http\Controllers\API\TransactionController;
use App\Http\Controllers\API\TransactionSubCategoryController;
use App\Models\TransactionSubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'authenticate']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('transactions', [TransactionController::class, 'index']);
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('transactions', TransactionController::class)->except('index');
        Route::apiResource('transaction_categories', TransactionCategoryController::class);
        Route::get('transaction_sub_categories', [TransactionSubCategoryController::class, 'index']);
        Route::apiResource('transaction_categories.transaction_sub_categories', TransactionSubCategoryController::class);
    });
});
