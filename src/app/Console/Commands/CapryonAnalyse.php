<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BinanceService;
use App\Services\CapryonService;
use Illuminate\Support\Facades\Log;

class CapryonAnalyse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'capryon:earn';

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
        CapryonService::earn(300);

        return 1;
    }
}
