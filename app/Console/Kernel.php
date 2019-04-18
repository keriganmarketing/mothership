<?php

namespace App\Console;

use App\Jobs\CleanBcar;
use App\Jobs\CleanEcar;
use App\Jobs\UpdateBcar;
use App\Jobs\UpdateEcar;
use App\Jobs\UpdateAgents;
use App\Jobs\Heartbeat;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Updaters\OpenHousesUpdater;
use App\Jobs\UpdateOpenHouses;
use App\Jobs\CleanOpenHouses;
use App\Jobs\RepairDB;

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
        $schedule->job(new CleanOpenHouses, 'cleaners')->hourly()->withoutOverlapping();
        $schedule->job(new UpdateAgents, 'updaters')->hourlyAt(5)->withOutOverlapping();
        $schedule->job(new UpdateBcar, 'updaters')->hourlyAt(10)->withOutOverlapping();
        $schedule->job(new UpdateEcar, 'updaters')->hourlyAt(15)->withOutOverlapping();
        $schedule->job(new UpdateOpenHouses, 'updaters')->hourly(20)->withoutOverlapping();
        // $schedule->job(new CleanBcar, 'cleaners')->hourly(21)->withOutOverlapping();
        // $schedule->job(new CleanEcar, 'cleaners')->hourly(22)->withOutOverlapping();
        // $schedule->job(new RepairDB, 'cleaners')->daily()->withoutOverlapping();

        //dev 
        // $schedule->job(new Heartbeat, 'updaters')->everyMinute();
        // $schedule->job(new Heartbeat, 'cleaners')->everyMinute();
        
        $schedule->command('horizon:snapshot')->everyFiveMinutes();
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
