<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\SaleController;
use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Web\ProductController;
use App\Http\Controllers\Web\CategoryController;
use App\Http\Controllers\Web\CustomerController;
use App\Http\Controllers\Web\PurchaseController;
use App\Http\Controllers\Web\SupplierController;
use App\Http\Controllers\Web\StockDetailController;

Route::get('/', function () {
    return view('auth.login');
});

Route::post('login', [AuthController::class, 'login'])->name('login');

Route::middleware('api.auth.web')->group(function () {
    Route::get('/dashboard', function () {
        return view('welcome');
    })->name('dashboard');

    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::prefix('manage')->name('manage.')->group(function() {

        Route::resource('users', UserController::class);
        Route::resource('customers', CustomerController::class);
        Route::resource('suppliers', SupplierController::class);
        Route::resource('categories', CategoryController::class);
        Route::resource('products', ProductController::class);
        Route::resource('stock-details', StockDetailController::class);
        Route::resource('sales', SaleController::class);
        Route::resource('purchases', PurchaseController::class);
    });
});
