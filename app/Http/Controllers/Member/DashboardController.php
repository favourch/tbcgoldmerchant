<?php

namespace App\Http\Controllers\Member;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Carbon\Carbon;
use Auth;

use App\User;
use App\Models\UserPlan;
use App\Library\BlockChainApiLib;

use App\Models\MembershipTransaction;
use App\Models\MaintenanceTransaction;

class DashboardController extends Controller
{
    protected $block_chain_api_lib;

    public function __construct(BlockChainApiLib $block_chain_api_lib)
    {
        $this->block_chain_api_lib  = $block_chain_api_lib;
    }

    public function dashboard_view()
    {
    	$last_activated_at = Auth::user()->last_activated_at;

    	$parsed_last_activated_at = Carbon::parse($last_activated_at);
    	$parsed_start_at = Carbon::parse($last_activated_at);
    	
    	$activated_start_time = $parsed_start_at;
    	$activated_end_time = Carbon::parse(Auth::user()->end_activation_at);        

        $transaction = MembershipTransaction::where('user_id', Auth::user()->id)->first();

        $today = Carbon::parse(date('Y-m-d'));
        
        $is_maintenance_payment_time = false;

        if ($today->diffInDays(Auth::user()->end_activation_at) <= 29)
        {
            $is_maintenance_payment_time = true;
        }

        $has_pending = MaintenanceTransaction::where('user_id', Auth::user()->id)->where('status', 'pending')->count();

        $plan1 = UserPlan::where('name', 'level-1')->first();
        $plan2 = UserPlan::where('name', 'level-2')->first();
        $plan3 = UserPlan::where('name', 'level-3')->first();

        $plan1_btc = $this->block_chain_api_lib->get_usd_to_btc_value($plan1->price);
        $plan2_btc = $this->block_chain_api_lib->get_usd_to_btc_value($plan2->price);
        $plan3_btc = $this->block_chain_api_lib->get_usd_to_btc_value($plan3->price);

        $total_upgrade_cost = $plan2->price - Auth::user()->upgrading_total;

        $total_value_to_send_btc = $this->block_chain_api_lib->get_usd_to_btc_value($total_upgrade_cost);

        $has_level_two_pending = MembershipTransaction::where('user_id', Auth::user()->id)->where('status', 'pending')->count();

    	return view('pages.member.dashboard', [
    		'activated_start_time'          => $activated_start_time->format('M d, Y H:i:s'),
    		'activated_end_time' 	        => $activated_end_time->format('M d, Y H:i:s'),
            'transaction'                   => $transaction,
            'is_maintenance_payment_time'   => $is_maintenance_payment_time,
            'has_pending'                   => $has_pending,
            'plan1'                         => $plan1->price,
            'plan2'                         => $plan2->price,
            'plan3'                         => $plan3->price,
            'plan1_btc'                     => $plan1_btc,
            'plan2_btc'                     => $plan2_btc,
            'plan3_btc'                     => $plan3_btc,
            'total_upgrade_cost'            => $total_upgrade_cost,
            'total_value_to_send_btc'       => $total_value_to_send_btc,
            'has_level_two_pending'       => $has_level_two_pending,
    	]);
    }
}
