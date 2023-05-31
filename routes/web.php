<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\User;
use \App\Http\Controllers\Crypto;
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

Route::post('user/register', [User::class, 'register']);
Route::post('user/login', [User::class, 'login']);
Route::group(['middleware' => 'valid.token'], function () {
    Route::post('crypto/exchangeRates', [Crypto::class, 'getCryptoExchangeRates']);
});

