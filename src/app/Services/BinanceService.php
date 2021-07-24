<?php

namespace App\Services;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Crypto;
use App\Models\Order;
use App\Services\Post;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BinanceService extends Command
{
    # get all crypto
    public static function getAllCrypto()
    {
        # ALL CRYPTO: {{binance}}/api/v3/exchangeInfo
        try {
            $response = Http::get(env('BINANCE_API')."/api/v3/exchangeInfo");
        }
        # connection error
        catch(GuzzleHttp\Exception\ConnectException $e) {
            Log::debug('Connection error', $e);
            return false;
        }
        # bad response error
        catch(GuzzleHttp\Exception\BadResponseException $e) {
            Log::debug('Response error', $e);
            return false;
        }
        # request error
        catch(GuzzleHttp\Exception\RequestException $e) {
            Log::debug('Request error', $e);
            return false;
        }

        # decode json response
        $json_response = json_decode($response->body(), true);

        # set response
        $response = [];

        # loop all crypto
        foreach($json_response["symbols"] as $crypto)
        {
            # symbol contains ***USDT 
            # symbol not contains ***DOWN 
            # symbol not contains ***UP
            if(strrpos($crypto["symbol"], "USDT") && !strrpos($crypto["symbol"], "DOWN", -3) && (!strrpos($crypto["symbol"], "UP", -2) && $crypto["symbol"] != "UP"))
            {
                # set cryopto name
                $name = str_replace("USDT", "", $crypto["symbol"]);

                # add crypto
                $response = [
                    "symbol" => $crypto["symbol"],
                    "name" => str_replace("USDT", "", $crypto["symbol"]),
                ];

                # add current crypto to good crypto
                Crypto::updateOrCreate($response, []);
            }
        }

        # return response
        return true;
    }

    # get current average price
    public static function getPrice($name = "")
    {
        # CURRENT AVERAGE PRICE: {{binance}}/api/v3/avgPrice?symbol={{ATAUSDT}}
        try 
        {
            $response = Http::get(env('BINANCE_API')."/api/v3/ticker/price?symbol=$name");
        }
        # connection error
        catch(GuzzleHttp\Exception\ConnectException $e) {
            Log::debug('Connection error', $e);
            return null;
        }
        # bad response error
        catch(GuzzleHttp\Exception\BadResponseException $e) {
            Log::debug('Response error', $e);
            return null;
        }
        # request error
        catch(GuzzleHttp\Exception\RequestException $e) {
            Log::debug('Request error', $e);
            return null;
        }
        # connection error
        catch(Illuminate\Http\Client\ConnectException $e) {
            Log::debug('Connection error', $e);
            return null;
        }
        # bad response error
        catch(Illuminate\Http\Client\BadResponseException $e) {
            Log::debug('Response error', $e);
            return null;
        }
        # request error
        catch(Illuminate\Http\Client\RequestException $e) {
            Log::debug('Request error', $e);
            return null;
        }
        # all
        catch(Exception $ex)
        {
            Log::debug('Error', $ex);
            return null;
        }

        # decode json response
        $json_response = json_decode($response->body(), true);

        # return response
        return $json_response;
    }

    # get price changes (24H)
    public static function getPriceChange($name = "")
    {
        # PRICE CHANGES: {{binance}}/api/v3/ticker/24hr?symbol={{ATAUSDT}}
        try {
            $response = Http::get(env('BINANCE_API')."/api/v3/ticker/24hr?symbol=$name");
        }
        # connection error
        catch(GuzzleHttp\Exception\ConnectException $e) {
            Log::debug('Connection error', $e);
            return false;
        }
        # bad response error
        catch(GuzzleHttp\Exception\BadResponseException $e) {
            Log::debug('Response error', $e);
            return false;
        }
        # request error
        catch(GuzzleHttp\Exception\RequestException $e) {
            Log::debug('Request error', $e);
            return false;
        }

        # decode json response
        $json_response = json_decode($response->body(), true);

        # return response
        return $json_response;
    }

    # get binance time
    public static function getTimestamp()
    {
        return Carbon::now()->timestamp * 1000;
    }

    # analyse crypto
    public static function analyse()
    {
        # get all crypto
        $all_crypto = Crypto::all()->toArray();

        # loop crypto
        foreach($all_crypto as $crypto)
        {
            # get current price changes
            $changes = BinanceService::getPriceChange($crypto["symbol"]);

            # get percent change (without number sign)
            $percent_change = $changes["priceChangePercent"] > 0 ? $changes["priceChangePercent"] : $changes["priceChangePercent"]*(-1);

            # get direction
            $direction = $changes["priceChangePercent"] > 0 ? "up" : "down";

            # get current crypto data
            $current_crypto = [
                "symbol" => $crypto["symbol"],
                "name" => $crypto["name"],
            ];

            # update current crypto data
            $current_crypto = Crypto::where('symbol', $crypto["symbol"])->first();
            $current_crypto->percent_change = $percent_change;
            $current_crypto->direction = $direction;
            $current_crypto->price = $changes["lastPrice"];
            $current_crypto->save();
        }

        # return response
        return true;
    }

    # get good crypto
    public static function getGood($limit=10)
    {
        # get all down crypto
        $crypto = Crypto::where("direction", "down")->get()->toArray();

        # set good crypto
        $good_crypto = [];

        # loop crypto
        foreach($crypto as $current)
        {
            # check percent change
            if($current["percent_change"] > $limit)
            {
                $good_crypto[] = $current;
            }
        }

        # return good crypto
        return $good_crypto;
    }
    
    # sell crypto
    public static function sell($name, $amount)
    {
        # ALL CRYPTO: {{binance}}/api/v3/order/test?symbol=BTCUSDT&side=SELL&type=LIMIT&timeInForce=GTC&quantity=0.01&price=9000&newClientOrderId=my_order_id_1&timestamp={{timestamp}}&signature={{signature}}
        try {
            # get current timestamp
            $params = [
                "symbol" => $name,
                "side" => "SELL",
                "type" => "MARKET",
                "quantity" => $amount,
                "timestamp" => BinanceService::getTimestamp(),
            ];

            $signature = BinanceService::getSignature($params);

            $query = $signature["query"];
            $signature = $signature["signature"];

            $params["signature"] = $signature;

            //die("/api/v3/order/test?symbol=$name&side=SELL&type=MARKET&timeInForce=GTC&quantity=$cash&newClientOrderId=my_order_id_1&timestamp=$timestamp&signature=$signature");


            $key = env('BINANCE_API_KEY');

            $response = Http::withHeaders(['X-MBX-APIKEY' => $key, 'Content-Type' => 'application/x-www-form-urlencoded'])->asForm()->post(env('BINANCE_API')."/api/v3/order", $params);
        }
        # connection error
        catch(GuzzleHttp\Exception\ConnectException $e) {
            Log::debug('Connection error', $e);
            return false;
        }
        # bad response error
        catch(GuzzleHttp\Exception\BadResponseException $e) {
            Log::debug('Response error', $e);
            return false;
        }
        # request error
        catch(GuzzleHttp\Exception\RequestException $e) {
            Log::debug('Request error', $e);
            return false;
        }

        # decode json response
        $json_response = json_decode($response->body(), true);

        # return response
        return $json_response;
    }
    
    # buy crypto
    public static function buy($name, $amount)
    {
        # ALL CRYPTO: {{binance}}/api/v3/order/test?symbol=BTCUSDT&side=SELL&type=LIMIT&timeInForce=GTC&quantity=0.01&price=9000&newClientOrderId=my_order_id_1&timestamp={{timestamp}}&signature={{signature}}
        try {
            # get current timestamp
            $params = [
                "symbol" => $name,
                "side" => "BUY",
                "type" => "MARKET",
                "quoteOrderQty" => $amount,
                "timestamp" => BinanceService::getTimestamp(),
            ];

            $signature = BinanceService::getSignature($params);

            $query = $signature["query"];
            $signature = $signature["signature"];

            $params["signature"] = $signature;

            //die("/api/v3/order/test?symbol=$name&side=SELL&type=MARKET&timeInForce=GTC&quantity=$cash&newClientOrderId=my_order_id_1&timestamp=$timestamp&signature=$signature");


            $key = env('BINANCE_API_KEY');

            $response = Http::withHeaders(['X-MBX-APIKEY' => $key, 'Content-Type' => 'application/x-www-form-urlencoded'])->asForm()->post(env('BINANCE_API')."/api/v3/order", $params);
        }
        # connection error
        catch(GuzzleHttp\Exception\ConnectException $e) {
            Log::debug('Connection error', $e);
            return false;
        }
        # bad response error
        catch(GuzzleHttp\Exception\BadResponseException $e) {
            Log::debug('Response error', $e);
            return false;
        }
        # request error
        catch(GuzzleHttp\Exception\RequestException $e) {
            Log::debug('Request error', $e);
            return false;
        }

        # decode json response
        $json_response = json_decode($response->body(), true);

        # return response
        return $json_response;
    }

    # get signature
    public static function getSignature($params = []) 
    {

        $query_array = array();
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                $query_array = array_merge($query_array, array_map(function ($v) use ($key) {
                    return urlencode($key) . '=' . urlencode($v);
                }, $value));
            } else {
                $query_array[] = urlencode($key) . '=' . urlencode($value);
            }
        }
        $query_string = implode('&', $query_array);

        return [
            "signature" => hash_hmac('sha256', $query_string, env("BINANCE_API_SECRET")),
            "query" => $query_string
        ];
    }

    # get percent change
    public static function getPercentChange($crypto = [])
    {
        $start = $crypto["start"];
        $last = $crypto["last"];

        $percent_change = (($last - $start)/ $start) * 100;

        return $percent_change;
    }

    # get next crypto rize
    public static function getRize($name='COMPUSDT', $cash=500)
    {
        # set crypto
        $crypto = [
            "name" => $name,
            "start" => 0,
            "last" => 0,
            "max" => 0,
            "percent_change" => 0,
        ];

        # get current price
        $price = BinanceService::getPrice($name);
        $crypto["start"] = $price["price"];
        $crypto["last"] = $price["price"];
        $crypto["max"] = $price["price"];

        
        # stop when crypto rize
        while(true)
        {
            Log::debug("Attend to first rize", $crypto);

            # get current price
            $price = BinanceService::getPrice($name);
            $crypto["last"] = $price["price"];
            $crypto["max"] = $crypto["max"] > $price["price"] ? $crypto["max"] : $price["price"];

            # register new order
            $order = new Order;
            $order->name = $crypto["name"];
            $order->in = $crypto["last"];
            $order->out = $crypto["last"];
            $order->earn = null;
            $order->save();

            # set percent change
            $crypto["percent_change"] = ($crypto["last"] - $crypto["start"]) / 100 * $crypto["start"];

            # UP
            if($crypto["percent_change"] > 1)
            {
                # get last order
                $order = Order::where("name", $name)->orderByDesc('id')->first();
                
                # FAKE UP
                if($order != null)
                {
                    # compare with current price
                    if($crypto["last"] < $order->out)
                    {
                        # register new order
                        $order = new Order;
                        $order->name = $crypto["name"];
                        $order->in = $crypto["last"];
                        $order->out = $crypto["last"];
                        $order->earn = null;
                        $order->save();

                        Log::debug("Last order was negative and current price is lower: Fake rize", $crypto);
                        return BinanceService::getRize($name, $cash);
                    }
                    # check last null earn
                    elseif($order->earn == null)
                    {
                        # compare with current price
                        if($crypto["last"] < $order->out)
                        {
                            # register new order
                            $order = new Order;
                            $order->name = $crypto["name"];
                            $order->in = $crypto["last"];
                            $order->out = $crypto["last"];
                            $order->earn = null;
                            $order->save();

                            Log::debug("Last order was null and current price is lower: Fake rize", $crypto);
                            return BinanceService::getRize($name, $cash);
                        }
                    }
                    elseif($crypto["last"] > $order->out)
                    {
                        Log::debug("Crypto upper than last price", $crypto);
                        break;
                    }
                }
            }
            # DOWN
            elseif($crypto["percent_change"] < -5)
            {
                # register new order
                $order = new Order;
                $order->name = $crypto["name"];
                $order->in = $crypto["last"];
                $order->out = $crypto["last"];
                $order->earn = null;
                $order->save();

                Log::debug("Crypto go down, going to attend new rize...", $crypto);
                return BinanceService::getRize($name, $cash);
            }
        }
        
        # get current price
        $price = BinanceService::getPrice($name);
        $crypto["last"] = $price["price"];
        $crypto["max"] = $crypto["max"] > $price["price"] ? $crypto["max"] : $price["price"];

        # set percent change
        $crypto["percent_change"] = ($crypto["last"] - $crypto["start"]) / 100 * $crypto["start"];

        $buy = BinanceService::buy($crypto["name"], $cash);
        Log::debug("Buy crypto", $buy);

        # update crypto status
        $crypto["amount"] = round($buy["executedQty"], 4, PHP_ROUND_HALF_DOWN);
        $crypto["last"] = $buy["fills"][0]["price"];
        $crypto["in"] = $buy["fills"][0]["price"];
        $crypto["start"] = $buy["fills"][0]["price"];
        $crypto["max"] = $buy["fills"][0]["price"];
        $crypto["lock"] = true;
        Log::debug("Update crypto status", $crypto);

        # return last crypto status
        return $crypto;
    }

    # get total amount
    public static function getTotalAmount()
    {
        # CURRENT AVERAGE PRICE: {{url}}/api/v3/account?timestamp={{timestamp}}&signature={{signature}}
        try {
            # get current timestamp
            $params = [
                "timestamp" => BinanceService::getTimestamp(),
            ];

            $signature = BinanceService::getSignature($params);

            $query = $signature["query"];
            $signature = $signature["signature"];

            $params["signature"] = $signature;

            $key = env('BINANCE_API_KEY');

            $response = Http::withHeaders(['X-MBX-APIKEY' => $key, 'Content-Type' => 'application/x-www-form-urlencoded'])->asForm()->get(env('BINANCE_API')."/api/v3/account", $params);
        }
        # connection error
        catch(GuzzleHttp\Exception\ConnectException $e) {
            Log::debug('Connection error', $e);
            return null;
        }
        # bad response error
        catch(GuzzleHttp\Exception\BadResponseException $e) {
            Log::debug('Response error', $e);
            return null;
        }
        # request error
        catch(GuzzleHttp\Exception\RequestException $e) {
            Log::debug('Request error', $e);
            return null;
        }
        # connection error
        catch(Illuminate\Http\Client\ConnectException $e) {
            Log::debug('Connection error', $e);
            return null;
        }
        # bad response error
        catch(Illuminate\Http\Client\BadResponseException $e) {
            Log::debug('Response error', $e);
            return null;
        }
        # request error
        catch(Illuminate\Http\Client\RequestException $e) {
            Log::debug('Request error', $e);
            return null;
        }
        # all
        catch(Exception $ex)
        {
            Log::debug('Error', $ex);
            return null;
        }

        # decode json response
        $json_response = json_decode($response->body(), true);

        foreach($json_response["balances"] as $balance)
        {
            if($balance["asset"] == "USDT")
            {
                $json_response = $balance["free"];
                break;
            }
        }

        # return response
        return $json_response;
    }

    # play with crypto
    public static function play($name = '', $cash = 500)
    {
        Log::debug("Start playng with $name [$cash]");

        # get next crypto rize
        $crypto = BinanceService::getRize($name, $cash);

        Log::debug("Get new crypto rize", $crypto);

        while(true)
        {
            Log::debug("Attend to new rize...");

            # get current price
            $price = BinanceService::getPrice($name);
            $crypto["last"] = $price["price"];
            $crypto["max"] = $crypto["max"] > $price["price"] ? $crypto["max"] : $price["price"];

            # set percent change
            $crypto["percent_change"] = ($crypto["last"] - $crypto["start"]) / 100 * $crypto["start"];
            
            # check crypto down
            if($crypto["percent_change"] < -0.5)
            {
                $earn = round($crypto["amount"] * $crypto["last"] - $cash, 4);

                if($crypto["percent_change"] < -20)
                {
                    if($crypto["lock"] === true)
                    {
                        # unlock sell
                        $crypto["lock"] = false;
                        Log::debug("Unlock sell...", $crypto);

                        continue;
                    }
                    
                    $earn = round($crypto["amount"] * $crypto["last"] - $cash, 4);

                    # BAD sell
                    $sell = BinanceService::sell($crypto["name"], $crypto["amount"]);
                    Log::debug("BAD sell...", $sell);

                    # register new order
                    $order = new Order;
                    $order->name = $crypto["name"];
                    $order->in = $crypto["in"];
                    $order->out = $crypto["last"];
                    $order->earn = $earn;
                    $order->save();

                    if(isset($sell["msg"]))
                    {
                        Log::debug("Error during sell...", $sell);
                        continue;
                    }

                    # new play
                    return BinanceService::play($name, $cash);
                }

                # register new order
                $order = new Order;
                $order->name = $crypto["name"];
                $order->in = $crypto["in"];
                $order->out = $crypto["last"];
                $order->earn = null;
                $order->save();

                Log::debug("Update crypto status", $crypto);

                continue;
            }

            # set max percent change
            $crypto["max_percent_change"] = ($crypto["last"] - $crypto["max"]) / 100 * $crypto["max"];
            
            # good sell
            if($crypto["percent_change"] > 10)
            {
                $earn = round($crypto["amount"] * $crypto["last"] - $cash, 4);

                // die("good sell");
                $sell = BinanceService::sell($crypto["name"], $crypto["amount"]);
                Log::debug("Good sell...", $sell);

                Log::debug("Update crypto status", $crypto);

                # register new order
                $order = new Order;
                $order->name = $crypto["name"];
                $order->in = $crypto["in"];
                $order->out = $crypto["last"];
                $order->earn = $earn;
                $order->save();

                if(isset($sell["msg"]))
                {
                    Log::debug("Error during sell...", $sell);
                    continue;
                }

                # new play
                return BinanceService::play($name, $cash);
            }

            Log::debug("Current crypto status", $crypto);
        }
    }

    # good buy method
    public static function goodBuy($name, $cash)
    {
        # get first price
        $order = Order::latest()->first();

        # 
        if($order != null && $order->out == null)
        {    
            # update crypto
            $crypto = [
                "name" => $order->name,
                "start" => $order->in,
                "last" => $order->in,
                "amount" => $order->amount,
                "max" => $order->in,
                "percent_change" => 0,
                "selling" => true
            ];

            # get current percent change
            $crypto["percent_change"] = BinanceService::getPercentChange($crypto);

            Log::debug("Restart from selling...");

            return $crypto;
        }

        # get first price
        $price = BinanceService::getPrice($name);

        # update crypto
        $crypto = [
            "name" => $name,
            "start" => $price["price"],
            "last" => $price["price"],
            "max" => $price["price"],
            "percent_change" => 0,
        ];

        # get current percent change
        $crypto["percent_change"] = BinanceService::getPercentChange($crypto);

        Log::debug("get first price", $crypto);

        # check percent change
        while($crypto["percent_change"] > -0.4)
        {
            try
            {
                # get first price
                $price = BinanceService::getPrice($name);
            }
            catch(Exception $ex)
            {
                Log::debug($ex);

                # skip
                continue;
            }

            if($price == null)
            {
                # skip
                continue;
            }

            # update crypto
            $crypto["last"] = $price["price"];
            $crypto["percent_change"] = BinanceService::getPercentChange($crypto);

            # -10
            if($crypto["percent_change"] < -10)
            {
                Log::debug("crypto is going down [-10%]", $crypto);
            }

            # -5
            elseif($crypto["percent_change"] < -5)
            {
                Log::debug("crypto is going down [-5%]", $crypto);
            }

            # 0
            elseif($crypto["percent_change"] < 0)
            {
                Log::debug("crypto is going down [-0%]", $crypto);
            }

            # 0.25
            elseif($crypto["percent_change"] > 0.25)
            {
                Log::debug("crypto is going up [+0.25%]", $crypto);

                return BinanceService::goodBuy($name, $cash);
            }

            else
            {
                Log::debug("crypto is going up [+".$crypto["percent_change"]."%]", $crypto);
            }

            # timeout
            sleep(10);
        }

        # buy crypto
        $buy = BinanceService::buy($crypto["name"], $cash);
        Log::debug("Buy crypto", $buy);

        # update crypto status
        $crypto["amount"] = $buy["executedQty"];
        $crypto["last"] = $buy["fills"][0]["price"];
        $crypto["in"] = $buy["fills"][0]["price"];
        $crypto["start"] = $buy["fills"][0]["price"];
        $crypto["max"] = $buy["fills"][0]["price"];
        Log::debug("Update crypto status", $crypto);

        # return last crypto status
        return $crypto;
    }

    # good sell method
    public static function goodSell($order_id)
    {
        # get first price
        $order = Order::where('id', $order_id)->first();

        # update crypto
        $crypto = [
            "name" => $order->name,
            "start" => $order->in,
            "last" => $order->in,
            "amount" => $order->amount,
            "max" => $order->in,
            "percent_change" => 0,
        ];

        # get current percent change
        $crypto["percent_change"] = BinanceService::getPercentChange($crypto);

        Log::debug("get first price", $crypto);

        # check percent change
        while($crypto["percent_change"] < 0.67)
        {
            # timeout
            sleep(10);

            try
            {
                # get first price
                $price = BinanceService::getPrice($order->name);
            }
            catch(Exception $ex)
            {
                Log::debug($ex);

                # skip
                continue;
            }

            if($price == null)
            {
                # skip
                continue;
            }

            # update crypto
            $crypto["last"] = $price["price"];
            $crypto["percent_change"] = BinanceService::getPercentChange($crypto);

            # -10
            if($crypto["percent_change"] < -10)
            {
                Log::debug("crypto is going down [-10%]", $crypto);
            }

            # -5
            elseif($crypto["percent_change"] < -5)
            {
                Log::debug("crypto is going down [-5%]", $crypto);
            }

            # 0
            elseif($crypto["percent_change"] < 0)
            {
                Log::debug("crypto is going down [-0%]", $crypto);
            }

            # 0.25
            elseif($crypto["percent_change"] > 0.25)
            {
                Log::debug("crypto is going up [+0.25%]", $crypto);
            }
            else
            {
                Log::debug("crypto is going up [+".$crypto["percent_change"]."%]", $crypto);
            }
        }

        # BAD sell
        $sell = BinanceService::sell($crypto["name"], $crypto["amount"]);
        Log::debug("Sell crypto...", $sell);

        # update crypto status
        $crypto["last"] = $sell["fills"][0]["price"];

        # get current percent change
        $crypto["percent_change"] = BinanceService::getPercentChange($crypto);

        $earn = round($sell["cummulativeQuoteQty"] - 300, 4, PHP_ROUND_HALF_DOWN);

        $order->out = $crypto["last"];
        $order->earn = $earn;
        $order->save();

        # return last crypto status
        return $crypto;
    }

    # bid method
    public static function bid($name, $cash)
    {
        # buy crypto
        $crypto = BinanceService::goodBuy($name, $cash);

        if(!isset($crypto["selling"]))
        {
            # register new order
            $order = new Order;
            $order->name = $crypto["name"];
            $order->in = $crypto["last"];
            $order->amount = $crypto["amount"];
            $order->out = null;
            $order->earn = null;
            $order->save();
        }
        else
        {
            # get first price
            $order = Order::latest()->first();
        }

        # sell crypto from id order
        $crypto = BinanceService::goodSell($order->id);

        return $crypto;
    }
}