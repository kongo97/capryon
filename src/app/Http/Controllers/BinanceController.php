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
            "balance" => $balance <= 300 ? round($balance, 2, PHP_ROUND_HALF_DOWN) : round($balance, 2, PHP_ROUND_HALF_DOWN)
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
}
