<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Hash;
use Auth;
use Carbon\Carbon;

use App\User;
use App\Models\UserDetail;
use App\Models\CashoutTransaction;
use App\Models\CashoutTransaction2;
use App\Models\UnilevelTransaction;
use App\Models\UserPlan;
use App\Models\UserRole;

use App\Library\CompanyAssetLib;
use App\Library\TransactionLib;
use App\Library\BlockChainApiLib;

class CashoutController extends Controller
{
	protected $company_asset_lib;
    protected $transaction_lib;
    protected $block_chain_api_lib;

    public function __construct(CompanyAssetLib $company_asset_lib, TransactionLib $transaction_lib, BlockChainApiLib $block_chain_api_lib)
    {
        $this->company_asset_lib    = $company_asset_lib;
        $this->transaction_lib      = $transaction_lib;
        $this->block_chain_api_lib = $block_chain_api_lib;
    }

    public function cashout(Request $request)
    {
        try
        {
 
                $company_asset = $this->company_asset_lib->get();

                $last_activated_at = $company_asset->last_activated_at;
                $last_cashout_at = $company_asset->last_cashout_at;

                $parsed_last_activated_at = Carbon::parse($last_activated_at);
                $parsed_last_cashout_at = Carbon::parse($last_cashout_at);

                $now = Carbon::now();

                $diff_last_activated_at = $parsed_last_activated_at->diffInDays($now);
                $diff_last_cashout_at = $parsed_last_cashout_at->diffInDays($now);

                if ($diff_last_activated_at > 30)
                {
                    $error = [
                        'error'     => 'ERROR_ACCOUNT_NOT_ACTIVATED',
                        'message'   => 'Your account is deactivated. You should not encounter this error.'
                    ];

                    return $error;

                    if ($diff_last_cashout_at < 7)
                    {
                        $error = [
                            'error'     => 'ERROR_ACCOUNT_CANNOT_EXCHANGE',
                            'message'   => 'Your account cannot exchange points yet. You should not encounter this error.'
                        ];

                        return $error;
                    }
                }

                $max_cashout_points = $company_asset->max_cashout_points;

                $min_cashout_points = $company_asset->min_cashout_points;

                $referral_points = $request->input('referral_points');
                $left_points = $request->input('left_points');
                $right_points = $request->input('right_points');

                $data = [
                    'user_id'                   => $request->input('user_id'),
                    'cashout_transaction_type'  => $request->input('cashout_transaction_type'),
                    'left_points'            	=> $left_points,
                    'right_points'            	=> $right_points,
                    'referral_points'           => $referral_points,
                ];

                if ($this->transaction_lib->save_cashout_transaction($data))
                {
                    return response()->json(['status' => 'ok'], 200);
                }

                return response()->json(['status' => 'fail'], 200);

        }
        catch(\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function cashout_unilevel(Request $request)
    {
        try
        {
            
                $company_asset = $this->company_asset_lib->get();

                
                $min_cashout_points = $company_asset->min_cashout_unilevel;

                $current_btc_value = $this->block_chain_api_lib->get_usd_to_btc_value($request->input('unilevel_bonus'));

                $data = [
                    'user_id'               => $request->input('user_id'),
                    'unilevel_bonus'        => $request->input('unilevel_bonus'),
                    'current_btc_value'     => $current_btc_value,
                ];

                if ($this->transaction_lib->save_unilevel_transaction($data))
                {
                    return response()->json(['status' => 'ok'], 200);
                }

                return response()->json(['status' => 'fail'], 200);
        }
        catch(\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
