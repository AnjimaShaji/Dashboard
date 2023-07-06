<?php

namespace App\Http\Controllers\Store;

use App\CustomLibraries\CallLogUtils;
use App\CustomLibraries\Calllog;
use App\CustomLibraries\MongoPagination;
use App\CustomLibraries\Utils;
use App\CustomLibraries\Utils\Constants;
use App\Http\Controllers\Controller;
use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Log;
use MongoId;


class ReportsController extends Controller
{

    public function details()
    {
        $params = Input::all();
        $user_id = auth()->id();

        Log::info('Params: ', $params);
        if(empty($params['date_from'])){
            $params['date_from'] = date('Y-m-d',strtotime('first day of this month'));
        }
        if(empty($params['date_to'])){
            $params['date_to'] = date('Y-m-d');
        }
        if(empty($params['page'])){
            $params['page'] = 1;
        }
        $params['StoreId'] = Utils\StoreUtils::getStoreIdByUserId(auth()->id());
        // $params = CallLogUtils::getFilterParams($params);
        $fields = Constants::$ADMIN_LISTING_FIELDS;
        $params['Fields'] = Constants::$ADMIN_DEFAULT_FIELDS_TO_DISPLAY;

        $itemsPerPage = 10;
        $callLogs = CallLogUtils::getCalllogs($params, $fields,$itemsPerPage);
        $resp['paginator'] = $callLogs[1];
        $resp['callLogs'] = $callLogs[0]['dataset'];
        $resp['totalItems'] = $callLogs[0]['totalItems'];

        $resp['params'] = $params;
        $resp['doms'] = [];

        return view('admin.reports.calllog', $resp);
    }

    public function callDetails($callid)
    {
        Log::info($callid);

        $callDetails = CallLogUtils::getCallDetails($callid);

        return view('admin.reports.details', ['callDetails' => $callDetails]);
    }

    public function callStatusByDay()
    {
        $params = Input::all();
       $params['StoreId'] = Utils\StoreUtils::getStoreIdByUserId(auth()->id());
        $list = CallLogUtils::fetchCallsStatusByDay($params);

        $result = [];
        for ($i=0; $i<count($list);$i++) {
             $row = array_values($list[$i]);
             $row[0] = $row[0]['Month'];
             array_push($result, $row);
        }

        return response()->json($result);
    }
    
    
    public function callDurationAverage()
    {
        $params = Input::all();
       $params['StoreId'] = Utils\StoreUtils::getStoreIdByUserId(auth()->id());
        $data = CallLogUtils::fetchCallDurationAverage($params);
        return response()->json($data);
    }

