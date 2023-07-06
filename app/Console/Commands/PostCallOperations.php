<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\CustomLibraries\Utils\PostCallOpsUtils;
use Illuminate\Support\Facades\DB;
use App\CustomLibraries\Utils\Curl;
use Log;

class PostCallOperations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'post-call-operations:pureit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to handle post call operations';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        while(true) {
            // $files = \Illuminate\Support\Facades\Storage::disk('local')->files('/CallbackData/NotProcessed');
            $files = array_filter(\Illuminate\Support\Facades\Storage::disk('local')->files('/CallbackData/NotProcessed'), function ($file) {
                return !in_array($file, ['CallbackData/NotProcessed/.gitignore']);
            });
            if(!empty($files)) {
                foreach ($files as $filename) {
                    $file = \Illuminate\Support\Facades\Storage::get($filename);
                    if(!empty($file)) {
                        $status = PostCallOpsUtils::index($file);
                        if($status) {
                            \Illuminate\Support\Facades\Storage::delete($filename);
                        } elseif(!$status) {
                            $newPath = str_replace('NotProcessed', 'Failed', $filename);
                            \Illuminate\Support\Facades\Storage::move($filename, $newPath);
                        }
                    } else {
                        Log::info('File empty');//Send alert
                    }
                    }
            } else {
                Log::info('No files to process');
                Log::info('-------Going to sleep for 5 seconds-------');
                sleep(5);
            }
        }
    }
}
