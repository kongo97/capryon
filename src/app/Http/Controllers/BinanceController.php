<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BinanceService;

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

        $chart_data = BinanceService::getChart($crypto);

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
}
