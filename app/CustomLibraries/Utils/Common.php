<?php

namespace App\CustomLibraries\Utils;
use Illuminate\Support\Facades\DB;
use App\CustomLibraries\Utils\CallFlowUtils;
/**
 * Description of Common
 *
 * @author waybeo
 */
class Common {
    
    public static function getAllRegions()
    {
        return DB::table('regions')->distinct()->pluck('region','id');
    }
    public static function getAllClusters()
    {
        return DB::table('clusters')->distinct()->pluck('cluster','id');
    }
    public static function getAllStores()
    {
        return DB::table('stores')->distinct()->pluck('store_name','id');
    }
    public static function getAllStates()
    {
        return DB::table('states')->distinct()->pluck('state', 'id');
    }

    public static function getAllLocations()
    {
        return DB::table('stores')->select('locality')->distinct()->get();
    }

    public static function getAllCities()
    {
        return DB::table('cities')->distinct()->pluck('city', 'id');
    }
    
    public static function getAllLocationsByRsmId($rsmId)
    {
        return DB::table('dealership')->where('rsm_id', $rsmId)->select('location')->distinct()->get();
    }
    
    public static function getAllRegionsByRsmId($rsmId)
    {
        return DB::table('dealership')->where('rsm_id', $rsmId)->select('region')->distinct()->get();
    }

    public static function getAllLocationsByDomId($domId)
    {
        return DB::table('dealership')
            ->join('rsm', 'dealership.rsm_id', '=', 'rsm.id')
            ->join('dom', 'rsm.dom_id', '=', 'dom.id')
            ->where('rsm.dom_id', $domId)
            ->select('dealership.location')
            ->distinct()
            ->get();
    }

    public static function getAllRegionByDomId($domId)
    {
        return DB::table('dealership')
            ->join('rsm', 'dealership.rsm_id', '=', 'rsm.id')
            ->join('dom', 'rsm.dom_id', '=', 'dom.id')
            ->where('rsm.dom_id', $domId)
            ->select('dealership.region')
            ->distinct()
            ->get();
    }

    public static function getAllLocationsByRegionAndDomId($region,$domId)
    {
        return DB::table('dealership')
            ->join('rsm', 'dealership.rsm_id', '=', 'rsm.id')
            ->join('dom', 'rsm.dom_id', '=', 'dom.id')
            ->where('rsm.dom_id', $domId)
            ->where('dealership.region', $region)
            ->select('dealership.location')
            ->distinct()
            ->get();
    }

    public static function pushToSQS($data)
    {
        $client = SqsClient::factory([
            'credentials' => [
                'key' => 'AKIA4BEHSEYUZCQX4IPF', 
                'secret' => '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'
            ],
            'region' => 'ap-south-1',
            'version' => '2012-11-05'
        ]);
        
        $queueUrl  ='https://sqs.ap-south-1.amazonaws.com/827065509417/Airtel-Number-Update-Q.fifo';
        $fifo_queue_message = json_encode($data);
        $message_group_id   = 'test-group';
        $message_dup_id     = $message_group_id.time();

        $response = $client->sendMessage(array(
            'QueueUrl' => $queueUrl,  
            'MessageBody' =>  $fifo_queue_message,
            'MessageGroupId' =>  $message_group_id,
            'MessageDeduplicationId' =>  $message_dup_id
        ))->toArray();
        return [
            'status' => $response['@metadata']['statusCode'],
            'uuid' => $message_dup_id
        ];
    }
    public static function updateFlow()
    {
        $activeVNs = DB::table('stores')
            ->join('numbers', 'stores.id', '=', 'numbers.store_id')
            ->leftJoin('agents as no_p1', function ($join) {
                $join->on('stores.id', '=', 'no_p1.store_id')
                    ->where('no_p1.number_type', '=', 'P1')
                    ->whereNull('no_p1.deleted_at');
            })
            ->leftJoin('agents as no_p2', function ($join) {
                $join->on('stores.id', '=', 'no_p2.store_id')
                    ->where('no_p2.number_type', '=', 'P2')
                    ->whereNull('no_p2.deleted_at');
            })
            ->leftJoin('agents as no_p3', function ($join) {
                $join->on('stores.id', '=', 'no_p3.store_id')
                    ->where('no_p3.number_type', '=', 'P3')
                    ->whereNull('no_p3.deleted_at');
            })
            ->leftJoin('agents as no_p4', function ($join) {
                $join->on('stores.id', '=', 'no_p4.store_id')
                    ->where('no_p4.number_type', '=', 'P4')
                    ->whereNull('no_p4.deleted_at');
            })
            ->leftJoin('agents as no_e1', function ($join) {
                $join->on('stores.id', '=', 'no_e1.store_id')
                    ->where('no_e1.number_type', 'E1')
                    ->whereNull('no_e1.deleted_at');
            })
            ->leftJoin('agents as no_e2', function ($join) {
                $join->on('stores.id', '=', 'no_e2.store_id')
                    ->where('no_e2.number_type', 'E2')
                    ->whereNull('no_e2.deleted_at');
            })
            ->whereNull('stores.deleted_at')
            ->get([
                'stores.id', 
                'numbers.sim_number',
                'stores.working_hours', 
                DB::raw("CONCAT_WS(',',no_p1.number,no_p2.number,no_p3.number,no_p4.number) AS simultaneous_numbers"),
                DB::raw("CONCAT_WS(',',no_e1.number,no_e2.number) AS escalation_numbers"),
            ]);

        /*Build Final Callflow JSON*/
        foreach ($activeVNs as $details) {
            $callFlow = CallFlowUtils::buildCallFlow($details->sim_number, $details->simultaneous_numbers, $details->escalation_numbers, $details->working_hours);
            $response = CallFlowUtils::updateCallFlow($callFlow);
            if(!empty($response['status']) && $response['status'] == 'failed') {
                /*Send Alert*/
            }
            exit;
        }
    }

}