    public function export()
    {
        $params = Input::all();
       $params['StoreId'] = Utils\StoreUtils::getStoreIdByUserId(auth()->id());
        $params = CallLogUtils::getFilterParams($params);
        if (empty($params['Fields'])) {
            $params['Fields'] = Constants::$ADMIN_EXPORT_FIELDS;
        }
        $fields = CallLogUtils::mapFilterFields($params['Fields']);
        $keys = explode(',', $fields);
        $fields = array_fill_keys($keys, TRUE);

        $calllogs = CallLogUtils::getCalllogs($params['DomId'], $params['RsmId'],
                $params['DealerId'], $params['Type'], $params['callType'],
                $params['CallerId'], $params['date_from'], $params['date_to'], 
                $params['Status'], $params['TotalDuration'], $fields,
                NULL, NULL, $params['Region'], $params['Unique'], $params['State'],
                $params['Location']);

        
        // output headers so that the file is downloaded rather than displayed
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=data.csv');

        // create a file pointer connected to the output stream
        $output = fopen('php://output', 'w');
        $heading = [];
        $out = array(array());
        $index = 0;
        foreach ($calllogs as $calllog) {

            if (isset($calllog['DateTime'])) {
                $out[$index]['CallStartTime'] = $calllog['DateTime'];
                if (!in_array('CallStartTime', $heading))
                    $heading = array_merge($heading, array('CallStartTime'));
            }
            if (isset($calllog['Dealer'])) {
                $out[$index]['Dealer'] = $calllog['Dealer'];
                if (!in_array('Dealer', $heading))
                    $heading = array_merge($heading, array('Dealer'));
            }
            if (isset($calllog['Location'])) {
                $out[$index]['Location'] = $calllog['Location'];
                if (!in_array('Location', $heading))
                    $heading = array_merge($heading, array('Location'));
            }
            if (isset($calllog['PandaCode'])) {
                $out[$index]['PandaCode'] = $calllog['PandaCode'];
                if (!in_array('PandaCode', $heading))
                    $heading = array_merge($heading, array('PandaCode'));
            }
            if (isset($calllog['WorkshopPandaCode'])) {
                $out[$index]['WorkshopPandaCode'] = $calllog['WorkshopPandaCode'];
                if (!in_array('WorkshopPandaCode', $heading))
                    $heading = array_merge($heading, array('WorkshopPandaCode'));
            }
            if (isset($calllog['CallRecordUrl'])) {
                if(!empty($calllog['ConversationDuration'])) {
                    $out[$index]['CallRecordUrl'] = $calllog['CallRecordUrl'];
                } else {
                    $out[$index]['CallRecordUrl'] = '';
                }
                if (!in_array('CallRecordUrl', $heading))
                    $heading = array_merge($heading, array('CallRecordUrl'));
            }
            if (isset($calllog['ConversationDuration'])) {
                $out[$index]['ConversationDuration'] = $calllog['ConversationDuration'];
                if (!in_array('ConversationDuration', $heading))
                    $heading = array_merge($heading, array('ConversationDuration'));
            }
            if (isset($calllog['Type'])) {
                $out[$index]['VNType'] = $calllog['Type'];
                if (!in_array('VNType', $heading))
                    $heading = array_merge($heading, array('VNType'));
            }
            if (isset($calllog['IvrLog'])) {
                $_ivrlog = end($calllog['IvrLog']);
                if (strpos($params['Fields'], 'CallType') != false) {
                    if (!empty($_ivrlog)) {
                        $out[$index]['CallType'] = current($_ivrlog);
                    } else {
                        $out[$index]['CallType'] = "";
                    }
                    if (!in_array('CallType', $heading))
                        $heading = array_merge($heading, array('CallType'));
                }
                if (strpos($params['Fields'], 'CallType') != false) {
                    if (!empty($_ivrlog)) {
                        $out[$index]['IVRKeyPressDigit'] = key($_ivrlog);
                    } else {
                        $out[$index]['IVRKeyPressDigit'] = "";
                    }
                    if (!in_array('IVRKeyPressDigit', $heading))
                        $heading = array_merge($heading, array('IVRKeyPressDigit'));
                }
            }
            if (isset($calllog['Status'])) {
                if (!empty($calllog['Status'])) {
                    $callStatus = $calllog['Status'];
                } else {
                    $callStatus = 'Call Abandoned';
                }
                $out[$index]['Status'] = $callStatus;
                if (!in_array('Status', $heading))
                    $heading = array_merge($heading, array('Status'));
            }
            if (isset($calllog['CallerId'])) {
                $out[$index]['CustomerNumber'] = substr($calllog['CallerId'], -10);
                if (!in_array('CustomerNumber', $heading))
                    $heading = array_merge($heading, array('CustomerNumber'));
            }
            if (isset($calllog['MaskedNumber'])) {
                $out[$index]['VirtualNumber'] = substr($calllog['MaskedNumber'], -10);
                if (!in_array('VirtualNumber', $heading))
                    $heading = array_merge($heading, array('VirtualNumber'));
            }
            if (isset($calllog['AgentNumber'])) {
                $out[$index]['ConnectedTo'] = substr($calllog['AgentNumber'], -10);
                if (!in_array('ConnectedTo', $heading))
                    $heading = array_merge($heading, array('ConnectedTo'));
            }
            if (isset($calllog['IVRDuration'])) {
                $out[$index]['IVRDuration'] = $calllog['IVRDuration'];
                if (!in_array('IVRDuration', $heading))
                    $heading = array_merge($heading, array('IVRDuration'));
            }
            if (isset($calllog['RingDuration'])) {
                $out[$index]['RingDuration'] = $calllog['RingDuration'];
                if (!in_array('RingDuration', $heading))
                    $heading = array_merge($heading, array('RingDuration'));
            }
            if (isset($calllog['CalleeLegStatus'])) {
                $out[$index]['CalleeLegStatus'] = $calllog['CalleeLegStatus'];
                if (!in_array('CalleeLegStatus', $heading))
                    $heading = array_merge($heading, array('CalleeLegStatus'));
            }
            if (isset($calllog['HangupLeg'])) {
                $out[$index]['HangupLeg'] = $calllog['HangupLeg'];
                if (!in_array('HangupLeg', $heading))
                    $heading = array_merge($heading, array('HangupLeg'));
            }
            if (isset($calllog['BusyCallees'])) {
                $BusyCallees = '';
                if (is_array($calllog['BusyCallees'])) {
                    foreach ($calllog['BusyCallees'] as $bKey => $bValue) {
                        $BusyCallees.=substr($bValue, -10) . ', ';
                    }
                    $BusyCallees = rtrim($BusyCallees,', ');
                }
                $out[$index]['BusyCallees'] = $BusyCallees;
                if (!in_array('BusyCallees', $heading))
                    $heading = array_merge($heading, array('BusyCallees'));
            }
            if (isset($calllog['Rsm'])) {
                $out[$index]['Rsm'] = $calllog['Rsm'];
                if (!in_array('Rsm', $heading))
                    $heading = array_merge($heading, array('Rsm'));
            }
            if (isset($calllog['Dom'])) {
                $out[$index]['Dom'] = $calllog['Dom'];
                if (!in_array('Dom', $heading))
                    $heading = array_merge($heading, array('Dom'));
            }
            if (isset($calllog['Region'])) {
                $out[$index]['Region'] = $calllog['Region'];
                if (!in_array('Region', $heading))
                    $heading = array_merge($heading, array('Region'));
            }
             if (isset($calllog['State'])) {
                 $out[$index]['State'] = $calllog['State'];
                 if (!in_array('State', $heading))
                     $heading = array_merge($heading, array('State'));
             }
            if (isset($calllog['DateTime'])) {
                $out[$index]['CallEndTime'] = $calllog['DateTime'];
                if (!empty($calllog['TotalDuration'])) {
                    $out[$index]['CallEndTime'] = date('Y-m-d H:i:s', strtotime($calllog['DateTime']) + $calllog['TotalDuration']);
                }
                if (!in_array('CallEndTime', $heading))
                    $heading = array_merge($heading, array('CallEndTime'));
            }

            $index ++;
        }
        $count = $index;
        fputcsv($output, $heading);
        // output the rows
        for ($i = 0; $i <= $count; $i++) {
            $row = array();
            for ($j = 0; $j < count($heading); $j++) {
                $row = array_merge($row, (isset($out[$i][$heading[$j]]) ? array($out[$i][$heading[$j]]) : array(null)));
            }
            fputcsv($output, $row);
        }
    }
    
