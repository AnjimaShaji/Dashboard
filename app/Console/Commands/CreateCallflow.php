<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\CustomLibraries\Utils\Curl;
use App\CustomLibraries\Utils\Constants;
use App\CustomLibraries\Utils\CallFlowUtils;
use Log;

class CreateCallflow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create-callflow:pureit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to create callflow for every virtual number';

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
        $activeVNs = DB::table('stores')
            ->join('numbers', 'stores.id', '=', 'numbers.store_id')
            ->whereNull('stores.deleted_at')
            ->get(['stores.id', 'numbers.sim_number','stores.working_hours','stores.store_numbers']);


        /*Build Final Callflow JSON*/
        foreach ($activeVNs as $details) {
            $callFlow = CallFlowUtils::buildCallFlow($details->sim_number, $details->store_numbers, $details->working_hours);
            print_r($callFlow);
            print_r("HIIIII");
            $response = CallFlowUtils::updateCallFlow($callFlow);
            print_r($response);
            exit;
            if(!empty($response['status']) && $response['status'] == 'failed') {
                /*Send Alert*/
            }
            exit;
        }
        /*Build Final Callflow JSON*/
    }
}
