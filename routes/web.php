<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::group(['middleware' => ['check_api_password']], function () {
    Route::post('/register', [RegisterController::class, 'register']);
    Route::middleware('auth:sanctum')->post('/register_2', [RegisterController::class, 'register2']);
    Route::post('/login', [RegisterController::class, 'login']);
    Route::middleware('auth:sanctum')->post('/get-user', [RegisterController::class, 'getUser']);
    Route::middleware('auth:sanctum')->post('/send-code', [RegisterController::class, 'sendVerfication']);
    Route::middleware('auth:sanctum')->post('/active-account', [RegisterController::class, 'activeAccount']);
    Route::middleware('auth:sanctum')->post('/change-password', [RegisterController::class, 'changePassword']);
    Route::middleware('auth:sanctum')->post('/edit-email', [RegisterController::class, 'editEmail']);
    Route::middleware('auth:sanctum')->post('/edit-phone', [RegisterController::class, 'editPhone']);
    Route::middleware('auth:sanctum')->post('/logout', [RegisterController::class, 'logout']);
});

Route::get('/', function () {
    return 'welcome';
});
