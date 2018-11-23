<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceTransaction extends Model
{
    protected $table = 'maintenance_transactions';

    protected $fillable = [
    	'user_id', 'transaction_hash', 'maintenance_cost', 'status', 'confirmed_at', 'denied_at'
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
