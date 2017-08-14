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
        UploadsLinkCommand::class,
        BlockMakeCommand::class,
        CurrencyUpdateCommand::class,
        CartCleanCommand::class,
        CartReminderCommand::class,
        ActivityCleanCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        /*$schedule->command(CurrencyUpdateCommand::class)->daily()
            ->withoutOverlapping()->emailOutputTo('example@mail.com');*/
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