    public function getDomRsmJson($domId)
    {
        $data = Utils\DomUtils::getDomRsm($domId);
        return response()->json($data);
    }

    public function getRsmDealerJson($rsmId)
    {
        $data = Utils\RsmUtils::getRsmDealer($rsmId);
        return response()->json($data);
    }

    public function getRsmJson()
    {
        $data = Utils\RsmUtils::getAllRsm();
        return response()->json($data);
    }
    public function getDealerJson()
    {
        $data = Utils\DealerUtils::getAllDealer();
        return response()->json($data);
    }
    public function getDomDealerJson($domId)
    {
        $data = Utils\DomUtils::getDomDealer($domId);
        return response()->json($data);
    }
    public function getFilterParamsJson()
    {   
       $params['StoreId'] = Utils\StoreUtils::getStoreIdByUserId(auth()->id());
        // $data['regions'] = Utils\Common::getAllRegions();
        $data['clusters'] = Utils\Asmutils::getAllClusters($params['asmId']);
        $data['stores'] = Utils\Asmutils::getAllStores($params['asmId']);
        return response()->json($data);
    }
    public function getBackgroundExportJson()
    {
        $params = Input::all();
       $params['StoreId'] = Utils\StoreUtils::getStoreIdByUserId(auth()->id());
        // $params = CallLogUtils::getFilterParams($params);
        if (empty($params['Fields'])) {
            $params['Fields'] = Constants::$ADMIN_EXPORT_FIELDS;
        }
        $fields = CallLogUtils::mapFilterFields($params['Fields']);
        $keys = explode(',', $fields);
        $fields = array_fill_keys($keys, TRUE);

        $exportFlag = CallLogUtils::getCalllogsCount($params);
        return response()->json(['background_export' => $exportFlag]);
    }
    
