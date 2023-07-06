<?php

namespace App\Console;

use App\PbxCallback;
use Carbon\Carbon;
use Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\DB;
use App\CustomLibraries\CallLogUtils;
use App\CustomLibraries\DataLayerCallLogUtils;
use App\CustomLibraries\MailUtils;
use App\CustomLibraries\DatalayerUtils;
use App\CustomLibraries\Utils\RmSalesUtils;
use App\CustomLibraries\Utils\RccmServiceUtils;
use App\CustomLibraries\Utils\CustomUtils;
use App\CustomLibraries\Utils\ZmUtils;
use App\CustomLibraries\Utils\DealerUtils;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\PostCallOperations::class,
        Commands\CreateCallflow::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

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
