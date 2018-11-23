<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BonusPointsLog extends Model
{
    protected $table = 'bonus_points_logs';

    protected $fillable = [
    	'points', 'action_type', 'action_detail', 'user_id'
    ];
}
