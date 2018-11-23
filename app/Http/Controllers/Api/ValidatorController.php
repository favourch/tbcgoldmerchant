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

class ValidatorController extends Controller
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
        $user_id = 0;
        if ($request->has('user_id'))
        {
            $user_id = $request->input('user_id');
        }

        $alias_exists = User::where('alias_name', $request->input('alias_name'))
                            ->when($user_id > 0, function($query) use($user_id) {
                                return $query->where('id', '!=', $user_id);
                            })
                            ->count();
        
        if ($alias_exists > 0)
        {
            return response()->json(['valid' => false, 'message' => 'Alias already taken.'], 200);
        }

        return response()->json(['valid' => true, 'message' => ''], 200);
    }

    public function validate_alias_exists(Request $request)
    {
        if ($request->input('alias_name') == 'leader')
        {
            return response()->json(['valid' => true, 'message' => ''], 200);
        }

        $alias_exists = User::where('alias_name', $request->input('alias_name'))->count();
        
        if ($alias_exists)
        {
            return response()->json(['valid' => true, 'message' => ''], 200);
        }
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
