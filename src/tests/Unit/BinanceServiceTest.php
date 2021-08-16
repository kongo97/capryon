<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\BinanceService;
use Illuminate\Support\Facades\Http;

class BinanceServiceTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */

    // ./vendor/bin/phpunit --filter testGetAllCrypto tests/Unit/BinanceServiceTest.php
    public function testGetAllCrypto()
    {
        $all = BinanceService::getAllCrypto();

        $this->assertTrue($all);
    }

    // ./vendor/bin/phpunit --filter testGetPrice tests/Unit/BinanceServiceTest.php
    public function testGetPrice()
    {
        $price = BinanceService::getPrice("COMPUSDT");

        print_r($price);

        $this->assertNotNull($price);
    }

    // ./vendor/bin/phpunit --filter testGetPriceChange tests/Unit/BinanceServiceTest.php
    public function testGetPriceChange()
    {
        $price = BinanceService::getPriceChange("ATAUSDT");

        print_r($price);

        $this->assertNotNull($price);
    }

    // ./vendor/bin/phpunit --filter testAnalyse tests/Unit/BinanceServiceTest.php
    public function testAnalyse()
    {
        $price = BinanceService::analyse();

        $this->assertTrue($price);
    }

    // ./vendor/bin/phpunit --filter testGetTotalAmount tests/Unit/BinanceServiceTest.php
    public function testGetTotalAmount()
    {
        $price = BinanceService::getTotalAmount();

        print_r($price);

        $this->assertNotNull($price);
    }

    // ./vendor/bin/phpunit --filter testGetGoog tests/Unit/BinanceServiceTest.php
    public function testGetGoog()
    {
        $price = BinanceService::getGood(5);

        print_r($price);

        $this->assertNotNull($price);
    }

    // ./vendor/bin/phpunit --filter testPlay tests/Unit/BinanceServiceTest.php
    public function testPlay()
    {
        $price = BinanceService::play("COMPUSDT", 250);

        $this->assertNotNull($price);
    }

    // ./vendor/bin/phpunit --filter testGetBestCrypto tests/Unit/BinanceServiceTest.php
    public function testGetBestCrypto()
    {
        $all_crypto = [
            "COMPUSDT",
            "SUSHIUSDT",
            "SANDUSDT",
            "UNIUSDT",
            "YFIUSDT",
            "SNXUSDT",
            "AAVEUSDT",
            "KNCUSDT",
            "MKRUSDT",
            "ZRXUSDT",
            "BALUSDT",
            "UMAUSDT",
            "CRVUSDT",
            "ALPHAUSDT",
            "RENUSDT"
        ];

        $price = BinanceService::getBestCrypto($all_crypto);

        echo $price;

        $this->assertNotNull($price);
    }

    // ./vendor/bin/phpunit --filter testSell tests/Unit/BinanceServiceTest.php
    public function testSell()
    {
        $price = BinanceService::sell("COMPUSDT", 1);

        print_r($price);

        $this->assertNotNull($price);
    }

    // ./vendor/bin/phpunit --filter testBuy tests/Unit/BinanceServiceTest.php
    public function testBuy()
    {
        $price = BinanceService::buy("COMPUSDT", 0.03);

        $qty = $price["fills"][0]["qty"];

        die("$qty");

        print_r($price);

        $this->assertNotNull($price);
    }

    // ./vendor/bin/phpunit --filter testGetTimestamp tests/Unit/BinanceServiceTest.php
    public function testGetTimestamp()
    {
        $price = BinanceService::getTimestamp();

        print_r("$price");

        $this->assertNotNull($price);
    }

    // ./vendor/bin/phpunit --filter testBid tests/Unit/BinanceServiceTest.php
    public function testBid()
    {
        $price = BinanceService::bid("COMPUSDT", 300);

        $this->assertNotNull($price);
    }

    // ./vendor/bin/phpunit --filter testGetOrders tests/Unit/BinanceServiceTest.php
    public function testGetOrders()
    {
        $price = BinanceService::getOrders("COMPUSDT", 392, 1);

        echo "N: $price";

        $this->assertNotNull($price);
    }

    // ./vendor/bin/phpunit --filter testGoodSell tests/Unit/BinanceServiceTest.php
    public function testGoodSell()
    {
        $price = BinanceService::goodSell(4);

        $this->assertNotNull($price);
    }
}
