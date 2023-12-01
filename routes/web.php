<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\Seller\RegisterController as SellerRigisterController;
use ExpoSDK\ExpoMessage;
use ExpoSDK\Expo;

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
    Route::middleware('auth:sanctum')->post('/collect', [RegisterController::class, 'collectPoints']);
    Route::post('/login', [RegisterController::class, 'login']);
    Route::middleware('auth:sanctum')->post('/get-user', [RegisterController::class, 'getUser']);
    Route::middleware('auth:sanctum')->post('/send-code', [RegisterController::class, 'sendVerfication']);
    Route::middleware('auth:sanctum')->post('/active-account', [RegisterController::class, 'activeAccount']);
    Route::middleware('auth:sanctum')->post('/change-password', [RegisterController::class, 'changePassword']);
    Route::middleware('auth:sanctum')->post('/edit-email', [RegisterController::class, 'editEmail']);
    Route::middleware('auth:sanctum')->post('/edit-phone', [RegisterController::class, 'editPhone']);
    Route::middleware('auth:sanctum')->post('/seen-approving-msg', [RegisterController::class, 'seenApprovingMsg']);
    Route::middleware('auth:sanctum')->post('/logout', [RegisterController::class, 'logout']);
});

Route::group(['middleware' => ['check_api_password'], 'prefix' => 'sellers'], function () {
    Route::post('/register', [SellerRigisterController::class, 'register']);
    Route::post('/login', [SellerRigisterController::class, 'login']);
    Route::middleware('auth:sanctum')->post('/get-user', [SellerRigisterController::class, 'getUser']);
    Route::middleware('auth:sanctum')->post('/change-password', [SellerRigisterController::class, 'changePassword']);
    Route::middleware('auth:sanctum')->post('/edit-email', [SellerRigisterController::class, 'editEmail']);
    Route::middleware('auth:sanctum')->post('/edit-phone', [SellerRigisterController::class, 'editPhone']);
    Route::middleware('auth:sanctum')->post('/logout', [SellerRigisterController::class, 'logout']);
});

Route::get('/', function () {
    return 'welcome';
});

Route::get('/push', function () {
/**
 * Create messages fluently and/or pass attributes to the constructor
 */
    $expo = Expo::driver('file');
    $message = (new ExpoMessage([
        'title' => 'initial title',
        'body' => 'initial body',
    ]))
    ->setTitle('This title overrides initial title')
    ->setBody('This notification body overrides initial body')
    ->setData(['id' => 1])
    ->setChannelId('default')
    ->setBadge(0)
    ->playSound();

    $recipients = [
        'ExponentPushToken[vr6jOyOAhiawPxDD2xr9_A]',
    ];

    $response = $expo->send($message)->to($recipients)->push();

    return $response;
});
