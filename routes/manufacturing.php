<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IssueVoucherController;
use App\Http\Controllers\ReceiveVoucherController;

Route::middleware('auth')->group(function () {
    Route::get('/issue_vouchers/create', [IssueVoucherController::class, 'create'])->name('issue_vouchers.create');
    Route::post('/issue_vouchers/store', [IssueVoucherController::class, 'store'])->name('issue_vouchers.store');
    Route::get('/issue_vouchers/getproduct/{id}', [IssueVoucherController::class, 'getproduct']);

    Route::get('/receive_vouchers/create', [ReceiveVoucherController::class, 'create'])->name('receive_vouchers.create');
    Route::post('/receive_vouchers/store', [ReceiveVoucherController::class, 'store'])->name('receive_vouchers.store');
    Route::get('/receive_vouchers/getproduct/{id}', [ReceiveVoucherController::class, 'getproduct']);
});
