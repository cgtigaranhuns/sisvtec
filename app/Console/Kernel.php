<?php

namespace App\Console;

use App\Console\Commands\SyncIfpeStudents;
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
        SyncIfpeStudents::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        
       $schedule->command('sync:ifpe-students')
             ->everyFiveMinutes()
            // ->sendOutputTo(storage_path('logs/sync_ifpe_students.log')); // Cria um novo arquivo a cada execução
             ->appendOutputTo(storage_path('logs/sync_ifpe_students.log')); // Adiciona ao final do arquivo
       
    }
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');



        require base_path('routes/console.php');
    }
}
