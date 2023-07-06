<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Store;
use App\City;
use App\State;
use App\Number;
use App\StoreManager;
use App\CustomLibraries\Utils\CallFlowUtils;
use Log;

class StoreController extends Controller
{
    public function details()
    {   
     
        $stores = Store::leftJoin('numbers','stores.id','=','numbers.store_id')
            ->leftJoin('cities', 'stores.city_id', '=', 'cities.id')
            ->leftJoin('states', 'cities.state_id', '=', 'states.id')
            ->leftJoin('zones', 'states.zone_id', '=', 'zones.id')
            ->whereNull('stores.deleted_at')
            ->get([
                'stores.actual_store_id', 'stores.store_name', 'stores.store_numbers', 
                'stores.type','stores.locality', 'stores.id as storeId', 'stores.address',
                'cities.city', 'cities.id as city_id', 'states.state','states.id as state_id',
                'zones.id as zone_id', 'zones.zone', 'numbers.sim_number', 'stores.post_code as postcode',
                'stores.store_email', 'stores.id'
            ]);
        $cities = City::select('id','city')
                    ->get();

        $states = State::distinct()->pluck('state','id');

        $numbers = Number::select('id', 'sim_number')
                    ->where('in_use', '=', 0)
                    ->get();

        $vno = DB::table('numbers')->select('id','sim_number')->where('in_use', '=', 0)->first('sim_number');
        $no = json_decode(json_encode($vno), True);
        return view('admin.store.details',['stores' => $stores,'cities' => $cities,'states' => $states, 'numbers' => $numbers, 'vno' => $no]);
    }

    public function edit($store_id)
    {
        $store = Store::leftJoin('numbers','stores.id','=','numbers.store_id')
            ->leftJoin('cities','stores.city_id','=','cities.id')
            ->leftJoin('states','cities.state_id','=','states.id')
            ->whereNull('stores.deleted_at')
            ->where('stores.id', $store_id)
            ->get(['stores.id','store_name','stores.actual_store_id','stores.locality as location', 'stores.store_email','stores.store_numbers',
                DB::raw('replace(numbers.sim_number,"+91","") as sim_number'),'state','city','stores.address','states.id as sid','cities.id as cid',
                'stores.working_hours','states.id as state_id', 'states.state','cities.id as city_id','cities.city'
            ])
            ->first();

        $states = DB::table('states')
                    ->select('state','id')
                    ->get()->toArray();

        $cities = DB::table('cities')
                    ->select('city','id')
                    ->get()->toArray();  
            
        return view('admin.store.update', ['store' => $store,'states' => $states,'cities' => $cities]);
    }
    public function state(Request $request)
    {
        $id = $request->input('state');
        $city = DB::table('cities')
            ->select('id','city')
            ->where("state_id",$id)
            ->get();
        return response()->json(['status' => true, 'data' => ['city' => $city]]);
    }
    public function create()
    {
        $params = Input::all();
        $bno = $params['sno'];
        $bno_array = explode(",",$bno);
        $s_no = json_encode($bno_array);
        $store_code = $params['scode'];
        $s_code = DB::table('stores')->select('store_code')->where('store_code', '=', $store_code)->first();
        if(!empty($s_code)){
         return response()->json(['status' => true,'s_code' => $s_code]);
        }
      
        $insertData = [
            'store_code' => $params['scode'],
            'store_name' => $params['sname'],
            'store_number' => $s_no,
            'store_manager' => $params['mname'],
            'store_address' => $params['address'],
            'state_id' => $params['state'],
            'working_hours' => $params['working_hours'],
            'created_at' => date('Y-m-d H:i:s'), 
            'city_id' => $params['city'],
        ];
        $id = DB::table('stores')->insertGetId($insertData);
        $updateData = [
            'in_use' => 1, 
            'store_id' => $id
        ];
        DB::table('numbers')->where('id',$params['vid'])->update($updateData);
        
        return response()->json(['status' => true, 'uid' => $id, 'msg'=>'User Created successfully']);
    }

    public function update()
    {
        $params = Input::all();
        if(!empty($params['store_numbers']) || !empty($params['id']) || !empty($params['sim_number'])){
            $store_numbers = '["+91' . implode ('","+91', $params['store_numbers'] ) . '"]';
            $updateData = [
                'store_name' => $params['name'],
                'store_email' => $params['email'],
                'address' => $params['address'],
                'city_id' => $params['city'],
                'locality' => $params['location'],
                'store_numbers' => $store_numbers,
                'working_hours' => $params['working_hours'], 
                'city_id' => $params['city'],

            ];
            $updated_user = Auth::id();
            $updated_store_row = array_merge($updateData, [
                'store_id' => $params['id'],
                'actual_store_id' => $params['actual_store_id'],
                'updated_by' => $updated_user,
                'action' => 'EDIT'

            ]);
            DB::table('stores')->where('id', $params['id'])->update($updateData);
            $id = DB::table('store_updates')->insertGetId($updated_store_row);
            $vn = DB::table('numbers')->where('store_id', $params['id'])->value('sim_number');
            $callFlow = CallFlowUtils::buildCallFlow($vn, $store_numbers, $params['working_hours']);
            Log::info('Callflow Request: ');
            Log::info($callFlow);
            $response = CallFlowUtils::updateCallFlow($callFlow);
            if (!empty($response['status']) && $response['status'] == 'failed') {
                return response()->json(['status' => false, 'message' => 'Update Failed']);
                /*Send Alert*/
            } else {
                return response()->json(['status' => true, 'message' => 'Updated Successfully']);
            }
        }else{
            return response()->json(['status' => false, 'message' => 'Update Failed']);
        }
       
    }

    public function editWorkingHours($id)
    {
        $working_hours = DB::table('stores')->select('working_hours')
                        ->where('stores.id', '=' ,$id)
                        ->first();
        return response()->json(['status' => true,'working_hours' => $working_hours->working_hours]);
    }
    
    public function checkvirtualnumber()
    {
        $numbers = DB::table('numbers')
                ->select('sim_number')
                ->where('in_use','=',0)
                ->get();
        return response()->json(['status' => true,'numbercheck' => $numbers]);
    }

    public function delete($id)
    {
        $no_id = DB::table('numbers')->select('id')->where('store_id','=' ,$id)->pluck('id');
        DB::table('stores')->where('id','=' ,$id)->update(['deleted_at'=> date('Y-m-d H:i:s'),'is_active' => 0]);
        DB::table('numbers')->where('store_id','=' ,$id)->delete($no_id);
        return response()->json(['status' => true]);

    }

    public function getAllFilterParams()
    {
        $asms = Asmutils::getAllAsms();
        $clusters = \App\CustomLibraries\Utils\ClusterUtils::getAllClusters();
        $rms = \App\CustomLibraries\Utils\Rmutils::getAllRms();
        $regions = DB::table('regions')
            ->pluck('region', 'id');
        $response = [
            'asms' => $asms,
            'clusters' => $clusters,
            'rms' => $rms,
            'regions' => $regions
        ];
        return response()->json($response);
    }
}
