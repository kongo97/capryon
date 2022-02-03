<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BinanceService;
use App\Services\CapryonService;
use App\Models\Crypto;

class BinanceController extends Controller
{
    public function getPrice(Request $request)
    {
        # set default response
        $response = [
            "error" => true,
            "data" => []
        ];

        $name = $request->post("name");

        $price = BinanceService::getPrice($name);

        if($price == null)
        {
            return json_encode($response);
        }

        $response = [
            "error" => false,
            "data" => $price
        ];

        return json_encode($response);
    }

    public static function getChart($crypto)
    {
        # set default response
        $response = [
            "error" => true,
            "data" => []
        ];

        $chart_data = BinanceService::getChart($crypto, "1m", 60);

        if($chart_data === false)
        {
            return json_encode($response);
        }

        $response = [
            "error" => false,
            "data" => $chart_data
        ];

        return json_encode($response);
    }

    public function getBalance(Request $request)
    {
        # set default response
        $response = [
            "error" => true,
            "data" => []
        ];

        $balance = BinanceService::getTotalAmount();

        if($balance == null)
        {
            return json_encode($response);
        }

        $response = [
            "error" => false,
            "balance" => round($balance, 2, PHP_ROUND_HALF_DOWN)
        ];

        return json_encode($response);
    }
    
    public function getAmount($crypto)
    {
        $crypto = Crypto::all()->where('name', $crypto)->first();

        # set default response
        $response = [
            "error" => true,
            "data" => []
        ];

        $amount = $crypto != null ? BinanceService::amount($crypto->name) : round(BinanceService::amount("usdt"), 2);

        if($amount === null)
        {
            return json_encode($response);
        }

        $response = [
            "error" => false,
            "amount" => $amount
        ];

        return json_encode($response);
    }

    public function play($crypto)
    {
        # play
        BinanceService::bid($crypto, 300);

        return true;
    }

    public function getController()
    {
        # set default response
        $response = [
            "error" => true,
            "data" => []
        ];

        $chart_data = BinanceService::getControl("5m", 20);

        if($chart_data === false)
        {
            return json_encode($response);
        }

        $response = [
            "error" => false,
            "data" => json_encode($chart_data)
        ];

        return json_encode($response);
    }

    public function predict()
    {
        # set default response
        $response = [
            "error" => true,
            "data" => []
        ];

        $cryptos = BinanceService::getAllCrypto();

        if($cryptos === false)
        {
            return false;
        }

        $histories = [];

        foreach($cryptos as $crypto)
        {
            $history = BinanceService::history($crypto["symbol"], "5m", 1);

            if($crypto["name"]!="STORM" && $crypto["name"]!="BCHSV")
            {
                $histories[$crypto["name"]] = $history;
            }
        }

        $keys = array_column($histories, 'percent_change');
        array_multisort($keys, SORT_DESC, $histories);

        //return json_encode(last($histories));

        return view('predict.index', ["currencies" => $cryptos, "histories" => $histories]);
    }

    public function market()
    {
        $daily_up = CapryonService::daily();

        return view('layouts/app', ['title' => 'Market', 'page' => 'dailyUp', 'dailyUp' => $daily_up]);
    }

    public function dailyUp()
    {
        $daily_up = CapryonService::dailyUp();

        return view('layouts/app', ['title' => 'Daily-Up', 'page' => 'dailyUp', 'dailyUp' => $daily_up]);
    }

    public function quick()
    {
        $daily_up = CapryonService::quick();

        return view('layouts/app', ['title' => 'Quick', 'page' => 'dailyUp', 'dailyUp' => $daily_up]);
    }

    public function monitor($crypto)
    {
        $crypto = Crypto::all()->where('name', $crypto)->first();

        $trade_list = CapryonService::tradeList($crypto->symbol);

        return view('layouts/app', [
            'title' => strtoupper($crypto->name), 
            'page' => 'monitor', 
            'crypto' => $crypto, 
            "trade_list" => $trade_list, 
            "buyers" => $trade_list["count"]['buyers'], 
            "sellers" => $trade_list["count"]['sellers'],
            "history_15m" => json_decode($crypto->history_15m, true),
            "history_1h" => json_decode($crypto->history_1h, true),
            "balance_crypto" => BinanceService::amount($crypto->name),
            "balance_usdt" => round(BinanceService::amount("usdt"), 2),
        ]);
    }

    public function tradeList($crypto)
    {
        $crypto = Crypto::all()->where('name', $crypto)->first();

        $trade_list = CapryonService::tradeList($crypto->symbol);

        return json_encode($trade_list);
    }

    public function updateHistory_15m($crypto)
    {
        $crypto = Crypto::all()->where('name', $crypto)->first();

        // get 24h history (split 1h)
        $history = BinanceService::history($crypto["symbol"], "1m", 60);

        return json_encode($history);
    }

    public function updateHistory_1h($crypto)
    {
        $crypto = Crypto::all()->where('name', $crypto)->first();
        
        // get 24h history (split 1h)
        $history = BinanceService::history($crypto["symbol"], "1h", 1);

        return json_encode($history);
    }

    public function price($crypto)
    {
        $crypto = Crypto::all()->where('name', $crypto)->first();
        
        // get 24h history (split 1h)
        $price = BinanceService::getPrice($crypto->symbol);

        $price["time"] = date('Y/m/d H:i:s');
        $price["price"] = rtrim($price["price"], 0);

        return json_encode($price);
    }

    public function buy($crypto)
    {
        $crypto = Crypto::all()->where('name', $crypto)->first();

        $amount = round(BinanceService::amount("usdt"), 0, PHP_ROUND_HALF_DOWN);

        // get 24h history (split 1h)
        $price = BinanceService::getPrice($crypto->symbol);
        $price = $price["price"];

        $amount = $amount / $price;

        $i = round($amount / $crypto->stepSize);
        $diff = $amount - $i * $crypto->stepSize;

        $amount = $amount - $diff;
        
        $buy = BinanceService::buy($crypto->symbol, $amount, $price);

        if(!$buy)
        {
            return json_encode(["error" => true]);
        }

        return json_encode(["error" => false, "buy" => $buy]);
    }

    public function sell($crypto)
    {
        $crypto = Crypto::all()->where('name', $crypto)->first();

        $amount = BinanceService::amount($crypto->name);

        $i = round($amount / $crypto->stepSize);
        $diff = $amount - $i * $crypto->stepSize;

        $amount = $amount - $diff;

        // get 24h history (split 1h)
        $price = BinanceService::getPrice($crypto->symbol);
        $price = $price["price"];
        
        $sell = BinanceService::sell($crypto->symbol, $amount, $price);

        if(!$sell)
        {
            return json_encode(["error" => true]);
        }

        return json_encode(["error" => false, "sell" => $sell]);
    }
}
