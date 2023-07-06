<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
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

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function sample(){
        $data = MailUtils::get24HourNationalData();
        $mtd = MailUtils::getMtdNationalData();
        $data['zones'] = MailUtils::getRdsAllZones();
        $data['regions'] = MailUtils::getRdsAllRegions();
        foreach ($data['zones'] as $zone){
            //Within24TAT
            if(empty($data['zone']['uniqueLeads'][$zone] ))
            {
                $data['zone']['uniqueLeads'][$zone] = 0;
            }
            if(empty($data['zone']['uniqueAttempted'][$zone] ))
            {
                $data['zone']['uniqueAttempted'][$zone] = 0;
            }
            if(empty($data['zone']['connected'][$zone] ))
            {
                $data['zone']['connected'][$zone] = 0;
            }
            //MTD
            if(empty($mtd['zone']['uniqueLeads'][$zone] ))
            {
                $mtd['zone']['uniqueLeads'][$zone] = 0;
            }
            if(empty($mtd['zone']['uniqueAttempted'][$zone] ))
            {
                $mtd['zone']['uniqueAttempted'][$zone] = 0;
            }
            if(empty($mtd['zone']['connected'][$zone] ))
            {
                $mtd['zone']['connected'][$zone] = 0;
            }
        }
        $data['zone']['uniqueLeads']['total'] = array_sum( $data['zone']['uniqueLeads']);
        $data['zone']['uniqueAttempted']['total'] = array_sum( $data['zone']['uniqueAttempted']);
        $data['zone']['connected']['total'] = array_sum( $data['zone']['connected']);
        $data['zone']['uniqueAttempted']['%'] = ($data['zone']['uniqueLeads']['total'] > 0) ? round($data['zone']['uniqueAttempted']['total']/$data['zone']['uniqueLeads']['total'] * 100 ,1) : 0;
        $data['zone']['connected']['%'] = ($data['zone']['uniqueLeads']['total'] > 0) ? round($data['zone']['connected']['total']/$data['zone']['uniqueLeads']['total'] *100 ,1): 0;

        $mtd['zone']['uniqueLeads']['total'] = array_sum( $mtd['zone']['uniqueLeads']);
        $mtd['zone']['uniqueAttempted']['total'] = array_sum( $mtd['zone']['uniqueAttempted']);
        $mtd['zone']['connected']['total'] = array_sum( $mtd['zone']['connected']);
        $mtd['zone']['uniqueAttempted']['%'] = ($mtd['zone']['uniqueLeads']['total'] > 0) ? round($mtd['zone']['uniqueAttempted']['total']/$mtd['zone']['uniqueLeads']['total'] * 100 ,1) : 0;
        $mtd['zone']['connected']['%'] = ($mtd['zone']['uniqueLeads']['total'] > 0) ? round($mtd['zone']['connected']['total']/$mtd['zone']['uniqueLeads']['total'] *100 ,1): 0;

        foreach($data['regions'] as $region){
            //Within24TAT
            if(empty($data['region']['uniqueLeads'][$region] ))
            {
                $data['region']['uniqueLeads'][$region] = 0;
            }
            if(empty($data['region']['uniqueAttempted'][$region] ))
            {
                $data['region']['uniqueAttempted'][$region] = 0;
            }
            if(empty($data['region']['connected'][$region] ))
            {
                $data['region']['connected'][$region] = 0;
            }

            //MTD
            if(empty($mtd['region']['uniqueLeads'][$region] ))
            {
                $mtd['region']['uniqueLeads'][$region] = 0;
            }
            if(empty($mtd['region']['uniqueAttempted'][$region] ))
            {
                $mtd['region']['uniqueAttempted'][$region] = 0;
            }
            if(empty($mtd['region']['connected'][$region] ))
            {
                $mtd['region']['connected'][$region] = 0;
            }
        }
        $data['region']['uniqueLeads']['total'] = array_sum( $data['region']['uniqueLeads']);
        $data['region']['uniqueAttempted']['total'] = array_sum( $data['region']['uniqueAttempted']);
        $data['region']['connected']['total'] = array_sum( $data['region']['connected']);
        $data['region']['uniqueAttempted']['%'] = ($data['region']['uniqueLeads']['total'] > 0) ? round($data['region']['uniqueAttempted']['total']/$data['region']['uniqueLeads']['total'] * 100 ,1) : 0;
        $data['region']['connected']['%'] = ($data['region']['uniqueLeads']['total'] > 0) ? round($data['region']['connected']['total']/$data['region']['uniqueLeads']['total'] *100 ,2): 0;

        $mtd['region']['uniqueLeads']['total'] = array_sum( $mtd['region']['uniqueLeads']);
        $mtd['region']['uniqueAttempted']['total'] = array_sum( $mtd['region']['uniqueAttempted']);
        $mtd['region']['connected']['total'] = array_sum( $mtd['region']['connected']);
        $mtd['region']['uniqueAttempted']['%'] = ($mtd['region']['uniqueLeads']['total'] > 0) ? round($mtd['region']['uniqueAttempted']['total']/$mtd['region']['uniqueLeads']['total'] * 100 ,1) : 0;
        $mtd['region']['connected']['%'] = ($mtd['region']['uniqueLeads']['total'] > 0) ? round($mtd['region']['connected']['total']/$mtd['region']['uniqueLeads']['total'] *100 ,1): 0;

        $data['mtd'] = $mtd;
        // dd($data);
        echo '<pre>',print_r($data),'</pre>';
        echo print_r($data);
        exit;

        return view('mail.national-outbound-report',$data);
    }
}
