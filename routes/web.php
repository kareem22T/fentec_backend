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
});

Route::get('/', function () {
    return 'welcome';
});
