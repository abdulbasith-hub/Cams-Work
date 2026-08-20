<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
  //  protected function schedule(Schedule $schedule)
  //  {
        // $schedule->command('inspire')->hourly();
    //}

protected function schedule(Schedule $schedule)
{
    $schedule->command('send:auditeedailystatus')->dailyAt('09:00');
    $schedule->command('send:afexitmeet_auditorpending')->dailyAt('10:00');  // ⏱️ new cron
    $schedule->command('helpdesk:auto-forward-nic-tickets')->everyMinute()->withoutOverlapping();
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
