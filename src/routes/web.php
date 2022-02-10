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

Route::get('/market', [BinanceController::class, 'market']);

Route::get('/play', function () {
    return view('play.bubble');
});

Route::get('/balance', function () {
    return view('balance.index');
});

Route::get('/charts/{crypto}', function ($crypto) {
    return view('charts.index', ['crypto' => $crypto]);
});

Route::post('/price', [BinanceController::class, 'getPrice']);

Route::get('/_balance', [BinanceController::class, 'getBalance']);

Route::get('/chart/{crypto}', [BinanceController::class, 'getChart']);

Route::get('/controller', function () {
    return view('controller.index');
});

Route::get('/predict', [BinanceController::class, 'predict']);

Route::get('/control', [BinanceController::class, 'getController']);

Route::get('/play/{crypto}', [BinanceController::class, 'play']);

Route::get('/dailyUp', [BinanceController::class, 'dailyUp']);

Route::get('/quick', [BinanceController::class, 'quick']);

Route::get('/crypto/{crypto}', [BinanceController::class, 'monitor']);

# API

Route::get('/api/tradeList/{crypto}', [BinanceController::class, 'tradeList']);

Route::get('/api/updateHistory_15m/{crypto}', [BinanceController::class, 'updateHistory_15m']);

Route::get('/api/updateHistory_1h/{crypto}', [BinanceController::class, 'updateHistory_1h']);

Route::get('/api/price/{crypto}', [BinanceController::class, 'price']);

Route::get('/api/balance', [BinanceController::class, 'getBalance']);

Route::get('/api/amount/{crypto}', [BinanceController::class, 'getAmount']);

Route::get('/buy/{crypto}', [BinanceController::class, 'buy']);

Route::get('/sell/{crypto}', [BinanceController::class, 'sell']);

Route::get('/follow', [BinanceController::class, 'follow']);