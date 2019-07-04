<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\DailyProjectStatus;

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
        // 7 PM -> 19:00
        $schedule->command('pms:status_update')
         ->at('19:10')
         ->when(function(){
          return !now()->isWeekend();
         });

        // 6:40 PM -> 18:40        
        $schedule->command('pms:status_invalid')
         ->at('18:40')
         ->when(function(){
          return !now()->isWeekend();
         });

         // 9:00 PM -> 09:30        
        $schedule->command('pms:update_data')
         ->at('09:30')
         ->when(function(){
          return !now()->isWeekend();
         });
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
