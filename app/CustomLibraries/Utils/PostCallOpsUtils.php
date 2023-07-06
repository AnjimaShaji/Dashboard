<?php

namespace App\CustomLibraries\Utils;

use Illuminate\Support\Facades\DB;
use App\CustomLibraries\Calllog;
use App\CustomLibraries\CeatSFDCUtils;
use App\CustomLibraries\Utils\StoreUtils;
use App\CustomLibraries\DatalayerUtils;
use App\Store;
use App\City;
use App\State;
use App\Number;
use MongoId;
use MongoDate;
use Log;


class PostCallOpsUtils
{


    public static function index($params)
    {

        $params = json_decode($params, true);
        if (!empty($params['callId'])) {
            if (!empty($params['virtualNumber'])) {
                $storeData = (array) self::getStoreData($params['virtualNumber']);
                if (empty($storeData)) {
                    return false;
                }
                if (!empty($params['dtmfKeys'])) {
                    $dtmfKey =  $params['dtmfKeys'][0];
                    $call_type = ['1' => 'Sales', '2' => 'Service'];
                    $params['CallType'] = $call_type[$dtmfKey];
                }
                $params = array_merge($params, $storeData);
                $calllog = self::setBasicCalllogData($params);
                $mongo_resp = self::_insertCalllog($calllog);
                if (!empty($mongo_resp['ok'])) {
                    self::siintegration($calllog);
                    return true;
                } else {
                    $chat_id = '-664319953';
                    $message =  'Hi Pureit, calllog insert failed for data. ' . json_encode($data);
                    self::sendTelegramAlert($chat_id, $message);
                }
            } else {
                return false;
            }
        }
    }

    private static function _insertCalllog($params)
    {
        $calllog = new Calllog();
        try {
            $resp = $calllog->mongoStr->insert($params);
        } catch (\Exception $e) {
            if ($e->getCode() == 64) {
                echo "waiting for replication timed out.\n";
                $resp = $e->getMessage();
            } else {
                $resp = $e->getMessage();
            }
        }
        return $resp;
    }
    public static function siintegration($params)
    {
        Log::info('Pureit - API integration');
        Log::info('Pureit Params:', $params);
        $data = self::mapFieldsForApi($params);
        $info = json_encode($data);
        Log::info('Pureit Request:');
        Log::info($info);
        $response = self::apiCurl($data);
        $httpcode = $response['httpcode'];
        Log::info('Pureit Curl Response:');
        $resp = json_encode($response);
        Log::info($resp);
        $csv_data = [
            'date' => date("Y-m-d H:i:s"),
            'simnumber' => $params['VirtualNumber'],
            'request' => $info,
            'response' => $resp,
            'httpcode' => $httpcode,
            'callId' => $params['CallId']
        ];

        $path = base_path();
        $file = fopen($path . "/storage/app/singleinterface/pureit_api_log.csv", 'a');
        fputcsv($file, $csv_data);
        fclose($file);
    }

    private static function apiCurl($data)
    {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://call-api.singleinterface.com/");
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSLVERSION, 0);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($ch);
        Log::info('Response:');
        Log::info($result);
        $output = json_decode($result, true);
        if ($output == FALSE) {
            $output['response'] = $result;
        }
        $output['httpcode'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($output['httpcode'] != "200") {
            $chat_id = '-664319953';
            $message =  'An API push failure with Pureit. ' . json_encode($data);
            self::sendTelegramAlert($chat_id, $message);
        }
        Log::info('SI Curl Response Code: ');
        Log::info($output['httpcode']);
        $error = curl_error($ch);
        Log::info('SI Curl Error: ');
        Log::info($error);
        curl_close($ch);
        return $output;
    }

    private static function mapFieldsForApi($data)
    {
        $push_data = [
            "SECRETKEY" => "70856e7628aae222fe09b86a3e4b7a13",
            "AUTHKEY" => "WT*i%P2q",
            "customer_name" => "Pureit",
            "publisher_type" => "waybeo",
            "lead_type" => "singleinterface",
            "virtual_number_type" => "singleinterface",

        ];
    
        if (!empty($data['CallStartTime'])) {
            $push_data['call_start_time'] = $data['CallStartTime'];
        }

        if (!empty($data['CallEndTime'])) {
            $push_data['call_end_time'] = $data['CallEndTime'];
        }

        if (!empty($data['Status'])) {
            $push_data['call_type'] = $data['Status'];
        }

        if (!empty($data['Location'])) {
            $push_data['Location'] = $data['Location'];
        }

        if (!empty($data['VirtualNumber'])) {
            $push_data['virtual_number'] = $data['VirtualNumber'];
        }

        if (!empty($data['CustomerNumber'])) {
            $push_data['customer_number'] = $data['CustomerNumber'];
        }

        if (!empty($data['ConversationDuration'])) {
            $push_data['call_duration'] = $data['ConversationDuration'];
        }

        if (!empty($data['TotalDuration'])) {
            $push_data['total_durations'] = $data['TotalDuration'];
        }

        if (!empty($data['CallRecordUrl']) && $data['Status'] == 'Connected') {
            $push_data['call_recording_url'] = $data['CallRecordUrl'];
        }
        return $push_data;
    }



    public static function _sendSmsInfobip($phone, $content, $sender = NULL)
    {
        if (empty($sender)) {
            $sender = 'SotcMO';
        }
        $phone = ltrim($phone, '+');
        if (
            strlen($phone) == 12 &&
            920 > (int) substr($phone, 0, 3) &&
            916 <= (int) substr($phone, 0, 3)
        ) {
            self::triggerCurl($sender, $phone, $content);
        }
    }

