<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IssueVoucherController;
use App\Http\Controllers\ReceiveVoucherController;
use App\Http\Controllers\ManufacturingOrderController;

Route::middleware('auth')->group(function () {
    // New Combined Manufacturing Orders
    Route::get('/manufacturing_orders', [ManufacturingOrderController::class, 'index'])->name('manufacturing_orders.index');
    Route::get('/manufacturing_orders/create', [ManufacturingOrderController::class, 'create'])->name('manufacturing_orders.create');
    Route::post('/manufacturing_orders/store', [ManufacturingOrderController::class, 'store'])->name('manufacturing_orders.store');
    Route::get('/manufacturing_orders/{order}', [ManufacturingOrderController::class, 'show'])->name('manufacturing_orders.show');
    Route::get('/manufacturing_orders/{order}/edit', [ManufacturingOrderController::class, 'edit'])->name('manufacturing_orders.edit');
    Route::post('/manufacturing_orders/{order}/update', [ManufacturingOrderController::class, 'update'])->name('manufacturing_orders.update');
    Route::get('/manufacturing_orders/{order}/delete', [ManufacturingOrderController::class, 'destroy'])->name('manufacturing_orders.delete');
    
    // Legacy Issue Vouchers
    Route::get('/issue_vouchers', [IssueVoucherController::class, 'index'])->name('issue_vouchers.index');
    Route::get('/issue_vouchers/create', [IssueVoucherController::class, 'create'])->name('issue_vouchers.create');
    Route::post('/issue_vouchers/store', [IssueVoucherController::class, 'store'])->name('issue_vouchers.store');
    Route::get('/issue_vouchers/{issueVoucher}/edit', [IssueVoucherController::class, 'edit'])->name('issue_vouchers.edit');
    Route::post('/issue_vouchers/{issueVoucher}/update', [IssueVoucherController::class, 'update'])->name('issue_vouchers.update');
    Route::get('/issue_vouchers/{issueVoucher}/delete', [IssueVoucherController::class, 'destroy'])->name('issue_vouchers.delete');
    Route::get('/issue_vouchers/{issueVoucher}', [IssueVoucherController::class, 'show'])->name('issue_vouchers.show');
    Route::get('/issue_vouchers/getproduct/{id}', [IssueVoucherController::class, 'getproduct']);

    // Legacy Receive Vouchers
    Route::get('/receive_vouchers', [ReceiveVoucherController::class, 'index'])->name('receive_vouchers.index');
    Route::get('/receive_vouchers/create', [ReceiveVoucherController::class, 'create'])->name('receive_vouchers.create');
    Route::post('/receive_vouchers/store', [ReceiveVoucherController::class, 'store'])->name('receive_vouchers.store');
    Route::get('/receive_vouchers/{receiveVoucher}/edit', [ReceiveVoucherController::class, 'edit'])->name('receive_vouchers.edit');
    Route::post('/receive_vouchers/{receiveVoucher}/update', [ReceiveVoucherController::class, 'update'])->name('receive_vouchers.update');
    Route::get('/receive_vouchers/{receiveVoucher}/delete', [ReceiveVoucherController::class, 'destroy'])->name('receive_vouchers.delete');
    Route::get('/receive_vouchers/{receiveVoucher}', [ReceiveVoucherController::class, 'show'])->name('receive_vouchers.show');
    Route::get('/receive_vouchers/getproduct/{id}', [ReceiveVoucherController::class, 'getproduct']);
});