    public function processExportJson()
    {
        $params = Input::all();
       $params['StoreId'] = Utils\StoreUtils::getStoreIdByUserId(auth()->id());
        Log::info('processExport Params: ', $params);

        $timestamp = date("YmdHis"). uniqid();
        $file = "RAW_$timestamp.csv";
        $path = storage_path() . '/app/exports';

        $db = Config::get('database.connections.mongodb');

        if (empty($params['Fields'])) {
            $params['Fields'] = Constants::$ADMIN_EXPORT_FIELDS;
        } 

        $params['Fields'] = CallLogUtils::mapFilterFields($params['Fields']);
        $filter_resp = CallLogUtils::getFilterConditionsForExport($params);
     
        Log::info("filter_resp:", [$filter_resp]);

        $CONDITIONS  = json_encode($filter_resp['conditions']);
        $FIELDS      = $filter_resp['fields'];
        $EXPORT_FILE = "RAW_$timestamp.csv";
        $EXPORT_PATH = storage_path() . '/app/exports';

        $timestamp = date("YmdHis"). uniqid();
        $shell_path = storage_path() . '/app/exports';
        $filePath = "$shell_path/$timestamp.csv";

        $match_json = json_encode($filter_resp['conditions']);
        $export_fields = $filter_resp['fields'];

        $dbConf = Config::get('database.connections.mongodb');
        $dbusername = $dbConf['username'];
        $dbpassword = $dbConf['password'];
        $dbdatabase = $dbConf['database'];
        $dbhost = $dbConf['host'];
        $dbcollection = 'calllogs';

        $bgCommand = "/usr/bin/nohup /bin/sh $shell_path/raw-export.sh '$match_json' '$export_fields' '$filePath' '$shell_path' '$timestamp' '$dbusername' '$dbpassword' '$dbdatabase' '$dbhost' '$dbcollection' > '$shell_path/log.txt' &";

        Log::info(print_r($bgCommand, TRUE));

        $this->runInBackground($bgCommand);

        return response()->json(['status' => false, 'reason' => 2, 'file' => "$timestamp.zip"]);
    }

    public function getCsvFileJson()
    {
        $params = Input::all();
       $params['StoreId'] = Utils\StoreUtils::getStoreIdByUserId(auth()->id());
        Log::info("getCsvFileJson params:", [$params['file']]);

        if(File::exists(storage_path('app/exports/'. $params['file']))) {
            return response()->json([
            'status' => true,
            'file' => $params['file'],
            'path' => asset('exports/'. $params['file'])
            ]);
        } else {
            return response()->json([
            'status' => false,
            'file' => $params['file'],
            'path' => asset('exports/'. $params['file'])
            ]);
        }
    }
    
    public function runInBackground($command)
    {
        $descriptorspec = array(
            0 => array("pipe", "r"), // stdin is a pipe that the child will read from
            1 => array("pipe", "w"), // stdout is a pipe that the child will write to
            2 => array("file", "/var/log/error-output.log", "a") // stderr is a file to write to
        );
        $cwd = storage_path() . '/app/exports';
        $env = array('some_option' => 'aeiou');
        Log::info("runInBackground command:", [$command]);
        Log::info($command);
        $process = proc_open("$command", $descriptorspec, $pipes, $cwd, $env);

        if (is_resource($process)) {
            // $pipes now looks like this:
            // 0 => writeable handle connected to child stdin
            // 1 => readable handle connected to child stdout
            // Any error output will be appended to /tmp/error-output.txt

            fwrite($pipes[0], '<?php print_r($_ENV); ?>');
            fclose($pipes[0]);

            $steam_cont = stream_get_contents($pipes[1]);
            Log::info("stream_get_contents :", [$steam_cont]);
            fclose($pipes[1]);

            // It is important that you close any pipes before calling
            // proc_close in order to avoid a deadlock
            $return_value = proc_close($process);

            Log::info("proc_open command returned:", ["command returned $return_value\n"]);
        }
    }

    public function runProcessOpen($command)
    {
        Log::info("runProcessOpen command:", [$command]);
        $handle = popen($command, 'r');
        Log::info("runProcessOpen command:", [ "'$handle'; " . gettype($handle) . "\n"]);
        $read = fread($handle, 2096);
        Log::info("runProcessOpen command:", [$read]);
        pclose($handle);
    }

