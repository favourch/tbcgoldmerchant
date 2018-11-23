<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LevelTwoUser extends Model
{
    protected $table = 'level_two_users';

    public function user_detail()
    {
    	return $this->hasOne('App\Models\UserDetail', 'user_id', 'user_id');
    }

    public function user()
    {
    	return $this->belongsTo('App\User', 'user_id');
    }
}
