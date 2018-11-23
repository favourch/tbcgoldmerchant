<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Validator;
use Mail;
use Auth;
use DB;
use Carbon\Carbon;

use App\User;
use App\Models\UserDetail;
use App\Models\UserRole;
use App\Models\UserPlan;
use App\Models\BonusPointsLog;
use App\Models\MembershipTransaction;
use App\Models\CashoutTransaction;
use App\Models\LevelTwoUser;

use App\Library\BonusPointsLogsLib;
use App\Library\CompanyAssetLib;
use App\Library\PointSystemLib;

class MembershipTransactionController extends Controller
{
    protected $point_system_lib;

    public function __construct(PointSystemLib $point_system_lib)
    {
        $this->point_system_lib = $point_system_lib;
    }

    public function membership_transactions_view()
    {
    	return view('pages.admin.membership-transaction.membership-transactions');
    }
    
    public function get_membership_transactions(Request $request)
    {
    	try
    	{                      
            $search_by = $request->input('search_by');
            $search_input = $request->input('search_input');
            $filter_by_status = $request->input('filter_by_status');

            $membership_transactions = \DB::table('membership_transactions')
                                ->selectRaw('membership_transactions.id as membership_transaction_id, membership_transactions.transaction_type, membership_transactions.transaction_hash, membership_transactions.transaction_confirm_no, user_details.fullname, membership_transactions.created_at, membership_transactions.status, user_plans.name, user_plans.price, membership_transactions.user_id, users.alias_name')
                                ->leftJoin('user_details', 'membership_transactions.user_id', '=', 'user_details.user_id')
                                ->leftJoin('user_plans', 'membership_transactions.plan_id', '=', 'user_plans.id')
                                ->leftJoin('users', 'membership_transactions.user_id', '=', 'users.id')
                                ->when($filter_by_status != '', function($query) use($filter_by_status) {
                                    return $query->where('membership_transactions.status', $filter_by_status);
                                })
                                ->when($search_by != '', function($query) use($search_by, $search_input) {
                                    if ($search_by == 'transaction_hash')
                                    {
                                        return $query->where('membership_transactions.transaction_hash', 'like', '%'.$search_input.'%');
                                    }
                                    elseif ($search_by == 'alias_name')
                                    {
                                        return $query->where('users.alias_name', 'like', '%'.$search_input.'%');
                                    }
                                    elseif ($search_by == 'fullname')
                                    {
                                        return $query->where('user_details.fullname', 'like', '%'.$search_input.'%');
                                    }
                                })
                                ->paginate(10);

            $total_cashout = \DB::table('membership_transactions')->sum('plan_cost');
            $total_cashout_pending = \DB::table('membership_transactions')->where('status', 'pending')->sum('plan_cost');
            $total_cashout_confirmed = \DB::table('membership_transactions')->where('status', 'confirmed')->sum('plan_cost');
            $total_cashout_denied = \DB::table('membership_transactions')->where('status', 'denied')->sum('plan_cost');

            $total = MembershipTransaction::count();
            $pending = MembershipTransaction::where('status', 'pending')->count();
            $confirmed = MembershipTransaction::where('status', 'confirmed')->count();
            $denied = MembershipTransaction::where('status', 'denied')->count();

    		return response()->json([
                'membership_transactions'   => $membership_transactions,
                'total_cashout'             => $total_cashout,
                'total_cashout_pending'     => $total_cashout_pending,
                'total_cashout_confirmed'   => $total_cashout_confirmed,
                'total_cashout_denied'      => $total_cashout_denied,
                'total'                     => $total,
                'pending'                   => $pending,
                'confirmed'                 => $confirmed,
                'denied'                    => $denied,
            ], 200);
    	}
    	catch(\Exception $e)
    	{
    		return response()->json(['error' => $e->getMessage()], 500);
    	}
    }

    public function membership_transaction_details_view($membership_transaction_id)
    {
        return view('pages.admin.membership-transaction.membership-transaction-details', [
            'membership_transaction_id'    => $membership_transaction_id
        ]);
    }

