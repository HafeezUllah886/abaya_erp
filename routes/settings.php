<?php

use App\Http\Controllers\AttendantController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::post('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::get('/bookmarks', [AuthController::class, 'bookmarks'])->name('bookmarks');
    Route::post('/bookmarks', [AuthController::class, 'updateBookmarks'])->name('bookmarks.update');
});
