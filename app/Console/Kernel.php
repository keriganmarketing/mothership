<?php

namespace App\Console;

use App\Jobs\CleanBcar;
use App\Jobs\UpdateBcar;
use App\Jobs\UpdateEcar;
use App\Jobs\UpdateAgents;
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
        $schedule->job(new CleanBcar)->hourly()->withOutOverlapping();
        $schedule->job(new UpdateAgents)->hourlyAt(5)->withOutOverlapping();
        $schedule->job(new UpdateBcar)->hourlyAt(10)->withOutOverlapping();
        $schedule->job(new UpdateEcar)->hourlyAt(15)->withOutOverlapping();
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
