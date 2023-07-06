<?php

namespace App\Http\Controllers\Callback;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Log;
use MongoId;
use App\CustomLibraries\Calllog;
use Illuminate\Support\Facades\Input;
use App\CustomLibraries\Utils\DealerUtils;



class CallbackController extends Controller
{
    public function index(Request $request)
    {
        Log::info('Request Received in Callback Controller');
        $_SERVER['PHP_AUTH_USER'] = $request->header('PHP_AUTH_USER');
        $_SERVER['PHP_AUTH_PW'] = $request->header('PHP_AUTH_PW');

        if (! self::pc_validate($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'])) { 
            header('HTTP/1.0 401 Unauthorized');
            echo "Invalid username or password"; 
            exit; 
        }else {
            Log::info('Request Received in Callback Controller');
            $params = Input::all();
            Log::info('Params',$params);
            if (!empty($params) && !empty($params['callId'])) {
                $filePath = storage_path().'/app/CallbackData/NotProcessed/';
                $fileName = $params['callId'];
                $file = fopen($filePath.$fileName, 'w');
                fwrite($file, json_encode($params));
            } else {
                return response()->json(['status' =>'failed','message' => 'empty params']);
            }
        }                                                                                                  
    }

    
    public function pc_validate($user,$pass) 
    { 
        $users = ['way_pureit' => 'pureit#sw114ff45d1!42']; 
        if (isset($users[$user]) && ($users[$user] == $pass)) 
            { 
                return true; 
            }else { 
                return false; 
            } 
    }
}
