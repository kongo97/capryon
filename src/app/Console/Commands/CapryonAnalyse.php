<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BinanceService;

class CapryonAnalyse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'capryon:bid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Analyse crypto';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // set all crypto
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

        // get best crypto
        $crypto = BinanceService::getBestCrypto($all_crypto);

        if($crypto != null)
        {
            BinanceService::bid($crypto, 380);
        }

        return 1;
    }
}
