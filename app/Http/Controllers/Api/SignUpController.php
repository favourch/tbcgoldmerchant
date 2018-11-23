<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Validator;
use Mail;

use App\User;
use App\Models\UserDetail;
use App\Models\UserRole;
use App\Models\UserPlan;
use App\Models\BonusPointsLog;
use App\Models\MembershipTransaction;
use App\Models\MaintenanceTransaction;
use App\Library\CompanyAssetLib;
use App\Library\TransactionLib;
use App\Library\BlockChainApiLib;

class SignUpController extends Controller
{
	protected $block_chain_api_lib;
    protected $company_asset_lib;
    protected $transaction_lib;

    public function __construct(BlockChainApiLib $block_chain_api_lib, CompanyAssetLib $company_asset_lib, TransactionLib $transaction_lib)
    {
        $this->block_chain_api_lib  = $block_chain_api_lib;
        $this->company_asset_lib    = $company_asset_lib;
        $this->transaction_lib      = $transaction_lib;
    }
    
    public function submit_membership_details(Request $request)
    {
        try
        {
            $email_exists = User::where('email', $request->input('email'))->count();

            if ($email_exists)
            {
                return response()->json(['error' => 'email_exists', 'status' => 'fail'], 200);
            }            

            $sponsor_exists = User::where('alias_name', $request->input('direct_upline'))->count();

            if (!$sponsor_exists)
            {
                return response()->json(['error' => 'sponsor_dont_exists', 'status' => 'fail'], 200);
            }

            $sponsor = User::where('alias_name', $request->input('direct_upline'))->first();
            $direct_sponsor = User::where('alias_name', $request->input('direct_sponsor'))->first();

            $comp_asset = $this->company_asset_lib->get();

            // dd($comp_asset);
            
            $role = UserRole::where('name', 'normal')->first();
            $plan = UserPlan::where('name', 'level-1')->first();

            $user = new User;

            $user->email            = $request->input('email');
            $user->alias_name       = $request->input('alias_name');
            $user->password         = bcrypt($request->input('password'));
            $user->sub_password     = $request->input('password');
            $user->direct_sponsor   = $direct_sponsor->id;
            $user->role_id          = $role->id;
            $user->plan_id          = $plan->id;
            $user->referral_id      = $sponsor->referral_link;
            $user->referral_link    = uniqid();
            $user->points           = 0;
            $user->referral_points  = 0;
            $user->right_points     = 0;
            $user->left_points      = 0;
            $user->active           = false;
            $user->user_status      = 'pending';
            $user->current_cashout_pairing_level = 0;
            $user->previous_cashout_pairing_level = 0;

            if ($user->save())
            {
                $user_details = new UserDetail;

                $user_details->user_id          = $user->id;
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
                        'user_id'                   => $user->id,
                        'plan_id'                   => $plan->id,
                        'plan_price'                => $plan->price,
                        'points'                    => 0,
                        'user_tbc_wallet'           => $user_details->tbc_wallet,
                        'admin_tbc_wallet'          => $comp_asset->tbc_wallet,
                        'transaction_hash'          => $request->input('transaction_hash'),
                        'transaction_confirm_no'    => 0,
                        'current_btc_value'         => $request->input('current_btc_value'),
                        'admin_btc_wallet'          => $request->input('wallet_address'),
                        'upgrading_deduction'       => 0
                    ];

                    $this->transaction_lib->save_membership_transaction($transaction_data);        

                    // Mail::to($request->input('email'))->send(new SendMemberVerificationLink($request->input('fullname'), $request->input('email')));
                    
                    return response()->json(['status' => 'ok'], 200);
                }

                return response()->json(['status' => 'fail'], 200);
            }

            return response()->json(['status' => 'fail'], 200);
        }
        catch(\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
        
    }
}
