<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    public $timestamps = true;
    protected $table = 'cities';
    public function activeStores()
    {
        return $this->hasOne('App\Store','city_id','id');
    }
}
