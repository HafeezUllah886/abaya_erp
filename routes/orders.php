<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderFulfillmentController;

Route::middleware('auth')->group(function () {
    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders/store', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/getproduct/{id}', [OrderController::class, 'getproduct']);

    Route::get('/fulfillments/create', [OrderFulfillmentController::class, 'create'])->name('fulfillments.create');
    Route::post('/fulfillments/store', [OrderFulfillmentController::class, 'store'])->name('fulfillments.store');
    Route::get('/fulfillments/getorder/{id}', [OrderFulfillmentController::class, 'getorder']);
});
