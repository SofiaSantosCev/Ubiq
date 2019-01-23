<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
	use SoftDeletes;
    public function role()
    {
        return $this->belongsTo('App\Role');
    }

    public function locations(){
    	return $this->hasMany('App\Location');
    }
}
