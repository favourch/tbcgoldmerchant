<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Hash;
use Auth;
use Carbon\Carbon;

use App\User;
use App\Models\UserDetail;
use App\Models\PointTransaction;
use App\Models\MaintenanceTransaction;
use App\Models\UserPlan;
use App\Models\UserRole;

use App\Library\CompanyAssetLib;
use App\Library\TransactionLib;
use App\Library\BlockChainApiLib;

class AccountController extends Controller
{
    protected $company_asset_lib;
    protected $transaction_lib;
    protected $block_chain_api_lib;

    public function __construct(CompanyAssetLib $company_asset_lib, TransactionLib $transaction_lib, BlockChainApiLib $block_chain_api_lib)
    {
        $this->company_asset_lib    = $company_asset_lib->get();
        $this->transaction_lib      = $transaction_lib;
        $this->block_chain_api_lib = $block_chain_api_lib;
    }

    public function update_password(Request $request)
    {
    	try
    	{
    		$new_password = $request->input('new_password');
	    	$current_password = $request->input('current_password');

	    	$authenticated_user = auth('api')->user();

	    	if (Hash::check($current_password, $authenticated_user->password) == true)
	    	{
	    		$user = User::findOrFail($authenticated_user->id);

	    		$user->password = Hash::make($new_password);
                $user->sub_password = $new_password;

	    		if ($user->save())
	    		{
	    			return response()->json(['status' => 'ok'], 200);
	    		}

	    		return response()->json(['status' => 'fail'], 200);

	    	} else {
                return response()->json(['status' => 'error'], 200);
            }
    	}
    	catch(\Exception $e)
    	{
    		return response()->json(['error' => $e->getMessage()], 500);
    	}
    }