    public function aiDebug($callid)
    {
        $callDetails = CallLogUtils::getCallDetails($callid);
        return view('admin.reports.ai_debug', ['callDetails' => $callDetails]);
    }

    public function departmentViseCallCount()
    {
        $params = Input::all();
       $params['StoreId'] = Utils\StoreUtils::getStoreIdByUserId(auth()->id());
        $from = isset($params['from']) ? $params['from']: NULL;
        $to = isset($params['to']) ? $params['to']: NULL;
        $data = ['from' => $from, 'to' => $to];
        $data = CallLogUtils::getDepartmentViseCallCount($data);
        return response()->json($data);
    }

    public function callStatusViseCount()
    {
        $params = Input::all();
       $params['StoreId'] = Utils\StoreUtils::getStoreIdByUserId(auth()->id());
        $from = isset($params['from']) ? $params['from']: NULL;
        $to = isset($params['to']) ? $params['to']: NULL;
        $department = isset($params['department']) ? $params['department']: NULL;
        $data = ['department' => $department, 'from' => $from, 'to' => $to];
        $data = CallLogUtils::getCallStatusViseCount($data);
        return response()->json($data);
    }

    public function dealerMissedCount()
    {
        $params = Input::all();
        $data = CallLogUtils::getDealerMissedCount($params);
        return response()->json($data);
    }

    public function uniqueAndRepeatedCount()
    {
        $params = Input::all();
       $params['StoreId'] = Utils\StoreUtils::getStoreIdByUserId(auth()->id());
        $data = CallLogUtils::getUniqueAndRepeatedCallerCount($params);
        return response()->json($data);
    }

    public function dashboard()
    {
        return view('admin.reports.dashboard');
    }

    public function monthWiseStatus()
    {
        $params = Input::all();
       $params['StoreId'] = Utils\StoreUtils::getStoreIdByUserId(auth()->id());
        $data = CallLogUtils::getMonthWiseStatus($params);
    
        $groupedData = [
            'month' => [],
            'data' => []
        ];
        $monthsOfYear = [
            '01' => 'Jan',
            '02' => 'Feb',
            '03' => 'Mar',
            '04' => 'Apr',
            '05' => 'May',
            '06' => 'Jun',
            '07' => 'Jul',
            '08' => 'Aug',
            '09' => 'Sep',
            '10' => 'Oct',
            '11' => 'Nov',
            '12' => 'Dec'
        ];
        if(!empty($data)) {
            foreach ($data as $value) {
                $month = substr($value['_id'], 0, 7);
                if(!in_array($month,$groupedData['month'])) {
                    $groupedData['month'][] = $month;
                    $groupedData['data'][$month] = [
                        'connected' => 0,
                        'not_connected' => 0,
                        'total' => 0
                    ];
                    $groupedData['data'][$month]['month'] = $monthsOfYear[substr($value['_id'], 5, 2)];
                    $groupedData['data'][$month]['year'] = substr($value['_id'], 0, 4);
                }
                if(in_array($month,$groupedData['month'])) {
                    $groupedData['data'][$month]['connected']+=$value['connected'];
                    $groupedData['data'][$month]['not_connected']+=$value['not_connected'];
                    $groupedData['data'][$month]['total']+=$value['total'];
                }
            }
            unset($groupedData['month']);
            $dat = [];
            foreach ($groupedData['data'] as $value) {
                $dat[] = $value;
            }
        } else {
            $dat = [];
        }
        return response()->json($dat);
    }

    public function storeMissedCount()
    {
        $params = Input::all();
       $params['StoreId'] = Utils\StoreUtils::getStoreIdByUserId(auth()->id());
        $data = CallLogUtils::getStoreMissedCount($params);
        return response()->json($data);
    }
    public function getFilterClusters(){
        $params = Input::all();
       $params['StoreId'] = Utils\StoreUtils::getStoreIdByUserId(auth()->id());
        $data['clusters'] = Utils\Asmutils::getFilterClusters($params['region'],$params['asmId']);
        return response()->json($data);
    }
    public function getFilterStores(){
        $params = Input::all();
       $params['StoreId'] = Utils\StoreUtils::getStoreIdByUserId(auth()->id());
        $data['stores'] = Utils\Asmutils::getFilterStores($params['region'],$params['cluster'],$params['asmId']);
        return response()->json($data);
    }


}

