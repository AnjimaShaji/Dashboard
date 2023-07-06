<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
    use SoftDeletes;
    // protected $casts = [ 'fx_contact' => 'json','holidays_contact' => 'json'];

    protected $table = 'stores';
    public function activestoreManagers()
    {
        return $this->hasOne('App\StoreManager','id','store_manager_id');
    }
    public function activeCities()
    {
        return  $this->hasMany('App\City','id','city_id');
    }
    public static function getStoreIdByUserId($userId){

        $store = new Store();
        return  $store->where('user_id',$userId)->value('id');
    }
}