<?php

use App\Http\Controllers\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\InvoiceController;
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


Route::post('/register',[LoginController::class,'register']);

Route::get('test',[LoginController::class, 'test']);

Route::post('/send-otp', [LoginController::class, 'sendOtp']);

// Verify OTP
Route::post('/verify-otp', [LoginController::class, 'verifyOtp']);

// Resend OTP
Route::post('/resend-otp', [LoginController::class, 'resendOtp']);

// Protected routes
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/admin-only', function () {
        return 'Admin content';
    });
});

Route::middleware(['auth:sanctum', 'seller'])->group(function () {
    Route::get('/seller-only', function () {
        return 'Seller content';
    });

    Route::post('add-client', [ClientController::class, 'addClient']);
    Route::get('get-single-client/{id}', [ClientController::class, 'getSingleClient']);
    Route::get('get-all-client-data', [ClientController::class, 'getAllClientData']);
    Route::post('create-invoice', [InvoiceController::class, 'createInvoice']);
    Route::get('get-invoice-details/{id}', [InvoiceController::class, 'getInvoiceDetails']);
    Route::get('search-category', [CategoryController::class, 'searchCategory']);
    Route::post('add-category',[CategoryController::class,'addCategory']);
});

Route::middleware(['auth:sanctum', 'driver'])->group(function () {
    Route::get('/driver-only', function () {
        return 'Driver content';
    });
});