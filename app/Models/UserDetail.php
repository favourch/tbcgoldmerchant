<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    protected $table = 'user_details';

    protected $fillable = [
    	'user_id', 'tbc_wallet', 'btc_wallet', 'paylance_wallet', 'fullname', 'contact_no'
    ];

}
