<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnilevelTransaction extends Model
{
    protected $table = 'unilevel_transactions';

    protected $fillable = [
    	'user_id', 'transaction_hash', 'status', 'unilevel_bonus', 'deduction_admin_fee', 'deduction_commission', 'confirmed_unilevel_bonus', 'confirmed_at', 'denied_at'
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
