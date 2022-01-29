<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        //$schedule->exec("php artisan capryon:earn")->everyMinute()->withoutOverlapping();

        // daily update
        $schedule->exec("./vendor/bin/phpunit --filter testDailyUpdate tests/Unit/CapryonServiceTest.php")->everyMinute()->withoutOverlapping();

        // quick update
        $schedule->exec("./vendor/bin/phpunit --filter testQuickUpdate tests/Unit/CapryonServiceTest.php")->everyMinute()->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
