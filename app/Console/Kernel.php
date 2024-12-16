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
        Commands\updateWithdrawRequests::class,
        Commands\updateOzowStatus::class,
        Commands\updateFundTransfer::class,
//        Commands\updateKycStatus::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
         // $schedule->command('command:cancelWithdrawRequest')->hourly();
        // $schedule->command('command:updateOzowStatus')->daily();
        // $schedule->command('command:updateFundTransfer')->daily();
       //  $schedule->command('command:updateKycStatus')->hourly();
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
