<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashoutTransaction extends Model
{
    protected $table = 'cashout_transactions';

    protected $fillable = [
    	'exchange_points', 'user_id', 'status'
    ];

    public function user()
    {
    	return $this->belongsTo('App\User');
    }

    public function user_detail()
    {
    	return $this->belongsTo('App\Models\UserDetail', 'user_id', 'user_id');
    }
}
