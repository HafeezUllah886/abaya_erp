<?php

use App\Http\Controllers\ManufacturingOrderController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    // New Combined Manufacturing Orders
    Route::get('/tailor_orders', [ManufacturingOrderController::class, 'index'])->name('tailor_orders.index');
    Route::get('/tailor_orders/create', [ManufacturingOrderController::class, 'create'])->name('tailor_orders.create');
    Route::post('/tailor_orders/store', [ManufacturingOrderController::class, 'store'])->name('tailor_orders.store');
    Route::get('/tailor_orders/{order}', [ManufacturingOrderController::class, 'show'])->name('tailor_orders.show');
    Route::get('/tailor_orders/{order}/edit', [ManufacturingOrderController::class, 'edit'])->name('tailor_orders.edit');
    Route::post('/tailor_orders/{order}/update', [ManufacturingOrderController::class, 'update'])->name('tailor_orders.update');
    Route::get('/tailor_orders/{order}/delete', [ManufacturingOrderController::class, 'destroy'])->name('tailor_orders.delete');
});
