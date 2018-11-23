<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\User;
use App\Models\UserDetail;
use App\Models\UserPlan;
use App\Models\MembershipTransaction;

use App\Library\CompanyAssetLib;
use App\Library\TransactionLib;
use App\Library\BlockChainApiLib;
use App\Library\PointSystemLib;

class MemberController extends Controller
{
    protected $block_chain_api_lib;
    protected $company_asset_lib;
    protected $transaction_lib;
    protected $point_system_lib;

    public function __construct(BlockChainApiLib $block_chain_api_lib, CompanyAssetLib $company_asset_lib, TransactionLib $transaction_lib, PointSystemLib $point_system_lib)
    {
        $this->block_chain_api_lib  = $block_chain_api_lib;
        $this->company_asset_lib    = $company_asset_lib;
        $this->transaction_lib      = $transaction_lib;
        $this->point_system_lib      = $point_system_lib;
    }

    public function members_view()
    {
    	return view('pages.admin.members');
    }

    public function get_members(Request $request)
    {
        try
        {
            $status = $request->input('filter_by_status');
            $search_input = $request->input('search_input');
            $search_by = $request->input('search_by');

            $members = \DB::table('users')
                            ->leftJoin('user_details', 'users.id', '=', 'user_details.user_id')
                            ->when($status != '', function($q) use($status) {
                                if ($status == 'active') 
                                {
                                    return $q->where('users.active', 1);
                                }
                                elseif ($status == 'inactive')
                                {
                                    return $q->where('users.active', 0);
                                }
                            })
                            ->when($search_by != '', function($query) use($search_by, $search_input) {
                                    if ($search_by == 'alias_name')
                                    {
                                        return $query->where('users.alias_name', 'like', '%'.$search_input.'%');
                                    }
                                    elseif ($search_by == 'fullname')
                                    {
                                        return $query->where('user_details.fullname', 'like', '%'.$search_input.'%');
                                    }
                                })
                            ->paginate(10);


            return response()->json(['members' => $members], 200);
        }
        catch(\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function get_active_members(Request $request)
    {
    	try
    	{
    		$members = User::with(['detail' => function ($query) {
                $query->orderBy('fullname', 'asc');
            }])->get();

    		return response()->json(['members' => $members], 200);
    	}
    	catch(\Exception $e)
    	{
    		return response()->json(['error' => $e->getMessage()], 500);
    	}
    }

    public function get_member_details($user_id)
    {
        try
        {
            $member = User::with('detail', 'plan')->where('id', $user_id)->first();

            return response()->json([
                'member' => $member,
            ], 200);
        }
        catch(\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function add_member_view()
    {
        return view('pages.admin.member.member-add');
    }

    public function add_member(Request $request)
    {
        try
        {
            $direct_sponsor = $request->input('direct_sponsor');
            $direct_upline = $request->input('direct_upline');    

            $user = new User;

            $user->email = $request->input('email');
            $user->alias_name = $request->input('alias_name');
            $user->password = bcrypt($request->input('password'));
            $user->sub_password = $request->input('password');
            $user->role_id = 1;
            $user->plan_id = 1;

            if ($direct_sponsor != 'leader')
            {
                $d_sponsor = User::where('alias_name', $direct_sponsor)->first();
                $user->direct_sponsor = $d_sponsor->id;
            }
            else
            {
                $user->direct_sponsor = 'leader';
            }
            
            $user->referral_link = uniqid();

            if ($direct_upline != 'leader')
            {
                $d_upline = User::where('alias_name', $direct_upline)->first();
                $user->referral_id = $d_upline->referral_link;
            }
            else
            {
                $user->referral_id = 'leader';
            }

            
            $user->referral_points = 0;
            $user->points = 0;
            $user->right_points = 0;
            $user->left_points = 0;
            $user->active = 1;
            $user->user_status = 'activated';
            $user->last_activated_at = date('Y-m-d H:i:s');
            $user->current_cashout_pairing_level = 0;
            $user->previous_cashout_pairing_level = 0;

            if ($user->save())
            {
                $user_details = new UserDetail;

                $user_details->user_id = $user->id;
                $user_details->fullname = $request->input('fullname');
                $user_details->contact_no = $request->input('contact_no');
                $user_details->btc_wallet = $request->input('btc_wallet');
                $user_details->tbc_wallet = $request->input('tbc_wallet');
                $user_details->paylance_wallet = $request->input('paylance_wallet');

                if ($user_details->save())
                {
                    if ($direct_sponsor != 'leader')
                    {
                        $this->point_system_lib->add_referral_point($d_sponsor->id, $user->id);
                    }

                    if ($direct_upline != 'leader')
                    {
                        $this->get_upline($user->referral_id, $user->id);
                    }

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

    private function get_upline($referral_id, $referred_id, $loop_no = 12)
    {
        $uplines = [];

        $dynamic_referral_id = $referral_id;
        $dynamic_referred_id = $referred_id;

        for ($i = 1; $i <= $loop_no; $i++)
        {
            $user = User::select('id', 'referral_id', 'referral_link')
                            ->where('referral_link', $dynamic_referral_id)
                            ->first();

            if (!is_null($user))
            {
                $xds = User::where('referral_id', $user->referral_link)->get();
                
                $count = 0;
                $position = 'left';
                foreach ($xds as $xd)
                {
                    $count++;
                    if ($xd->id == $dynamic_referred_id)
                    {
                        if ($count > 1)
                        {
                            $position = 'right';
                        }
                        else
                        {
                            $position = 'left';
                        }
                    }
                }
            
                // $uplines[$user->id][] = $position; 
                // $referred_id = $user->id;
                
                $this->point_system_lib->add_bonus_point($user->id, $dynamic_referred_id, $position);

                $dynamic_referral_id = $user->referral_id;
                $dynamic_referred_id = $user->id;
            }
            else
            {
                break;
            }
        }

        // return $uplines;
    }

    private function count_member_downline($referral_link)
    {
        $count = User::where('referral_id', $referral_link)->where('active', 1)->count();

        return $count;
    }

    public function edit_member_view($user_id)
    {
        $member = User::with('detail', 'plan')->where('id', $user_id)->first();

        $plan1 = UserPlan::where('name', 'level-1')->first();
            $plan2 = UserPlan::where('name', 'level-2')->first();
            $plan3 = UserPlan::where('name', 'level-3')->first();

            $plan1_btc = $this->block_chain_api_lib->get_usd_to_btc_value($plan1->price);
            $plan2_btc = $this->block_chain_api_lib->get_usd_to_btc_value($plan2->price);
            $plan3_btc = $this->block_chain_api_lib->get_usd_to_btc_value($plan3->price);

            $total_upgrade_cost = $plan2->price - $member->upgrading_total;

            $total_value_to_send_btc = $this->block_chain_api_lib->get_usd_to_btc_value($total_upgrade_cost);

            $has_level_two_pending = MembershipTransaction::where('user_id', $user_id)->where('plan_id', 2)->where('status', 'pending')->count();

            $has_level_two_confirmed = MembershipTransaction::where('user_id', $user_id)->where('plan_id', 2)->where('status', 'confirmed')->count();

        return view('pages.admin.member.member-edit', [
            'user_id' => $user_id,
            'plan1'                         => $plan1->price,
                'plan2'                         => $plan2->price,
                'plan3'                         => $plan3->price,
                'plan1_btc'                     => $plan1_btc,
                'plan2_btc'                     => $plan2_btc,
                'plan3_btc'                     => $plan3_btc,
                'total_upgrade_cost'            => $total_upgrade_cost,
                'total_value_to_send_btc'       => $total_value_to_send_btc,
                'member'                        => $member,
                'has_level_two_pending'       => $has_level_two_pending,
                'has_level_two_confirmed'       => $has_level_two_confirmed,
        ]);
    }

    public function get_member_sponsors(Request $request)
    {
        try
        {
            $member = User::where('id', $request->input('user_id'))->first();
            $direct_sponsor = '';
            $current_sponsor = '';
            if ($member->direct_sponsor != 'leader')
            {
                $ds = User::where('id', $member->direct_sponsor)->first();
                $cs = User::where('referral_link', $member->referral_id)->first();

                $direct_sponsor = $ds->detail->fullname;
                $current_sponsor = $cs->detail->fullname;
            }
            else
            {
                $current_sponsor = 'leader';
                $direct_sponsor = 'leader';
            }
            

            return response()->json(['direct_sponsor' => $direct_sponsor, 'current_sponsor' => $current_sponsor]);
        }
        catch(\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function get_membership_plans()
    {
        try
        {
            $plans = UserPlan::all();

            return response()->json(['plans' => $plans], 200);
        }
        catch(\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function edit_member(Request $request)
    {
        try
        {
            $user = User::where('id', $request->input('user_id'))->first();

            $user->email = $request->input('email');

            if ($request->input('password') != '')
            {
                $user->password = bcrypt($request->input('password'));
            }
            
            $user->active = $request->input('active');
            $user->points = $request->input('points');
            $user->right_points = $request->input('right_points');
            $user->plan_id = $request->input('plan_id');

            $detail = UserDetail::where('user_id', $request->input('user_id'))->first();

            $detail->fullname = $request->input('fullname');
            $detail->contact_no = $request->input('contact_no');
            $detail->tbc_wallet = $request->input('tbc_wallet');
            $detail->btc_wallet = $request->input('btc_wallet');
            $detail->paylance_wallet = $request->input('paylance_wallet');

            if ($user->save())
            {
                if ($detail->save())
                {
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

    public function delete_member(Request $request)
    {
        try
        {
            $delete = User::where('id', $request->input('user_id'))->delete();

            if ($delete) 
            {
                $delete_details = UserDetail::where('user_id', $request->input('user_id'))->delete();
                if ($delete_details) 
                {
                    $delete_membership = MembershipTransaction::where('user_id', $request->input('user_id'))->where('transaction_type', 'membership')->delete();
                    if ($delete_membership) 
                    {
                        return response()->json(['status' => 'ok'], 200);
                    }
                    
                    return response()->json(['status' => 'fail'], 200);
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

    public function get_member_stats()
    {
        try
        {
            $total = User::count();
            $active = User::where('active', 1)->count();
            $inactive = User::where('active', 0)->count();

            return response()->json([
                'total'     => $total,
                'active'    => $active,
                'inactive'  => $inactive,
            ], 200);
        }
        catch(\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function member_bonus_logs_view()
    {
        return view('pages.admin.member-bonus-logs');
    }

    public function member_bonus_logs(Request $request)
    {
        try
        {
            $user_id = $request->input('search_input');

            if ($user_id)
            {
                $bonus_logs = \DB::table('bonus_points_logs')
                            ->selectRaw('bonus_points_logs.points, bonus_points_logs.action_type, bonus_points_logs.action_detail, user_details.fullname, users.alias_name, bonus_points_logs.created_at')
                            ->leftJoin('users', 'bonus_points_logs.from_id', '=', 'users.id')
                            ->leftJoin('user_details', 'users.id', '=', 'user_details.user_id')
                            ->where('bonus_points_logs.user_id', $user_id)
                            ->orderBy('bonus_points_logs.created_at', 'desc')
                            ->paginate(10);

                return response()->json(['bonus_logs' => $bonus_logs], 200);
            }   
            
            return response()->json(['bonus_logs' => null], 200);
        }
        catch(\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
