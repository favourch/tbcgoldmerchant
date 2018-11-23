<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MembershipTransaction extends Model
{
    protected $table = 'membership_transactions';

    protected $fillable = [
    	'transaction_type', 'plan_id', 'plan_cost', 'user_id', 'user_tbc_wallet', 'admin_tbc_wallet'
    ];

    public function user()
    {
    	return $this->belongsTo('App\User');
    }

    public function plan()
    {
    	return $this->belongsTo('App\Models\UserPlan');
    }

    public function user_detail()
    {
        return $this->belongsTo('App\Models\UserDetail', 'user_id', 'user_id');
    }
}
