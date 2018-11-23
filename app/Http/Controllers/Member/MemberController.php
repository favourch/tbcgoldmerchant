<?php

namespace App\Http\Controllers\Member;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Auth;
use Validator;

use App\User;
use App\Models\UserDetail;
use App\Models\UserRole;
use App\Models\UserPlan;
use App\Models\LevelTwoUser;

use App\Library\BonusPointsLogsLib;
use App\Library\CompanyAssetLib;
use App\Library\PointSystemLib;
use App\Library\BlockChainApiLib;
use App\Library\TransactionLib;

class MemberController extends Controller
{
    protected $point_system_lib;
    protected $block_chain_api_lib;
    protected $transaction_lib;

    public function __construct(PointSystemLib $point_system_lib, BlockChainApiLib $block_chain_api_lib, TransactionLib $transaction_lib)
    {
        $this->point_system_lib = $point_system_lib;
        $this->block_chain_api_lib  = $block_chain_api_lib;
        $this->transaction_lib  = $transaction_lib;
    }

    public function account_view()
    {
        return view('pages.member.account');
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

    public function bonus_points_logs_view()
    {
        return view('pages.member.bonus-points-logs');
    }

    public function merchant_view()
    {
        return view('pages.member.merchant');
    }

    public function genology_view()
    {
        return view('pages.member.genology');
    }

    public function genology2_view()
    {
        return view('pages.member.genology2');
    }

    public function ways_to_earn_view()
    {
        return view('pages.member.ways-to-earn');
    }

    public function account_details()
    {
        try
        {
            $user = User::with(['detail', 'plan', 'level_two_user'])->findOrFail(Auth::user()->id);


            return response()->json(['status' => 'ok', 'user' => $user], 200);
        }
        catch(\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function confirm_email(Request $request, $email)
    {
        try
        {
            $email_exists = User::where('email', $email)->count();

            if ($email_exists)
            {
                $user = User::where('email', $email)->first();

                $user->active = true;

                if ($user->save())
                {
                    Auth::loginUsingId($user->id);

                    session()->flash('greetings', 'Welcome ' . $user->detail->fullname . '. You are now a member of TBC Gold Merchant.');

                    return redirect()->route('member.dashboard');
                }
            }
        }
        catch(\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function get_referral_details(Request $request)
    {
        try
        {
            $user = User::with(['detail'])->
                        where('referral_link', $request->only('referral_link'))
                        ->first();

            return response()->json(['user' => $user], 200);
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
            $referral_links[] = Auth::user()->referral_link;
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

    public function get_member_bonus_points_logs()
    {
        try
        {
            $logs = \DB::table('bonus_points_logs')
                            ->selectRaw('bonus_points_logs.points, bonus_points_logs.action_type, bonus_points_logs.action_detail, user_details.fullname, users.alias_name, bonus_points_logs.created_at')
                            ->leftJoin('users', 'bonus_points_logs.from_id', '=', 'users.id')
                            ->leftJoin('user_details', 'users.id', '=', 'user_details.user_id')
                            ->where('bonus_points_logs.user_id', Auth::user()->id)
                            ->orderBy('bonus_points_logs.created_at', 'desc')
                            ->paginate(10);

            return response()->json(['logs' => $logs], 200);
        }
        catch(\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function count_member_downline($referral_id)
    {
        $count = User::where('referral_id', $referral_id)->count();

        return $count;
    }

    private function handle_pairing_bonus($referral_link, $loop_no)
    {
        $downline_count = 0;
        $dynamic_referral_links = [];

        $member = \App\User::where('referral_id', $referral_link)
                        ->take(2)
                        ->get();

        if (count($member) < 2)
        {
            return $downline_count;
        }

        $ref_links = [];

        foreach ($member as $mem)
        {
            $ref_links[] = $mem->referral_link;

            for ($i = 1; $i <= $loop_no; $i++)
            {
                $xmem = \App\User::whereIn('referral_id', $ref_links)->get();

                $ref_links = [];

                if (count($xmem) > 0)
                {
                    foreach($xmem as $xm)
                    {
                        $ref_links[] = $xm->referral_link;
                    }
                }
                else
                {
                    return 0;
                }
                
            }
        }

        foreach ($member as $mem)
        {
            $dynamic_referral_links[] = $mem->referral_link;
        }

        for ($i = 1; $i <= $loop_no; $i++)
        {
            $mem = [];

            $mem = \App\User::whereIn('referral_id', $dynamic_referral_links)->get();


            $dynamic_referral_links = [];

            if (count($mem) > 0)
            {
                foreach ($mem as $m)
                {
                    $dynamic_referral_links[] = $m->referral_link;
                }
            }
            
            if ($i == $loop_no)
            {
                $downline_count = count($dynamic_referral_links);
                
                break;
            }
        }

        return $downline_count;
    }

    private function get_upline($referral_id, $loop_no = 12)
    {
        $uplines = [];

        $dynamic_referral_id = $referral_id;

        for ($i = 1; $i <= $loop_no; $i++)
        {
            $member = User::select('referral_id', 'referral_link')
                            ->where('referral_link', $dynamic_referral_id)
                            ->first();

            if (!is_null($member))
            {
                $uplines[] = $member->referral_link;

                $dynamic_referral_id = $member->referral_id;
            }
            else
            {
                break;
            }
            
        }

        return $uplines;
    }

    public function give_recruit(Request $request, BonusPointsLogsLib $bonus_point_logs)
    {
        try
        {
            $member_to_give = User::where('referral_link', $request->input('member_to_give'))->first();

            $member_to_give->referral_id = $request->input('member_to_recieve');

            if ($member_to_give->save())
            {
                $sponsor = User::where('referral_link', $member_to_give->referral_id)->first();

                if ($this->count_member_downline2($sponsor->referral_link) == 2)
                {
                    $this->point_system_lib->add_matching_point($sponsor->id, $member_to_give->id);
                }

                $this->get_upline2($member_to_give->referral_id, $member_to_give->id, Auth::user()->referral_link);

                return response()->json(['status' => 'ok'], 200);
            }
        }
        catch(\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function get_upline2($referral_id, $referred_id, $last_referral_link, $loop_no = 12)
    {
        // $uplines = [];

        $dynamic_referral_id = $referral_id;
        
        for ($i = 1; $i <= $loop_no; $i++)
        {
            $user = User::select('id', 'referral_id', 'referral_link')
                            ->where('referral_link', $dynamic_referral_id)
                            ->where('active', 1)
                            ->first();

            if (!is_null($user))
            {
                // $uplines[] = $user->id;

                if ($user->referral_link != $last_referral_link)
                {
                    $this->point_system_lib->add_bonus_point($user->id, $referred_id);

                    $dynamic_referral_id = $user->referral_id;
                }
                else
                {
                    break;
                }
                
            }
            else
            {
                break;
            }
        }

        // return $uplines;
    }

    private function count_member_downline2($referral_link)
    {
        $count = User::where('referral_id', $referral_link)->where('active', 1)->count();

        return $count;
    }

    public function get_member_level2_downlines(Request $request)
    {
        $referral_links = [];
        $referral_ids = [];

        $previous_link = '';
        $previous_base_link = '';

        if ($request->has('referral_link') && $request->input('referral_link') != '')
        {  
            $referral_links[] = $request->input('referral_link');
            $previous = LevelTwoUser::with('user_detail', 'user')->where('level_two_user_referral_link', $request->input('level_two_user_referral_link'))->first();
            $previous_link = $previous->level_two_user_referral_id;
            $previous2 = LevelTwoUser::with('user_detail', 'user')->where('level_two_user_referral_link', $previous->level_two_user_referral_id)->first();
            $previous_base_link = $previous2->user_detail->fullname;
        }
        else
        {
            $referral_links[] = Auth::user()->level_two_user->level_two_user_referral_link;
        }
        
        $downlines = [];

        for ($i = 1; $i <= 8; $i++)
        {
            $members = $this->get_level2_downlines($referral_links);

            if ($members != null)
            {
                $referral_links = [];
                $referral_ids = [];

                foreach ($members as $mem)
                {
                    $referral_links[] = $mem->level_two_user_referral_link;
                    $referral_ids[] = $mem->level_two_user_referral_id;
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

    public function get_first_level2_downlines(Request $request)
    {
        try
        {
            $members = LevelTwoUser::with('user_detail', 'user')->where('level_two_user_referral_id', $request->input('referral_link'))->get();

            return response()->json(['members' => $members], 200);
        }
        catch(\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function get_level2_downlines($referral_links)
    {
        $member = LevelTwoUser::with('user_detail', 'user')->whereIn('level_two_user_referral_id', $referral_links)->orderBy('created_at', 'asc')->get();

        if (count($member) > 0)
        {
            return $member;
        }
        else
        {
            return null;
        }
    }
}
