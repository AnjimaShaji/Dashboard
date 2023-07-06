<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'role'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Get the dom for the users
     */
    public function dom()
    {
        return $this->hasMany('App\Dom');
    }

    /**
     * Get the rsm for the users
     */
    public function rsm()
    {
        return $this->hasMany('App\Rsm');
    }

    /**
     * Get the dealer for the users
     */
    public function dealer()
    {
        return $this->hasMany('App\Dealer');
    }
}
