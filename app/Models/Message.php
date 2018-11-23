<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'messages';

    protected $fillable = [
    	'admin_id', 'user_id', 'subject', 'message'
    ];

    public function user()
    {
    	return $this->belongsTo('App\User', 'user_id');
    }

    public function admin()
    {
    	return $this->belongsTo('App\Admin', 'admin_id');
    }

    public function user_detail()
    {
        return $this->belongsTo('App\Models\UserDetail', 'user_id', 'user_id');
    }
}
