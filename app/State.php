<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    public $timestamps = true;
    protected $table = 'states';
    public function cities()
    {
        return $this->hasMany('App\City','state_id','id');
    }
}