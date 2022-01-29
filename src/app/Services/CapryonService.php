<?php

namespace App\Services;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Crypto;
use App\Models\Order;
use App\Services\Post;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Services\BinanceService;
use \parallel\Runtime;

class CapryonService extends Command
{
    # get all crypto
    public static function allCrypto()
    {
        $cryptos = BinanceService::getAllCrypto();

        foreach($cryptos as $crypto)
        {
            Crypto::create([
                'name' => strtolower($crypto["name"]),
                'symbol' => strtolower($crypto["symbol"]),
            ]);
        }

        return Crypto::all();
    }

    # update crypto up
    public static function dailyUpdate()
    {
        $cryptos = Crypto::all();

        foreach($cryptos as $crypto)
        {
            $daily = CapryonService::_dailyUpdate($crypto["symbol"]);

            if(!$daily || $daily['start'] == 0)
            {
                $crypto->isDailyUpdated = false;
                continue;
            }
            else
            {
                $delta_percent = round((($daily['price'] - $daily['start']) / $daily['start']) * 100, 2);

                $crypto->isDailyUpdated = true;
                $crypto->isDailyUp = $delta_percent > 1 ? true : false;
                $crypto->start = $daily["start"];
                $crypto->price = $daily["price"];
                $crypto->delta_percent = $delta_percent;
                $crypto->min = $daily["min"];
                $crypto->max = $daily["max"];

                // get 24h history (split 1h)
                $history = BinanceService::history($crypto["symbol"], "1h", 24);

                // get last h history (split 1h)
                $last = BinanceService::history($crypto["symbol"], "3m", 20);

                $crypto->history_24h = json_encode($history);
                $crypto->history_1h = json_encode($last);
            }

            $crypto->save();
        }

        return Crypto::all();
    }

    # update crypto up
    public static function quickUpdate()
    {
        $cryptos = Crypto::all();

        foreach($cryptos as $crypto)
        {
            // get 24h history (split 1h)
            $history = BinanceService::history($crypto["symbol"], "1m", 15);

            $crypto->history_15m = json_encode($history);

            $delta_percent = ($history['history'][count($history['history'])-1]['close'] - $history['history'][0]['open']) / $history['history'][0]['open'] * 100;

            if(( ($delta_percent <= -2 && $crypto->delta_percent > 5 ) || (($crypto->max - $history['history'][count($history['history'])-1]['close'] > $history['history'][count($history['history'])-1]['close'] - $crypto->min) && $delta_percent <= -2)) && $crypto->isDailyUpdated == true ) 
            {
                $crypto->isQuick = true;
            }
            else
            {
                $crypto->isQuick = false;
            }

            $crypto->save();
        }

        return Crypto::all();
    }

    # is crypto daily-up?
    public static function _dailyUpdate($symbol)
    {
        $symbol = strtoupper($symbol);

        // get percent change (from 00:00 to now) 
        try
        {
            $price_change = BinanceService::getPriceChange($symbol);
        }
        catch(Exception $ex)
        {
            return false;
        }

        $percent_change = round($price_change["priceChangePercent"], 2);

        $up_crypto = [
            "symbol" => $symbol,
            "delta" => $percent_change,
            "max" => $price_change["highPrice"],
            "min" => $price_change["lowPrice"],
            "start" => $price_change["openPrice"],
            "price" => $price_change["lastPrice"],
        ];

        return $up_crypto;
    }

    # get all crypto
    public static function daily()
    {
        return Crypto::all()->where('isDailyUpdated', true)->sortBy('delta_percent');
    }

    # get all crypto
    public static function dailyUp()
    {
        return Crypto::all()->where('isDailyUp', true)->sortBy('delta_percent');
    }

    # get all crypto
    public static function quick()
    {
        return Crypto::all()->where('isQuick', true)->sortBy('delta_percent');
    }
























