<?php

namespace App\CustomLibraries\Utils;

use Illuminate\Support\Facades\DB;

/**
 * Description of Common
 *
 * @author waybeo
 */
class StoreUtils
{

    public static function getAllStores()
    {
        return DB::table('stores')->distinct()->pluck('store_code', 'id');
    }
    public static function getFilterStores($regionId = NULL, $clusterId = NULL)
    {
        $data = DB::table('stores')->distinct();
        if (!empty($regionId)) {
            $data = $data->leftJoin('clusters', 'stores.cluster_id', '=', 'clusters.id')
                ->leftJoin('regions', 'clusters.region_id', '=', 'regions.id')
                ->where('clusters.region_id', $regionId);
        }
        if (!empty($clusterId)) {
            $data = $data->where('cluster_id', $clusterId);
        }
        $data = $data->pluck(DB::raw("CONCAT(store_code,'-',location)"), 'stores.id');
        return $data;
    }
    public static function getStoreDataByVirtualNumber($virtualNumber)
    {
        return DB::table('stores')
            ->leftJoin('numbers', 'stores.id', '=', 'numbers.store_id')
            ->leftJoin('cities', 'stores.city_id', '=', 'cities.id')
            ->leftJoin('states', 'cities.state_id', '=', 'states.id')
            ->leftJoin('zones', 'states.zone_id', '=', 'zones.id')
            ->where('sim_number', $virtualNumber)
            ->get([
                'stores.actual_store_id as store_code', 'stores.store_name', 'stores.store_numbers', 
                'stores.type','stores.locality', 'stores.id as storeId',
                'cities.city', 'cities.id as city_id', 'states.state','states.id as state_id',
                'zones.id as zone_id', 'zones.zone'
            ])
            ->first();
    }
    public static function storeTypes()
    {
        return DB::table('stores')
            ->distinct()
            ->pluck('type');
    }
    public static function getStoreIdByUserId($userId)
    {
        return DB::table('stores')
            ->where('user_id',$userId)
            ->value('id');
    }
}