    public function get_membership_transaction_details($membership_transaction_id)
    {
        try
        {
            $membership_transaction = MembershipTransaction::with('user_detail', 'plan', 'user')->where('id', $membership_transaction_id)->first();

            $total_gc = CashoutTransaction::where('user_id', $membership_transaction->user->id)->sum('deduction_gc');

            return response()->json(['membership_transaction' => $membership_transaction, 'total_gc' => $total_gc], 200);
        }
        catch(\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function confirm_membership_transaction(Request $request)
    {
        try
        {
            $user = User::findOrFail($request->input('user_id'));

            $user_detail = UserDetail::where('user_id', $request->input('user_id'))->first();

            $sponsor = User::where('id', $user->direct_sponsor)->first();

            $this->point_system_lib->add_referral_point($sponsor->id, $user->id);

            $this->get_upline($user->referral_id, $user->id);

            $now = date('Y-m-d H:i:s');
            $end_activation_at = date('Y-m-d H:i:s', strtotime($now . ' + 30 days'));


            $user->last_activated_at = $now;
            $user->confirmed_at = $now;
            $user->end_activation_at = $end_activation_at;
            $user->active = true;
            $user->user_status = 'activated';
            $user->commission_balance = $request->input('commission_balance');
            $user->save();

            $transaction = MembershipTransaction::where('id', $request->input('membership_transaction_id'))->first();

            $transaction->status = 'confirmed';

            $transaction->save();

            return response()->json(['status' => 'ok'], 200);
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
                
                $this->point_system_lib->add_bonus_point($user->id, $referred_id, $position);

                $dynamic_referral_id = $user->referral_id;
                $dynamic_referred_id = $user->id;
            }
            else
            {
                break;
            }
        }
    }

    public function deny_membership_transaction(Request $request)
    {
        try
        {
            $transaction = MembershipTransaction::where('id', $request->input('membership_transaction_id'))->first();
            $transaction->status = 'denied';
            $transaction->save();

            $user = User::findOrFail($request->input('user_id'));
            $user->user_status = 'denied';
            $user->save();

            return response()->json(['status' => 'ok'], 200);
        }
        catch(\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function upgrade_member(Request $request)
    {
        try
        {
            $count = LevelTwoUser::count();

            $user_id = $request->input('user_id');

            $upline_referral_link = 'leader';
               
            $upline_id = 0;

            if ($count < 1)
            {
                $upgraded_users = DB::table('level_two_users')->get();

                foreach ($upgraded_users as $u)
                {
                    $count_downline = \DB::table('level_two_users')->where('level_two_user_referral_id', $u->level_two_user_referral_link)->count();

                    if ($count_downline < 2)
                    {
                        // can be upline
                        
                        $upline_referral_link = $u->level_two_user_referral_link;
                        $upline_id = $u->user_id;
                        break;
                    }
                }

                $data = [
                    'user_id'                                       => $user_id,
                    'level_two_user_referral_id'                    => $upline_referral_link,
                    'level_two_user_referral_link'                  => uniqid(),
                    'level_two_user_direct_sponsor'                 => $upline_id,
                    'level_two_user_referral_points'                => 0,
                    'level_two_user_left_points'                    => 0,
                    'level_two_user_right_points'                   => 0,
                    'level_two_user_unilevel_bonus'                 => 0,
                    'level_two_user_current_cashout_pairing_level'  => 0,
                    'level_two_user_current_cashout_pairing_level'  => 0,
                    'created_at'                                    => date('Y-m-d H:i:s'),
                    'updated_at'                                    => date('Y-m-d H:i:s'),
                ];

                DB::table('level_two_users')->insert($data);

                $user = User::where('id', $user_id)->first();
                $user->commission_balance += $request->input('commission_balance');
                $user->upgrading_total = 0;
                $user->save();

                $transaction = MembershipTransaction::where('id', $request->input('membership_transaction_id'))->first();

                $transaction->status = 'confirmed';

                $transaction->save();

                return response()->json(['status' => 'ok'], 200);
            }

            $upgraded_users = DB::table('level_two_users')->get();

            foreach ($upgraded_users as $u)
            {
                $count_downline = \DB::table('level_two_users')->where('level_two_user_referral_id', $u->level_two_user_referral_link)->count();

                if ($count_downline < 2)
                {
                    // can be upline
                        
                    $upline_referral_link = $u->level_two_user_referral_link;
                    $upline_id = $u->user_id;
                    break;
                }
            }

            $referral_id = uniqid();

            $data = [
                'user_id'                                       => $user_id,
                'level_two_user_referral_id'                    => $upline_referral_link,
                'level_two_user_referral_link'                  => $referral_id,
                'level_two_user_direct_sponsor'                 => $upline_id,
                'level_two_user_referral_points'                => 0,
                'level_two_user_left_points'                    => 0,
                'level_two_user_right_points'                   => 0,
                'level_two_user_unilevel_bonus'                 => 0,
                'level_two_user_current_cashout_pairing_level'  => 0,
                'level_two_user_current_cashout_pairing_level'  => 0,
                'created_at'                                    => date('Y-m-d H:i:s'),
                'updated_at'                                    => date('Y-m-d H:i:s'),
            ];

            DB::table('level_two_users')->insert($data);

            $user = User::where('id', $user_id)->first();
            $user->commission_balance += $request->input('commission_balance');
            $user->upgrading_total = 0;
            $user->save();

            $this->point_system_lib->add_referral_point2($upline_id, $user_id);

            $this->get_upline2($upline_referral_link, $user_id);

            $transaction = MembershipTransaction::where('id', $request->input('membership_transaction_id'))->first();

            $transaction->status = 'confirmed';

            $transaction->save();

            return response()->json(['status' => 'ok'], 200);
        }
        catch(\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function get_upline2($referral_id, $referred_id, $loop_no = 12)
    {
        $uplines = [];

        $dynamic_referral_id = $referral_id;
        $dynamic_referred_id = $referred_id;

        for ($i = 1; $i <= $loop_no; $i++)
        {
            $user = LevelTwoUser::select('id', 'user_id', 'level_two_user_referral_id', 'level_two_user_referral_link')
                            ->where('level_two_user_referral_link', $dynamic_referral_id)
                            ->first();

            if (!is_null($user))
            {
                $xds = LevelTwoUser::select('id', 'user_id', 'level_two_user_referral_id', 'level_two_user_referral_link')
                ->where('level_two_user_referral_id', $user->level_two_user_referral_link)->get();
                
                $count = 0;
                $position = 'left';

                foreach ($xds as $xd)
                {
                    $count++;
                    
                    if ($xd->user_id == $dynamic_referred_id)
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
                
                $this->point_system_lib->add_bonus_point2($user->user_id, $referred_id, $position);

                $dynamic_referral_id = $user->level_two_user_referral_id;
                $dynamic_referred_id = $user->user_id;
            }
            else
            {
                break;
            }
        }
    }
}
