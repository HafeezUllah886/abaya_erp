<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IssueVoucherController;
use App\Http\Controllers\ReceiveVoucherController;

Route::middleware('auth')->group(function () {
    Route::get('/issue_vouchers', [IssueVoucherController::class, 'index'])->name('issue_vouchers.index');
    Route::get('/issue_vouchers/create', [IssueVoucherController::class, 'create'])->name('issue_vouchers.create');
    Route::post('/issue_vouchers/store', [IssueVoucherController::class, 'store'])->name('issue_vouchers.store');
    Route::get('/issue_vouchers/{issueVoucher}/edit', [IssueVoucherController::class, 'edit'])->name('issue_vouchers.edit');
    Route::post('/issue_vouchers/{issueVoucher}/update', [IssueVoucherController::class, 'update'])->name('issue_vouchers.update');
    Route::get('/issue_vouchers/{issueVoucher}/delete', [IssueVoucherController::class, 'destroy'])->name('issue_vouchers.delete');
    Route::get('/issue_vouchers/{issueVoucher}', [IssueVoucherController::class, 'show'])->name('issue_vouchers.show');
    Route::get('/issue_vouchers/getproduct/{id}', [IssueVoucherController::class, 'getproduct']);

    Route::get('/receive_vouchers', [ReceiveVoucherController::class, 'index'])->name('receive_vouchers.index');
    Route::get('/receive_vouchers/create', [ReceiveVoucherController::class, 'create'])->name('receive_vouchers.create');
    Route::post('/receive_vouchers/store', [ReceiveVoucherController::class, 'store'])->name('receive_vouchers.store');
    Route::get('/receive_vouchers/{receiveVoucher}/edit', [ReceiveVoucherController::class, 'edit'])->name('receive_vouchers.edit');
    Route::post('/receive_vouchers/{receiveVoucher}/update', [ReceiveVoucherController::class, 'update'])->name('receive_vouchers.update');
    Route::get('/receive_vouchers/{receiveVoucher}/delete', [ReceiveVoucherController::class, 'destroy'])->name('receive_vouchers.delete');
    Route::get('/receive_vouchers/{receiveVoucher}', [ReceiveVoucherController::class, 'show'])->name('receive_vouchers.show');
    Route::get('/receive_vouchers/getproduct/{id}', [ReceiveVoucherController::class, 'getproduct']);
});
