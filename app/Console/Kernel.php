<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    private $_scheduledCommands = [
//        ['telegram:getUpdates' => 'everyMinute',]
    ];
    
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $this->runScheduledCommands($schedule);
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        
        require base_path('routes/console.php');
    }

    public function runScheduledCommands(Schedule $schedule)
    {
        foreach ($this->_scheduledCommands as $commands) {
            foreach ($commands as $command => $period) {
                $schedule->command($command)->$period();
            }
        }
    }
}
