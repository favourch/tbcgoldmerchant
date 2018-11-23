<?php

namespace App\Http\Controllers\Member;

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

use App\Mail\SendMemberVerificationLink;

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

    public function sign_up_view($referral_link)
    {
        $referral_count = User::where('referral_link', $referral_link)->count();

        $direct_recruit_count = User::where('referral_id', $referral_link)->count();

        if ($direct_recruit_count >= 2)
        {
            return view('pages.member.auth.sign-up', [
                'has_two_recruit' => true
            ]);
        }

        if ($referral_count > 0)
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

            return view('pages.member.auth.sign-up', [
                'referral_link'     => $referral_link,
                'btc_value'         => $btc_value,
                'plan_price'        => $plan->price,
                'wallet_address'    => $wallet_address,
                'transaction_no'    => $transaction_no
            ]);
        }

        abort(404);
    }

    public function sign_up_with_sponsor_view()
    { 
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

        $plan = UserPlan::where('name', 'level-1')->first();
        
        return view('pages.member.auth.sign-up-with-sponsor', [
            'wallet_address'    => $wallet_address,
            'btc_value'         => $btc_value,
            'plan_price'        => $plan->price,
            'transaction_no'    => $transaction_no,
        ]);
    }

    public function sign_up(Request $request)
    {
        try
        {
            $email_exists = User::where('email', $request->input('email'))->count();

            if ($email_exists)
            {
                return response()->json(['email_exists' => 'The email address is already taken.', 'status' => 'fail'], 200);
            }

            $comp_asset = $this->company_asset_lib->get();

            $sponsor = User::where('referral_link', $request->input('referral_id'))->first();

            $role = UserRole::where('name', 'normal')->first();
            $plan = UserPlan::where('name', 'level-1')->first();

            $user = new User;

            $user->email            = $request->input('email');
            $user->password         = bcrypt($request->input('password'));
            $user->sub_password     = $request->input('password');
            $user->role_id          = $role->id;
            $user->plan_id          = $plan->id;
            $user->referral_id      = $request->input('referral_id');
            $user->alias_name       = $request->input('alias_name');
            $user->referral_link    = uniqid();
            $user->direct_sponsor   = $sponsor->id;
            $user->points           = 0;
            $user->referral_points  = 0;
            $user->right_points     = 0;
            $user->left_points      = 0;
            $user->active           = false;
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

    public function sign_up_with_sponsor(Request $request)
    {
        try
        {
            $email_exists = User::where('email', $request->input('email'))->count();

            if ($email_exists)
            {
                return response()->json(['error' => 'email_exists', 'status' => 'fail'], 200);
            }            

            $sponsor_exists = User::where('alias_name', $request->input('sponsor_alias'))->count();

            if (!$sponsor_exists)
            {
                return response()->json(['error' => 'sponsor_dont_exists', 'status' => 'fail'], 200);
            }

            $sponsor = User::where('alias_name', $request->input('sponsor_alias'))->first();
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

    public function validate_email(Request $request)
    {
        $email_exists = true;
        if ($request->has('user_id'))
        {
            $email_exists = User::where('email', $request->input('email'))->where('id', '!=', $request->input('user_id'))->count();
        }
        else
        {
            $email_exists = User::where('email', $request->input('email'))->count();
        }
        
        if ($email_exists)
        {
            return response()->json(['valid' => false, 'message' => 'Email already taken.'], 200);
        }

        return response()->json(['valid' => true, 'message' => ''], 200);
    }

    public function validate_sponsor_alias(Request $request)
    {
        $sponsor_exists = User::where('alias_name', $request->input('sponsor_alias'))->count();

        if ($sponsor_exists)
        {
            if ($this->is_sponsor_active($request->input('sponsor_alias')) == false)
            {
                return response()->json(['valid' => false, 'message' => 'Member is not yet confirmed.'], 200); 
            }

            return response()->json(['valid' => true, 'message' => ''], 200);
        }

        

        return response()->json(['valid' => false, 'message' => 'Sponsor do not exists.'], 200);
    }

    public function validate_sponsor_alias2(Request $request)
    {
        $sponsor_exists = User::where('alias_name', $request->input('sponsor_alias'))->count();

        if ($sponsor_exists)
        {
            $sponsor = User::where('alias_name', $request->input('sponsor_alias'))->first();

            $count_direct_recruits = User::where('referral_id', $sponsor->referral_link)->count();

            if ($count_direct_recruits >= 2)
            {
                return response()->json(['valid' => false, 'message' => 'Sponsor already have 2 direct recruits.'], 200); 
            }        

            if ($this->is_upline_under_sponsor($request->input('direct_sponsor'), $request->input('sponsor_alias')) == false)
            {
                return response()->json(['valid' => false, 'message' => 'Direct Upline is not under on the Direct Sponsor you specified.'], 200); 
            }

            if ($this->is_sponsor_active($request->input('sponsor_alias')) == false)
            {
                return response()->json(['valid' => false, 'message' => 'Member is not yet confirmed.'], 200); 
            }

            return response()->json(['valid' => true, 'message' => ''], 200);
        }

        return response()->json(['valid' => false, 'message' => 'Sponsor do not exists.'], 200);
    }

    private function is_sponsor_active($sponsor_alias)
    {
        $member = User::where('alias_name', $sponsor_alias)->first();

        return $member->active;
    }

    private function is_upline_under_sponsor($sponsor_alias, $upline_alias)
    {
        $loop = 5000;

        $member = User::where('alias_name', $upline_alias)->first();

        $dynamic_referral_link = $member->referral_link;

        $aliases = [];

        $is_under = false;

        for ($i = 1; $i <= $loop; $i++)
        {
            $upline = User::where('referral_link', $dynamic_referral_link)->first();

            if ($upline != null)
            {
                if ($upline->referral_id != 'leader')
                {
                    if ($upline->alias_name == $sponsor_alias)
                    {
                        $is_under = true;
                        break;
                        
                    }
                    else
                    {
                        $dynamic_referral_link = $upline->referral_id;
                    }
                }
                else
                {
                    $is_under = true;
                    break;
                    
                }
            }
            else
            {
                $is_under = false;
                break;
               
            }
            
        }

        return $is_under;

    }

    public function validate_sponsor_alias3(Request $request)
    {
        if ($request->input('sponsor_alias') == 'leader')
        {
            return response()->json(['valid' => true, 'message' => ''], 200);
        }
        
        $sponsor_exists = User::where('alias_name', $request->input('sponsor_alias'))->count();

        if ($sponsor_exists)
        {
            $sponsor = User::where('alias_name', $request->input('sponsor_alias'))->first();

            $count_direct_recruits = User::where('referral_id', $sponsor->referral_link)->count();

            if ($count_direct_recruits >= 2)
            {
                return response()->json(['valid' => false, 'message' => 'Sponsor already have 2 direct recruits.'], 200); 
            }        

            if ($this->is_upline_under_sponsor($request->input('direct_sponsor'), $request->input('sponsor_alias')) == false)
            {
                return response()->json(['valid' => false, 'message' => 'Direct Upline is not under on the Direct Sponsor you specified.'], 200); 
            }

            if ($this->is_sponsor_active($request->input('sponsor_alias')) == false)
            {
                return response()->json(['valid' => false, 'message' => 'Member is not yet confirmed.'], 200); 
            }

            return response()->json(['valid' => true, 'message' => ''], 200);
        }

        return response()->json(['valid' => false, 'message' => 'Sponsor do not exists.'], 200);
    }

    public function validate_alias_unique(Request $request)
    {
        $has_user_id = false;
        $user_id = 0;
        if ($request->has('user_id'))
        {
            $has_user_id = true;
            $user_id = $request->input('user_id');
        }

        $alias_exists = User::where('alias_name', $request->input('alias'))
                            ->when($has_user_id == true, function($query) use($user_id) {
                                return $query->where('id', '!=', $user_id);
                            })
                            ->count();
        
        if ($alias_exists)
        {
            return response()->json(['valid' => false, 'message' => 'Alias already taken.'], 200);
        }

        return response()->json(['valid' => true, 'message' => ''], 200);
    }

    public function validate_alias_exists(Request $request)
    {
        $alias_exists = User::where('alias_name', $request->input('alias'))->count();
        
        if ($alias_exists)
        {
            return response()->json(['valid' => true, 'message' => ''], 200);
        }

        return response()->json(['valid' => false, 'message' => 'Alias dont exists.'], 200);
    }

    public function validate_alias_exists2(Request $request)
    {
        if ($request->input('alias') == 'leader')
        {
            return response()->json(['valid' => true, 'message' => ''], 200);
        }

        $alias_exists = User::where('alias_name', $request->input('alias'))->count();
        
        if ($alias_exists)
        {
            return response()->json(['valid' => true, 'message' => ''], 200);
        }

        return response()->json(['valid' => false, 'message' => 'Alias dont exists.'], 200);
    }

    public function validate_transaction_hash(Request $request)
    {
        try
        {
            if ($request->input('transaction_hash') == 'cash' || $request->input('transaction_hash') == 'cd')
            {
                return response()->json(['valid' => true, 'message' => ''], 200);
            }
            else
            {
                $exists = MembershipTransaction::where('transaction_hash', $request->input('transaction_hash'))->count();

                    $exists2 = MaintenanceTransaction::where('transaction_hash', $request->input('transaction_hash'))->count();
                
                    if ($exists == 0 && $exists2 == 0)
                    {
                        return response()->json(['valid' => true, 'message' => ''], 200);
                    }

                    return response()->json(['valid' => false, 'message' => 'Hash is already used.'], 200);
            }

        }
        catch(\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }   
    }

}