    public static function triggerCurl($sender, $phone, $content)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://lzzlmw.api.infobip.com/sms/2/text/advanced",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => '{"messages": [{"from": "' . $sender . '","destinations": [{"to": "' . $phone . '"}],"text": "' . $content . '"}]}',
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: Basic d2F5YmVvOndheWJlb0BiRTE1MTI="
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        print_r($response);
    }
    private static function saveCalllog($params)
    { }
    private static function getStoreData($virtualNumber)
    {
        $storeData = StoreUtils::getStoreDataByVirtualNumber($virtualNumber);
        return $storeData;
    }
    private static function setBasicCalllogData($params)
    {
        if (!empty($params['callId'])) {
            $calllog['CallId'] = $params['callId'];
        }
        if (!empty($params['uniqueId'])) {
            $calllog['UniqueId'] = $params['uniqueId'];
        }
        if (!empty($params['dateTime'])) {
            $calllog['CallStartTime'] = date('Y-m-d H:i:s', $params['dateTime'] / 1000);
            $calllog['ISODate'] = new MongoDate($params['dateTime'] / 1000 + 19800);
            $calllog['Date'] = date('Y-m-d', $params['dateTime'] / 1000);
        }
        if (!empty($params['totalDuration'])) {
            $calllog['TotalDuration'] = $params['totalDuration'];
            $callEndTime = strtotime($calllog['CallStartTime']) + $calllog['TotalDuration'];
            $calllog['CallEndTime'] = date('Y-m-d H:i:s', $callEndTime);
        } else {
            $calllog['TotalDuration'] = 0;
        }
        if (!empty($params['conversationDuration'])) {
            $calllog['ConversationDuration'] = $params['conversationDuration'];
        } else {
            $calllog['ConversationDuration'] = 0;
        }
        if (!empty($params['ivrDuration'])) {
            $calllog['IVRDuration'] = $params['ivrDuration'];
        } else {
            $calllog['IVRDuration'] = 0;
        }
        if (!empty($params['ringDuration'])) {
            $calllog['RingDuration'] = $params['ringDuration'];
        } else {
            $calllog['RingDuration'] = 0;
        }
        if (!empty($params['callerNumber'])) {
            $calllog['CustomerNumber'] = (int) substr($params['callerNumber'], -10);
        }
        if (!empty($params['busyCallees'])) {
            $calllog['BusyCallees'] = $params['busyCallees'];
        }
        if (!empty($params['busyCalleesStr'])) {
            $calllog['BusyCalleesStr'] = $params['busyCalleesStr'];
        }
        if (!empty($params['answeredBy'])) {
            $calllog['AgentNumber'] = $params['answeredBy'];
        }
        if (!empty($params['callRecordUrl'])) {
            $calllog['CallRecordUrl'] = $params['callRecordUrl'];
        }
        if (!empty($params['gateway'])) {
            $calllog['gateway'] = $params['gateway'];
        }
        if (!empty($params['correlationId'])) {
            $calllog['CorrelationId'] = $params['correlationId'];
        }
        if (!empty($params['virtualNumber'])) {
            $calllog['VirtualNumber'] = (int) substr($params['virtualNumber'], -10);
        }
        if(!empty($params['AgentNumber'])){
            $calllog['AgentNumber'] = (int) substr($params['AgentNumber'], -10);
        }
        if (!empty($params['CallType'])) {
            $calllog['CallType'] = $params['CallType'];
        }
        $calllog['HangupLeg'] = !empty($params['hangupLeg']) ? $params['hangupLeg'] : NULL;
        $calllog['Status'] = $params['status'];
        $calllog['callerStatus'] = !empty($params['callerStatus']) ? $params['callerStatus'] : NULL;
        $calllog['calleeStatus'] = !empty($params['calleeStatus']) ? $params['calleeStatus'] : NULL;
        $calllog['StoreId'] = !empty($params['storeId']) ? $params['storeId'] : NULL;
        $calllog['StoreCode'] = !empty($params['store_code']) ? $params['store_code'] : NULL;
        $calllog['StoreName'] = !empty($params['store_name']) ? $params['store_name'] : NULL;
        $calllog['Location'] = !empty($params['locality']) ? $params['locality'] : NULL;
        $calllog['CityId'] = !empty($params['city_id']) ? $params['city_id'] : NULL;
        $calllog['City'] = !empty($params['city']) ? $params['city'] : NULL;
        $calllog['StateId'] = !empty($params['state_id']) ? $params['state_id'] : NULL;
        $calllog['State'] = !empty($params['state']) ? $params['state'] : NULL;
        $calllog['ZoneId'] = !empty($params['zone_id']) ? $params['zone_id'] : NULL;
        $calllog['Zone'] = !empty($params['zone']) ? $params['zone'] : NULL;
        $calllog['StoreType'] = !empty($params['type']) ? $params['type'] : NULL;
        return $calllog;
    }
    
    public static function sendTelegramAlert($chat_id, $messge)
    {
        $chb = curl_init();
        curl_setopt($chb, CURLOPT_URL, "https://api.telegram.org/bot1064015300:AAGsd7H3aC7Cj53L655MRkp8CNwwXMuA7_w/sendMessage");
        curl_setopt($chb, CURLOPT_HEADER, false);
        curl_setopt($chb, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($chb, CURLOPT_SSLVERSION, 0);
        curl_setopt($chb, CURLOPT_POST, true);
        curl_setopt($chb, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($chb, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($chb, CURLOPT_POSTFIELDS, ['chat_id' => $chat_id, 'text' => $messge]);
        $result = curl_exec($chb);
        if (!empty($result["ok"])) {
            return true;
        }
        return false;
    }

}
