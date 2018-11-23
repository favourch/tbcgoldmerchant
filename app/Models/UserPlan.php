<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPlan extends Model
{
    protected $table = 'user_plans';

    protected $fillable = [
    	'name', 'description', 'price'
    ];
}
