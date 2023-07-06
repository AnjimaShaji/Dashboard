<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\CustomLibraries\CallLogUtils;

class ZeroCallStoresController extends Controller
{
    public function index(Request $r)
    {
        $params = $r->all();
        if (empty($params['date_from'])) {
            $params['date_from'] = date('Y-m-d', strtotime(date('Y-m-d') . 'first day of this month'));
        }
        if (empty($params['date_to'])) {
            $params['date_to'] = date("Y-m-d");
        }
        $stores = CallLogUtils::getStoreIds($params);
        $paginator = \DB::table('stores')
            ->select('stores.id', 'stores.store_name', 'stores.locality as location', 'stores.actual_store_id as store_code', 'numbers.sim_number')
            ->join('numbers', 'numbers.store_id', '=', 'stores.id')
            ->whereNull('stores.deleted_at')
            ->whereNotIn('stores.id', $stores);
        $paginator = $paginator->paginate(20);
        $data = [
            'params' => $params,
            'stores' => $paginator
        ];
        return view('admin.reports.zero-call-stores', $data);
    }
    public function export(Request $r)
    {
        $params = $r->all();
        if (empty($params['date_from'])) {
            $params['date_from'] = date('Y-m-d', strtotime(date('Y-m-d') . 'first day of this month'));
        }
        if (empty($params['date_to'])) {
            $params['date_to'] = date("Y-m-d");
        }
        $stores = CallLogUtils::getStoreIds($params);
        $zeroCallStores = \DB::table('stores')
            ->select('stores.id', 'stores.store_name', 'stores.locality as location', 'stores.actual_store_id as store_code', 'numbers.sim_number')
            ->join('numbers', 'numbers.store_id', '=', 'stores.id')
            ->whereNull('stores.deleted_at')
            ->whereNotIn('stores.id', $stores)->get();
        // output headers so that the file is downloaded rather than displayed
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=data.csv');

        // create a file pointer connected to the output stream
        $output = fopen('php://output', 'w');
        $out = [[]];
        $index = 0;
        $heading = [
            'Store Code',
            'Store Name',
            'Location',
            'Virtual Number'
        ];
        foreach ($zeroCallStores as $store) {
            if (isset($store->store_code)) {
                $out[$index]['Store Code'] = $store->store_code;
                if (!in_array('Store Code', $heading))
                    $heading = array_merge($heading, ['Store Code']);
            }
            if (isset($store->store_name)) {
                $out[$index]['Store Name'] = $store->store_name;
                if (!in_array('Store Name', $heading))
                    $heading = array_merge($heading, ['Store Name']);
            }
            if (isset($store->location)) {
                $out[$index]['Location'] = $store->location;
                if (!in_array('Location', $heading))
                    $heading = array_merge($heading, ['Location']);
            }
            
            if (isset($store->sim_number)) {
                $out[$index]['Virtual Number'] = $store->sim_number;
                if (!in_array('Virtual Number', $heading))
                    $heading = array_merge($heading, ['Virtual Number']);
            }
            $index++;
        }
        $count = $index;
        fputcsv($output, $heading);
        // output the rows
        for ($i = 0; $i <= $count; $i++) {
            $row = [];
            for ($j = 0; $j < count($heading); $j++) {
                $row = array_merge($row, (isset($out[$i][$heading[$j]]) ? array($out[$i][$heading[$j]]) : array(null)));
            }
            fputcsv($output, $row);
        }
    }
}
