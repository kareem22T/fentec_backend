<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RegisterController;
use App\Http\Controllers\Admin\AdminHomeController;
use App\Http\Controllers\Admin\ManageUsersController;
use App\Http\Controllers\Seller\ManageSellersController;

Route::middleware(['admin_guest'])->group(function () {
    Route::get('/login', [RegisterController::class, 'getLoginIndex']);
    Route::post('/login', [RegisterController::class, 'login'])->name('admin.login');
});
Route::post('/login-seller-marager', [RegisterController::class, 'sellersManagerLogin']);
Route::middleware('auth:sanctum')->post('/get-admin', [RegisterController::class, 'getAdmin']);
Route::middleware('auth:sanctum')->post('/get-sellers', [ManageSellersController::class, 'getSellers']);
Route::middleware('auth:sanctum')->post('/reload-seller-points', [ManageSellersController::class, 'reloadPoints']);
Route::middleware('auth:sanctum')->post('/delete-seller', [ManageSellersController::class, 'deleteSeller']);
Route::middleware('auth:sanctum')->post('/search-seller', [ManageSellersController::class, 'search']);
Route::middleware('auth:sanctum')->post('/create-seller', [ManageSellersController::class, 'create']);
Route::middleware('auth:sanctum')->post('/update-seller', [ManageSellersController::class, 'update']);

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