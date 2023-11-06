<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RegisterController;
use App\Http\Controllers\Admin\AdminHomeController;

Route::middleware(['admin_guest'])->group(function () {
    Route::get('/login', [RegisterController::class, 'getLoginIndex']);
    Route::post('/login', [RegisterController::class, 'login'])->name('admin.login');
});

Route::middleware('auth:admin')->group(function () {
    Route::get('/', [AdminHomeController::class, 'getIndex'])->name('admin.home');

    //logout
    Route::get('/logout', [RegisterController::class, 'logout'])->name('admin.logout');
});