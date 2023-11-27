<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RegisterController;
use App\Http\Controllers\Admin\AdminHomeController;
use App\Http\Controllers\Admin\ManageUsersController;

Route::middleware(['admin_guest'])->group(function () {
    Route::get('/login', [RegisterController::class, 'getLoginIndex']);
    Route::post('/login', [RegisterController::class, 'login'])->name('admin.login');
});

Route::middleware('auth:admin')->group(function () {
    Route::get('/', [AdminHomeController::class, 'getIndex'])->name('admin.home');

    //coupon
    Route::post('/coupon/put', [AdminHomeController::class, 'addCoupon'])->name('coupon.put');

    // users
    Route::prefix('users')->group(function () {
        Route::get('/', [ManageUsersController::class, 'previewIndex'])->name('prev.users');
        Route::post('/', [ManageUsersController::class, 'getUsers'])->name('get.users');
        Route::post('/approve', [ManageUsersController::class, 'approve'])->name('user.approve');
        Route::post('/reject', [ManageUsersController::class, 'reject'])->name('user.reject');
    });

    //logout
    Route::get('/logout', [RegisterController::class, 'logout'])->name('admin.logout');
});