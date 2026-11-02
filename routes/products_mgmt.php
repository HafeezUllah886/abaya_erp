<?php

use App\Http\Controllers\ModelsController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\StockController;
use App\Http\Middleware\ConfirmPassword;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::resource('stock', StockController::class);
    Route::resource('products', ProductsController::class);
    Route::resource('models', ModelsController::class);

    Route::resource('stock-adjustments', StockAdjustmentController::class);
    Route::get('stock-adjustment/delete/{ref}', [StockAdjustmentController::class, 'destroy'])->name('stock-adjustment.delete')->middleware(ConfirmPassword::class);

});