    # earn with best crypto
    public static function earn($cash = 300)
    {
        # get latest order
        $order = CapryonService::latestOrder();

        # restart from selling
        if($order != null)
        {
            # close order
            return CapryonService::sell();
        }
        else
        {
            # get best crypto
            $best = CapryonService::getBest();

            if($best != null)
            {
                Log::debug("Try to play with ".$best["name"]);

                # set crypto
                $crypto = $best["name"];
            }
            else
            {
                Log::debug("No crypto found...");
                return CapryonService::earn();
            }
        }

        # get first price
        $first_price = BinanceService::getPrice($crypto);

        # set status
        $status = [
            "name" => $crypto,
            "price" => $first_price["price"],
            "percent_change" => 0,
            "min" => $first_price["price"],
            "max" => $first_price["price"],
            "start" => $first_price["price"],
            "up" => 0,
            "down" => 0,
        ];

        # trend counter
        $trend_counter = [
            "up" => 0,
            "down" => 0,
        ];

        while(true)
        {
            # timeout
            //sleep(1);

            # get current price
            try
            {
                $price = BinanceService::getPrice($crypto);
            }
            catch(Exception $ex)
            {
                continue;
            }

            # percent change
            $percent_change = (($price["price"] - $status["start"])/ $status["start"]) * 100;

            # min
            $min = $price["price"] < $status["min"] ? $price["price"] : $status["min"];

            # max
            $max = $price["price"] > $status["max"] ? $price["price"] : $status["max"];

            # compare prices
            if($price["price"] > $first_price["price"])
            {
                # COUNTER CHECK
                # TRYWIN
                if($trend_counter["down"] >= 2)
                {
                    Log::debug("TRYBUY", ["percent_change" => $percent_change]);

                    # buy crypto
                    $buy = BinanceService::buy($crypto, $cash);
                    Log::debug("BUY", $buy);

                    # register new order
                    $order = new Order;
                    $order->name = $crypto;
                    $order->in = $buy["fills"][0]["price"];
                    $order->amount = $buy["executedQty"];
                    $order->out = null;
                    $order->earn = null;
                    $order->save();

                    Log::debug("Selling...", ["name" => $crypto]);

                    return true;
                }
                else
                {
                    # get best crypto
                    $best = CapryonService::getBest();

                    if($best != null)
                    {
                        Log::debug("Try to play with ".$best["name"]);

                        # set crypto
                        $crypto = $best["name"];

                        # check crypto change
                        if($status["name"] != $crypto)
                        {
                            Log::debug("Changing crypto...");
                            return CapryonService::earn();    
                        }
                    }
                    else
                    {
                        Log::debug("No crypto found...");
                        return CapryonService::earn();
                    }
                }

                # COUNTER
                # increment counter up
                $trend_counter["up"] = $trend_counter["up"] + 1;

                # reset counter down
                $trend_counter["down"] = 0;

                # CRYPTO STATUS
                # set status
                $status = [
                    "name" => $crypto,
                    "price" => $price["price"],
                    "percent_change" => $percent_change,
                    "min" => $min,
                    "max" => $max,
                    "start" => $status["start"],
                    "x_up" => $trend_counter["up"],
                    "x_down" => $trend_counter["down"]
                ];

                # first up
                if($trend_counter["up"] == 1)
                {
                    # percent change
                    $percent_change = (($price["price"] - $first_price["price"])/ $first_price["price"]) * 100;

                    # CRYPTO STATUS
                    # set status
                    $status = [
                        "name" => $crypto,
                        "price" => $price["price"],
                        "percent_change" => $percent_change,
                        "min" => $min,
                        "max" => $max,
                        "start" => $first_price["price"],
                        "x_up" => $trend_counter["up"],
                        "x_down" => $trend_counter["down"]
                    ];
                }

                # LOG
                Log::debug("up", $status);
            }
            elseif($price["price"] == $first_price["price"])
            {
                // Log::debug("...");
            }
            else
            {
                # COUNTER
                # increment counter up
                $trend_counter["down"] = $trend_counter["down"] + 1;

                # reset counter down
                $trend_counter["up"] = 0;

                # CRYPTO STATUS
                # set status
                $status = [
                    "name" => $crypto,
                    "price" => $price["price"],
                    "percent_change" => $percent_change,
                    "min" => $min,
                    "max" => $max,
                    "start" => $status["start"],
                    "x_up" => $trend_counter["up"],
                    "x_down" => $trend_counter["down"]
                ];

                # first down
                if($trend_counter["down"] == 1)
                {
                    # percent change
                    $percent_change = (($price["price"] - $first_price["price"])/ $first_price["price"]) * 100;

                    # CRYPTO STATUS
                    # set status
                    $status = [
                        "name" => $crypto,
                        "price" => $price["price"],
                        "percent_change" => $percent_change,
                        "min" => $min,
                        "max" => $max,
                        "start" => $first_price["price"],
                        "x_up" => $trend_counter["up"],
                        "x_down" => $trend_counter["down"]
                    ];
                }

                # LOG
                Log::debug("down", $status);
            }

            # price (now) is new first price
            $first_price = $price;
        }

        return true;
    }