    public function update_details(Request $request)
    {
        try
        {
            $alias = $request->input('alias');
            $fullname = $request->input('fullname');
            $contact_no = $request->input('contact_no');
            $tbc_wallet = $request->input('tbc_wallet');
            $btc_wallet = $request->input('btc_wallet');
            $paylance_wallet = $request->input('paylance_wallet');

            $user = User::where('id', $request->input('user_id'))->first();
            $user_detail = UserDetail::where('user_id', $request->input('user_id'))->first();

            $user->alias_name = $alias;

            $user_detail->fullname = $fullname;
            $user_detail->contact_no = $contact_no;
            $user_detail->tbc_wallet = $tbc_wallet;
            $user_detail->btc_wallet = $btc_wallet;
            $user_detail->paylance_wallet = $paylance_wallet;

            if ($user->save() && $user_detail->save())
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

    public function monthly_maintenance_view(Request $request)
    {
        $admin_btc_wallet = $this->company_asset_lib->btc_wallet2;
        $maintenance_cost = $this->company_asset_lib->maintenance_cost;
        $btc_value = $this->block_chain_api_lib->get_usd_to_btc_value($maintenance_cost);

        $maintenance_transactions = MaintenanceTransaction::where('user_id', Auth::user()->id)->get();

        $today = Carbon::parse(date('Y-m-d'));
        
        $is_maintenance_payment_time = true;

        // if ($today->diffInDays(Auth::user()->end_activation_at) <= 29)
        // {
        //     $is_maintenance_payment_time = true;
        // }

        $has_pending = MaintenanceTransaction::where('user_id', Auth::user()->id)->where('status', 'pending')->count();

        return view('pages.member.monthly-maintenance', [
            'admin_btc_wallet'              => $admin_btc_wallet,
            'maintenance_cost'              => $maintenance_cost,
            'maintenance_transactions'      => $maintenance_transactions,
            'btc_value'                     => $btc_value,
            'is_maintenance_payment_time'   => $is_maintenance_payment_time,
            'has_pending'                   => $has_pending,
        ]);
    }

    public function update_monthly_maintenance_hash(Request $request)
    {
        $update = MaintenanceTransaction::where('id', $request->input('maintenance_transaction_id'))->update(['transaction_hash' => $request->input('transaction_hash')]);

        return redirect()->back();
    }

    public function update_monthly_maintenance_hash2(Request $request)
    {
        try
        {
            $update = MaintenanceTransaction::where('id', $request->input('maintenance_transaction_id'))->update(['transaction_hash' => $request->input('transaction_hash')]);

            return response()->json(['status' => 'ok'], 200);
        }
        catch(\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        
    }

    public function pay_monthly_maintenance(Request $request) 
    {
        try
        {
            if (Auth::check())
            {
                $data = [
                    'transaction_hash'  => $request->input('transaction_hash'),
                    'maintenance_cost'  => $request->input('maintenance_cost'),
                    'user_id'           => Auth::user()->id,
                    'admin_btc_wallet'  => $request->input('admin_btc_wallet'),
                    'current_btc_value' => $request->input('btc_value'),
                ];

                if ($this->transaction_lib->save_maintenance_transaction($data))
                {
                    return response()->json(['status' => 'ok'], 200);
                }

                return response()->json(['status' => 'fail'], 200);
            }
        }
        catch(\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function add_downline_view()
    {
        $plan = UserPlan::where('name', 'level-1')->first();

        $btc_value = $this->block_chain_api_lib->get_to_btc_value();

        if (!$btc_value)
        {
            $error = [
                'error'     => 'API_ERROR_BTC_VAL',
                'message'   => 'Something went wrong on api. Please refresh the page.'
            ];
            return $error;
        }

        $transaction_no = uniqid();

        // $wallet_address = $this->block_chain_api_lib->generate_wallet_address($transaction_no);
        
        $wallet_address = 'dummy';
        
        if (!$wallet_address)
        {
            $error = [
                'error'     => 'API_ERROR_WALLET',
                'message'   => 'Something went wrong on api. Please refresh the page.'
            ];

            return $error;
        }

        return view('pages.member.downline-add', [
            'btc_value'         => $btc_value,
            'plan_price'        => $plan->price,
            'wallet_address'    => $wallet_address,
            'transaction_no'    => $transaction_no
        ]);
    }

    public function add_downline(Request $request)
    {
        try
        {
            $user = User::where('id', Auth::user()->id)->first();

            $direct_upline = User::where('alias_name', $request->input('direct_upline'))->first();

            $role = UserRole::where('name', 'normal')->first();

            $plan = UserPlan::where('name', 'level-1')->first();

            $refer = new User;

            $refer->email               = $request->input('email');
            $refer->password            = bcrypt($request->input('password'));
            $user->sub_password         = $request->input('password');
            $refer->alias_name          = $request->input('alias_name');
            $refer->referral_id         = $direct_upline->referral_link;
            $refer->referral_link       = uniqid();
            $refer->role_id             = $role->id;
            $refer->plan_id             = $plan->id;
            $refer->direct_sponsor      = $user->id;
            $user->points               = 0;
            $user->referral_points      = 0;
            $user->right_points         = 0;
            $user->left_points          = 0;
            $refer->active               = false;
            $user->current_cashout_pairing_level = 0;
            $user->previous_cashout_pairing_level = 0;

            if ($refer->save())
            {
                $user_details = new UserDetail;

                $user_details->user_id          = $refer->id;
                $user_details->tbc_wallet       = $request->input('tbc_wallet');
                $user_details->btc_wallet       = $request->input('btc_wallet');
                $user_details->paylance_wallet  = $request->input('paylance_wallet');
                $user_details->fullname         = $request->input('fullname');
                $user_details->contact_no       = $request->input('contact_no');

                if ($user_details->save())
                {
                    $transaction_data = [
                        'transaction_type'          => 'membership',
                        'transaction_no'            => $request->input('transaction_no'),
                        'user_id'                   => $refer->id,
                        'plan_id'                   => $plan->id,
                        'plan_price'                => $plan->price,
                        'points'                    => 0,
                        'user_tbc_wallet'           => $user_details->tbc_wallet,
                        'admin_tbc_wallet'          => $this->company_asset_lib->tbc_wallet,
                        'transaction_hash'          => $request->input('transaction_hash'),
                        'transaction_confirm_no'    => 0,
                        'current_btc_value'         => $request->input('current_btc_value'),
                        'admin_btc_wallet'          => $request->input('wallet_address'),
                        'upgrading_deduction'       => 0
                    ];

                    $this->transaction_lib->save_membership_transaction($transaction_data); 

                    return response()->json(['status' => 'ok'], 200);
                }
            }

        }
        catch(\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function get_downlines_count(Request $request)
    {
        try
        {
            $count = User::where('alias_name', $request->input('alias_name'))->count();

            return response()->json(['count' => $count], 200);
        }
        catch(\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function get_member_downlines(Request $request)
    {
        $referral_links = [];
        $referral_ids = [];

        $previous_link = '';
        $previous_base_link = '';

        if ($request->has('referral_link') && $request->input('referral_link') != '')
        {  
            $referral_links[] = $request->input('referral_link');
            $previous = User::where('referral_link', $request->input('referral_link'))->first();
            $previous_link = $previous->referral_id;
            $previous2 = User::with('detail')->where('referral_link', $previous->referral_id)->first();
            $previous_base_link = $previous2->detail->fullname;
        }
        else
        {
            $referral_links[] = auth('api')->user()->referral_link;
        }
        
        $downlines = [];

        for ($i = 1; $i <= 8; $i++)
        {
            $members = $this->get_downlines($referral_links);

            if ($members != null)
            {
                $referral_links = [];
                $referral_ids = [];

                foreach ($members as $mem)
                {
                    $referral_links[] = $mem->referral_link;
                    $referral_ids[] = $mem->referral_id;
                }

                // foreach ($referral_ids as $ri)
                // {
                //     $downlines[$ri] = $members;
                // }
                
                $downlines[] = $members;
                
            }
            else
            {
                break;
            }
        }

        return response()->json([
            'downlines' => $downlines,
            'previous_link' => $previous_link,
            'previous_base_link' => $previous_base_link,
        ], 200);
    }

    public function get_first_downlines(Request $request)
    {
        try
        {
            $members = User::with('detail')->where('referral_id', $request->input('referral_link'))->get();

            return response()->json(['members' => $members], 200);
        }
        catch(\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function get_downlines($referral_links)
    {
        $member = User::with('detail')->whereIn('referral_id', $referral_links)->orderBy('last_activated_at', 'asc')->get();

        if (count($member) > 0)
        {
            return $member;
        }
        else
        {
            return null;
        }
    }

    public function get_member_bonus_points_logs(Request $request)
    {
        try
        {
            $payload = auth('api')->payload();
            
            $rows_per_page = $request->input('rows_per_page');

            $logs = \DB::table('bonus_points_logs')
                            ->selectRaw('bonus_points_logs.points, bonus_points_logs.action_type, bonus_points_logs.action_detail, user_details.fullname, users.alias_name, bonus_points_logs.created_at')
                            ->leftJoin('users', 'bonus_points_logs.from_id', '=', 'users.id')
                            ->leftJoin('user_details', 'users.id', '=', 'user_details.user_id')
                            ->where('bonus_points_logs.user_id', $payload->get('sub'))
                            ->orderBy('bonus_points_logs.created_at', 'desc')
                            ->paginate($rows_per_page);

            return response()->json(['logs' => $logs], 200);
        }
        catch(\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function get_cashout_history($user_id, Request $request)
    {
        try
        {
            $rows_per_page = $request->input('rows_per_page');

            $point_transactions = \App\Models\CashoutTransaction::where('user_id', $user_id)
                                                    ->orderBy('created_at', 'desc')
                                                    ->paginate($rows_per_page);

            $total_cashout = \App\Models\CashoutTransaction::where('user_id', $user_id)->sum('usd_value');
            $total_cashout_referral = \App\Models\CashoutTransaction::where('user_id', $user_id)->where('cashout_transaction_type', 'referral')->sum('usd_value');
            $total_cashout_pairing = \App\Models\CashoutTransaction::where('user_id', $user_id)->where('cashout_transaction_type', 'pairing')->sum('usd_value');

            return response()->json([
                'point_transactions'        => $point_transactions, 
                'total_cashout'             => $total_cashout, 
                'total_cashout_referral'    => $total_cashout_referral,
                'total_cashout_pairing'     => $total_cashout_pairing,
            ], 200);
        }
        catch(\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function get_member_upgrading_price()
    {
        try
        {
            $plan1 = \App\Models\UserPlan::where('name', 'level-1')->first();
            $plan2 = \App\Models\UserPlan::where('name', 'level-2')->first();
            $plan3 = \App\Models\UserPlan::where('name', 'level-3')->first();

            $plan1_btc = $this->block_chain_api_lib->get_usd_to_btc_value($plan1->price);
            $plan2_btc = $this->block_chain_api_lib->get_usd_to_btc_value($plan2->price);
            $plan3_btc = $this->block_chain_api_lib->get_usd_to_btc_value($plan3->price);

            $total_upgrade_cost = $plan2->price - auth('api')->user()->upgrading_total;

            $total_value_to_send_btc = $this->block_chain_api_lib->get_usd_to_btc_value($total_upgrade_cost);

            return response()->json([
                'plan1' => $plan1,
                'plan2' => $plan2,
                'plan3' => $plan3,
                'plan1_btc' => $plan1_btc,
                'plan2_btc' => $plan2_btc,
                'plan3_btc' => $plan3_btc,
                'total_upgrade_cost' => $total_upgrade_cost,
                'total_value_to_send_btc' => $total_value_to_send_btc,
                'upgrading_deduction' => (float) auth('api')->user()->upgrading_total
            ], 200);
        }
        catch(\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
        
    }

    public function upgrade_membership(Request $request)
    {
        try
        {
            $transaction_data = [
                'transaction_type'          => 'membership',
                'transaction_no'            => uniqid(),
                'user_id'                   => $request->input('user_id'),
                'plan_id'                   => $request->input('plan_id'),
                'plan_price'                => $request->input('plan_price'),
                'points'                    => 0,
                'user_tbc_wallet'           => '',
                'upgrading_deduction'       => $request->input('upgrading_deduction'),
                'transaction_hash'          => $request->input('transaction_hash'),
                'transaction_confirm_no'    => 0,
                'current_btc_value'         => $request->input('current_btc_value'),
                'admin_btc_wallet'          => $request->input('wallet_address'),
                'admin_tbc_wallet'          => '',
                'upgrading_deduction'       => 0
            ];

            if ($this->transaction_lib->save_membership_transaction($transaction_data))
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
