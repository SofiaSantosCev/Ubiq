<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
	use SoftDeletes;
	use Notifiable;

	protected $fillable = ['password'];
    
    public function role()
    {
        return $this->belongsTo('App\Role');
    }

    public function locations(){
    	return $this->hasMany('App\Location');
    }
}