    # sell
    public static function sell()
    {
        # get latest order
        $order = Order::latest()->first();

        Log::debug("restart from selling...", json_decode($order, true));

        # set crypto name
        $crypto = $order->name;

        # get crypto buy price
        $start_price = $order->in;

        # get current price
        try
        {
            $price = BinanceService::getPrice($crypto);
        }
        catch(Exception $ex)
        {
            return CaprionService::sell();
        }

        # percent change
        $percent_change = (($price["price"] - $start_price) / $start_price) * 100;

        # no sell
        while($percent_change < 0.2)
        {
            # timeout
            sleep(1);

            # get current price
            try
            {
                $price = BinanceService::getPrice($crypto);
            }
            catch(Exception $ex)
            {
                continue;
            }

            # percent change
            $percent_change = (($price["price"] - $start_price) / $start_price) * 100;

            # LOG
            Log::debug("Selling", ["name" => $crypto, "percent_change" => $percent_change]);
        }

        # WIN
        if($percent_change >= 0.2)
        {
            # sell crypto
            $sell = BinanceService::sell($crypto, $order->amount);
            Log::debug("SOLD", $sell);

            # get earn
            $earn = round($order->amount * $price["price"] - 300, 4);

            # close order
            $order->name = $crypto;
            $order->out = $price["price"];
            $order->earn = $earn;
            $order->save();

            Log::debug("WIN", ["name" => $crypto, "percent_change" => $percent_change]);

            return true;
        }

        return CapryonService::sell();
    }

    # get latest order
    public static function latestOrder()
    {
        $order = Order::latest()->first();

        if($order != null && $order->out == null && $order->amount != 0)
        {
            return $order->name;
        }

        return null;
    }

    # get crypto trend
    public static function getTrend($crypto)
    {
        # get first price
        $first_price = BinanceService::getPrice($crypto);

        # set status
        $status = [
            "name" => $crypto,
            "price" => $first_price["price"],
            "percent_change" => 0,
            "min" => $first_price["price"],
            "max" => $first_price["price"],
            "start" => $first_price["price"],
            "up" => 0,
            "down" => 0,
        ];

        # trend counter
        $trend_counter = [
            "up" => 0,
            "down" => 0,
        ];

        while(true)
        {
            # timeout
            //sleep(1);

            # get current price
            try
            {
                $price = BinanceService::getPrice($crypto);
            }
            catch(Exception $ex)
            {
                continue;
            }

            # percent change
            $percent_change = (($price["price"] - $status["start"])/ $status["start"]) * 100;

            # min
            $min = $price["price"] < $status["min"] ? $price["price"] : $status["min"];

            # max
            $max = $price["price"] > $status["max"] ? $price["price"] : $status["max"];

            # compare prices
            if($price["price"] > $first_price["price"])
            {
                # COUNTER CHECK
                # TRYWIN
                if($trend_counter["down"] >= 4)
                {
                    Log::debug("TRYBUY", ["percent_change" => $percent_change]);
                }

                # COUNTER
                # increment counter up
                $trend_counter["up"] = $trend_counter["up"] + 1;

                # reset counter down
                $trend_counter["down"] = 0;

                # CRYPTO STATUS
                # set status
                $status = [
                    "name" => $crypto,
                    "price" => $price["price"],
                    "percent_change" => $percent_change,
                    "min" => $min,
                    "max" => $max,
                    "start" => $status["start"],
                    "x_up" => $trend_counter["up"],
                    "x_down" => $trend_counter["down"]
                ];

                # first up
                if($trend_counter["up"] == 1)
                {
                    # percent change
                    $percent_change = (($price["price"] - $first_price["price"])/ $first_price["price"]) * 100;

                    # CRYPTO STATUS
                    # set status
                    $status = [
                        "name" => $crypto,
                        "price" => $price["price"],
                        "percent_change" => $percent_change,
                        "min" => $min,
                        "max" => $max,
                        "start" => $first_price["price"],
                        "x_up" => $trend_counter["up"],
                        "x_down" => $trend_counter["down"]
                    ];
                }

                # LOG
                Log::debug("up", $status);

                # WIN
                if($percent_change >= 0.2)
                {
                    Log::debug("WIN", ["percent_change" => $percent_change]);
                }
            }
            elseif($price["price"] == $first_price["price"])
            {
                // Log::debug("...");
            }
            else
            {
                # COUNTER
                # increment counter up
                $trend_counter["down"] = $trend_counter["down"] + 1;

                # reset counter down
                $trend_counter["up"] = 0;

                # CRYPTO STATUS
                # set status
                $status = [
                    "name" => $crypto,
                    "price" => $price["price"],
                    "percent_change" => $percent_change,
                    "min" => $min,
                    "max" => $max,
                    "start" => $status["start"],
                    "x_up" => $trend_counter["up"],
                    "x_down" => $trend_counter["down"]
                ];

                # first up
                if($trend_counter["down"] == 1)
                {
                    # percent change
                    $percent_change = (($price["price"] - $first_price["price"])/ $first_price["price"]) * 100;

                    # CRYPTO STATUS
                    # set status
                    $status = [
                        "name" => $crypto,
                        "price" => $price["price"],
                        "percent_change" => $percent_change,
                        "min" => $min,
                        "max" => $max,
                        "start" => $first_price["price"],
                        "x_up" => $trend_counter["up"],
                        "x_down" => $trend_counter["down"]
                    ];
                }

                # LOG
                Log::debug("down", $status);
            }

            # price (now) is new first price
            $first_price = $price;
        }

        return true;
    }

