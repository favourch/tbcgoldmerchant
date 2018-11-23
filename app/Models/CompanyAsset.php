<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyAsset extends Model
{
    protected $table = 'company_assets';

    protected $fillable = [
    	'tbc_wallet', 'btc_wallet', 'btc_wallet2', 'btc_wallet3', 'paylance_wallet', 'max_cashout_points', 'min_cashout_points', 'min_cashout_referral_points', 'min_cashout_unilevel', 'tbc_usd_value', 'tbc_btc_value', 'tbc_php_value', 'maintenance_cost'
    ];
}
