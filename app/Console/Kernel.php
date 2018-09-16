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
    protected $commands = [];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule
            ->command('ark:ping')
            ->everyMinute()
            ->evenInMaintenanceMode()
            ->withoutOverlapping();

        $schedule
            ->command('ark:maintain:voters')
            ->everyFiveMinutes()
            ->withoutOverlapping();

        $schedule
            ->command('ark:poll:voters')
            ->everyFiveMinutes()
            ->withoutOverlapping();

        $schedule
            ->command('ark:poll:blocks')
            ->everyFiveMinutes()
            ->withoutOverlapping();

        if (config('delegate.polling.transactions')) {
            $schedule
                ->command('ark:poll:transactions')
                ->everyFiveMinutes()
                ->withoutOverlapping();
        }

        if (config('delegate.distribute.blacklist')) {
            $schedule
                ->command('ark:distribute:banned')
                ->dailyAt('17:55')
                ->withoutOverlapping();
        }

        $schedule
            ->command('ark:disburse:voters')
            ->dailyAt('18:00')
            ->withoutOverlapping();

        $schedule
            ->command('ark:disburse:developer')
            ->dailyAt('18:30')
            ->withoutOverlapping();

        $schedule
            ->command('backup:clean')
            ->daily();

        $schedule
            ->command('backup:run')
            ->daily();

        $schedule
            ->command('backup:monitor')
            ->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
    }
}
