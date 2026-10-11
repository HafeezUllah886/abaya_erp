<?php

use App\Http\Controllers\SaleController;
use App\Http\Middleware\ConfirmPassword;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {

    Route::resource('customer-sales', SaleController::class)->parameters(['customer-sales' => 'sale']);

    Route::get('sales/getproduct/{id}', [SaleController::class, 'getSignleProduct']);
    Route::get('sale/delete/{id}', [SaleController::class, 'destroy'])->name('sales.delete')->middleware(ConfirmPassword::class);
    Route::get('sale/deliver/{id}', [SaleController::class, 'deliver'])->name('sale.deliver');
    Route::post('sale/deliver/{id}', [SaleController::class, 'storeDelivery'])->name('sale.deliver.store');

});