    # get all crypto status
    public static function getAllCrypto($interval, $limit)
    {
        // set all crypto
        $all_crypto = [
            "COMPUSDT",
            "SUSHIUSDT",
            "SANDUSDT",
            "UNIUSDT",
            //"YFIUSDT",
            "SNXUSDT",
            "AAVEUSDT",
            //"KNCUSDT",
            "MKRUSDT",
            "ZRXUSDT",
            "BALUSDT",
            "UMAUSDT",
            "CRVUSDT",
            //"ALPHAUSDT",
            "RENUSDT"
        ];

        # set response
        $response = [];

        foreach($all_crypto as $crypto)
        {
            $history = BinanceService::history($crypto, $interval, $limit);

            $history["latest_delta_percent"] = $history["latest_delta"] / ($history["close"] - $history["latest_delta"]) * 100;

            $response[] = $history;
        }

        $keys = array_column($response, 'latest_delta_percent');
        array_multisort($keys, SORT_ASC, $response);

        return $response;
    }

    # get all best crypto
    public static function getBest($limit = 20)
    {
        $all_crypto = CapryonService::getAllCrypto("5m", $limit);

        $best = $all_crypto[count($all_crypto) - 1];

        if($best["latest_delta_percent"] > 0.5)
        {
            return $best;    
        }

        return null;
    }

    # get latest trends
    public static function getLatestTrends($crypto, $hours = 24)
    {
        $history = BinanceService::history($crypto, "15m", 4*$hours);

        # set last direction
        $delta = 0;
        $last_direction = "up";

        # set trend
        $trend = [];

        # set counter
        $counter = 0;

        # loop all directions
        foreach($history["directions"] as $direction)
        {
            # get current direction
            $current_direction = $direction["direction"];

            # check direction
            if($direction["direction"] == $last_direction)
            {
                $delta += $direction["delta"];
                $counter++;
            }
            else
            {
                # check first loop
                if($counter != 0)
                {
                    # update trend
                    $trend[] = [
                        $last_direction => $delta,
                        "count" => $counter
                    ];
                }

                # update direction
                $last_direction = $direction["direction"];

                # reset delta
                $delta = $direction["delta"];

                # reset counter
                $counter = 1;
            }
        }

        return $trend;
    }

    # analyze latest trends
    public static function analyzeTrend($crypto, $hours)
    {
        # get latest trend
        $trend = CapryonService::getLatestTrends($crypto, $hours);
        $result = [
            "data" => [],
            "direction" => null
        ];

        # loop trend
        for($pair_counter = count($trend); $pair_counter >=2; $pair_counter-=2)
        {
            $first = $trend[$pair_counter - 1];
            $second = $trend[$pair_counter - 2];

            $first_direction = isset($first["down"]) ? "down" : "up";
            $second_direction = $first_direction == "down" ? "up" : "down";
            
            $pair = [
                "first" => $first,
                "second" => $second,
                "diff" => [
                    $first[$first_direction] + $second[$second_direction] > 0 ? "up" : "down" => $first[$first_direction] + $second[$second_direction],
                    "count" => $first["count"] + $second["count"]
                ]
            ];

            # get current pair
            $result["data"][] = $pair;
        }

        # reverse result
        $reverse_result = array_reverse($result["data"]);

        $count_up = 0;

        $latest_up = [];

        foreach($reverse_result as $pair)
        {
            if(!isset($pair["diff"]["up"]))
            {
                break;
            }
            
            # check continuous up
            if(count($latest_up) == 0 || min($latest_up) > $pair["diff"]["up"])
            {
                $latest_up[] = $pair["diff"]["up"];
            } 
        }

        if(count($latest_up) >= 3)
        {
            $result["direction"] = "up";
        }
        else
        {
            $result["direction"] = "down";
        }

        return $result;
    }

    # analyze latest trends
    public static function analyzeCryptoTrend($cryptos = [], $hours = 12)
    {
        # set result
        $result = [];

        # loop all crypto
        foreach($cryptos as $crypto)
        {
            $trend = CapryonService::analyzeTrend($crypto, $hours);

            $trend["name"] = $crypto;

            if($trend["direction"] == "up")
            {
                $result[] = $trend;
            }
        }

        return $result;
    }
}