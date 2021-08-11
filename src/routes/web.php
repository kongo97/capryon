<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BinanceController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('layouts/app', ['title' => 'Home', 'page' => 'index']);
});

Route::get('/play', function () {
    return view('play.bubble');
});

Route::get('/balance', function () {
    return view('balance.index');
});

Route::post('/price', [BinanceController::class, 'getPrice']);

Route::get('/_balance', [BinanceController::class, 'getBalance']);
