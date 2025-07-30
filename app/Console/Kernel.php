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
        // Agende o comando SyncIfpeStudents para ser executado todos os dias à 1:00 AM
        $schedule->command('sync:ifpe-students')->dailyAt('01:00');
        // ou $schedule->command(SyncIfpeStudents::class)->dailyAt('01:00');
        // Você pode usar 'daily()' para meia-noite, ou 'dailyAt('HH:MM')' para um horário específico.
    }
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');



        require base_path('routes/console.php');
    }
}
