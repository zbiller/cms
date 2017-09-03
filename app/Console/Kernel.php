<?php

namespace App\Console;

use App\Console\Commands\ActivityCleanCommand;
use App\Console\Commands\BlockMakeCommand;
use App\Console\Commands\CartCleanCommand;
use App\Console\Commands\CartReminderCommand;
use App\Console\Commands\CurrencyUpdateCommand;
use App\Console\Commands\UploadsLinkCommand;
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
     * @param \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //uncomment to run currency exchange rate update daily via cron jobs
        //$schedule->command(CurrencyUpdateCommand::class)->daily()->withoutOverlapping()->emailOutputTo('example@mail.com');

        //uncomment to clean cart entries weekly via cron jobs
        //$schedule->command(CartCleanCommand::class)->weekly()->withoutOverlapping()->emailOutputTo('example@mail.com');

        //uncomment to clean activity log entries weekly via cron jobs
        //$schedule->command(ActivityCleanCommand::class)->weekly()->withoutOverlapping()->emailOutputTo('example@mail.com');
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
