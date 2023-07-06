<?php

namespace App\CustomLibraries\Utils;
use Illuminate\Support\Facades\DB;
use Log;
/**
 * Description of Common
 *
 * @author waybeo
 */
class CallFlowUtils {


    public static function buildCallFlow($virtualNumber, $storeNumber, $workingHours)
    {
        $callFlowJson = Constants::$CALLFLOW_JSON;
        $callFlowArr = json_decode($callFlowJson, true);

        if(!empty($virtualNumber) && !empty($storeNumber)) {
            $callFlowArr['number'] = $virtualNumber;
            if(!empty($storeNumber)) {
                $fxContactLen = NULL;
                $fxContactArr = json_decode($storeNumber,true);
                $participants = [];
                $fxContactLen = count($fxContactArr);
                foreach ($fxContactArr as $key => $agent) {
                    if(strlen($agent) == 10) {
                        $agent = "+91$agent";
                    }
                    if($key < ($fxContactLen - 1) ){
                        $participants[] = ["name" => "", "number" => $agent , "timeout" => "25" ];
                    } else{
                        $participants[] = ["name" => "", "number" => $agent, "timeout" => "60" ];
                    }
                }
                if(!empty($participants)) {
                    $callFlowArr['callflow'][0]['WORKING_HOUR']['true'][0]['MENU']['dtmf_logic'][2]['logic'][1]['CONNECT_GROUP']['participants'] = $participants;
                    $callFlowArr['callflow'][0]['WORKING_HOUR']['true'][0]['MENU']['dtmf_logic'][1]['logic'][1]['CONNECT_GROUP']['participants'] = $participants;
                }
            }
            
            if(!empty($workingHours)) {
                $workingHours = str_replace('working_', '', $workingHours);
            } else {
                $workingHours = ['all' => ['from' => '0900', 'to' => '1800']];
            }
            $callFlowArr['callflow'][0]['WORKING_HOUR']['config'] = json_decode($workingHours,true);
            return json_encode($callFlowArr);
        } else {
            /*No Agents*/
        }
    }

    public static function createCallFlow($callFlowJson)
    {
        $url = 'http://callflow.waybeo.com/create-callflow';
        $headers = [
            'Accept:application/json',
            'Content-Type:application/json', 
            'Authorization:Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IlB1cmVpdCIsImlhdCI6MTUxNjIzOTAyMn0.c4IWLj3UUmusvNkHHlt8TSiutc_w_ihcO2Qq0KuVTEs'
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $callFlowJson);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        $response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if($response_code == 200) {
            $result = json_decode($result, true);
            if(is_array($result)) {
                $result = array_merge($result, ['status' => 'success']);
            } else {
                $result = ['status' => 'success', 'response' => []];
            }
        } else {
            $result = ['status' => 'failed'];
        }
        return $result;
    }

    public static function updateCallFlow($callFlowJson)
    {
        $url = 'http://callflow.waybeo.com/update-callflow';
        $headers = [
            'Accept:application/json',
            'Content-Type:application/json', 
            'Authorization:Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IlB1cmVpdCIsImlhdCI6MTUxNjIzOTAyMn0.c4IWLj3UUmusvNkHHlt8TSiutc_w_ihcO2Qq0KuVTEs'
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $callFlowJson);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        $response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if($response_code == 200) {
            $result = json_decode($result, true);
            if(is_array($result)) {
                $result = array_merge($result, ['status' => 'success']);
            } else {
                $result = ['status' => 'success', 'response' => []];
            }
        } else {
            $result = ['status' => 'failed'];
        }
        return $result;
    }
}
