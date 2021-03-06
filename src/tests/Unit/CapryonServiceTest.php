<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\CapryonService;
use Illuminate\Support\Facades\Http;

class CapryonServiceTest extends TestCase
{
    // ./vendor/bin/phpunit --filter testEarn tests/Unit/CapryonServiceTest.php
    public function testEarn()
    {
        $earn = CapryonService::earn();

        $this->assertTrue($earn);
    }

    // ./vendor/bin/phpunit --filter testSell tests/Unit/CapryonServiceTest.php
    public function testSell()
    {
        $earn = CapryonService::sell();

        $this->assertTrue($earn);
    }

    // ./vendor/bin/phpunit --filter testGetTrend tests/Unit/CapryonServiceTest.php
    public function testGetTrend()
    {
        $trend = CapryonService::getTrend("BALAUSDT");

        $this->assertTrue($trend);
    }

    // ./vendor/bin/phpunit --filter testGetAllCrypto tests/Unit/CapryonServiceTest.php
    public function testGetAllCrypto()
    {
        $history = CapryonService::getAllCrypto("15m", 4);

        echo json_encode($history, JSON_PRETTY_PRINT);

        $this->assertNotNull($history);
    }

    // ./vendor/bin/phpunit --filter testGetBest tests/Unit/CapryonServiceTest.php
    public function testGetBest()
    {
        $best = CapryonService::getBest();

        echo json_encode($best, JSON_PRETTY_PRINT);

        $this->assertNotNull($best);
    }

    // ./vendor/bin/phpunit --filter testGetLatestTrends tests/Unit/CapryonServiceTest.php
    public function testGetLatestTrends()
    {
        $trend = CapryonService::getLatestTrends("COMPUSDT", 12);

        echo json_encode($trend, JSON_PRETTY_PRINT);

        $this->assertNotNull($trend);
    }

    // ./vendor/bin/phpunit --filter testAnalyzeTrend tests/Unit/CapryonServiceTest.php
    public function testAnalyzeTrend()
    {
        $trend = CapryonService::analyzeTrend("SANDUSDT", 12);

        echo json_encode($trend, JSON_PRETTY_PRINT);

        $this->assertNotNull($trend);
    }

    // ./vendor/bin/phpunit --filter testAnalyzeCryptoTrend tests/Unit/CapryonServiceTest.php
    public function testAnalyzeCryptoTrend()
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

        $trends = CapryonService::analyzeCryptoTrend($all_crypto, 12);

        echo json_encode($trends, JSON_PRETTY_PRINT);

        $this->assertNotNull($trends);
    }
}
