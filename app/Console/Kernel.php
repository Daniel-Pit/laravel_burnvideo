<?php

namespace burnvideo\Console;

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
		\burnvideo\Console\Commands\BurnCommand::class,
        \burnvideo\Console\Commands\UpdateMonthCommand::class,
        \burnvideo\Console\Commands\FirstOrderMail::class,
        \burnvideo\Console\Commands\s3bucketdelete::class,
        \burnvideo\Console\Commands\UsermailSend::class,
        \burnvideo\Console\Commands\userActivate::class,
        \burnvideo\Console\Commands\notifySend::class,
        \burnvideo\Console\Commands\ParallelConvert::class,
        \burnvideo\Console\Commands\ParallelTest::class,
        \burnvideo\Console\Commands\UsermailSendTest::class,
        \burnvideo\Console\Commands\ParallelConvertOne::class,
        \burnvideo\Console\Commands\s3bucketdeleteOne::class,
        \burnvideo\Console\Commands\BurnOneDownload::class,
        \burnvideo\Console\Commands\AwsConvert::class,
        \burnvideo\Console\Commands\AwsCompleteOrder::class,
        \burnvideo\Console\Commands\ordermoniter::class,

    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
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
