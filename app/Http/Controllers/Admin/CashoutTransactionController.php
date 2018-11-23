<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\User;
use App\Models\CashoutTransaction;
use App\Models\CashoutTransaction2;
use App\Models\UserDetail;

use App\Library\TransactionLib;

use Carbon\Carbon;

class CashoutTransactionController extends Controller
{
    protected $transaction_lib;

    public function __construct(TransactionLib $transaction_lib)
    {
        $this->transaction_lib = $transaction_lib;
    }

    public function cashout_transactions_view()
    {
        return view('pages.admin.cashout-transaction.cashout-transactions');
    }

    public function cashout_transactions2_view()
    {
    	return view('pages.admin.cashout-transaction2.cashout-transactions');
    }

    public function get_cashout_transactions(Request $request)
    {
    	try
    	{
            $now = Carbon::now();
            
            $previous = Carbon::now()->subDays(6);

            $search_by = $request->input('search_by');
            $search_input = $request->input('search_input');
            $filter_by_status = $request->input('filter_by_status');

    		$cashout_transactions = \DB::table('cashout_transactions')
                                ->selectRaw('cashout_transactions.id as cashout_transaction_id, cashout_transactions.left_points, cashout_transactions.right_points, cashout_transactions.referral_points, user_details.fullname, cashout_transactions.created_at, users.alias_name, users.previous_cashout_pairing_level, users.current_cashout_pairing_level, cashout_transactions.status, cashout_transactions.cashout_transaction_type, cashout_transactions.user_id')
                                ->leftJoin('user_details', 'cashout_transactions.user_id', '=', 'user_details.user_id')
                                ->leftJoin('users', 'cashout_transactions.user_id', '=', 'users.id')
                                ->when($filter_by_status != '', function($query) use($filter_by_status, $now, $previous) {
                                    if ($filter_by_status == 'confirmed')
                                    {
                                        return $query->where('cashout_transactions.status', $filter_by_status)
                                                ->whereDate('cashout_transactions.created_at', '>=', $previous->toDateString())
                                                ->whereDate('cashout_transactions.created_at', '<=', $now->toDateString());
                                    }
                                    elseif ($filter_by_status == 'pending')
                                    {
                                        return $query->where('cashout_transactions.status', $filter_by_status);
                                                   
                                    }
                                    else
                                    {
                                        return $query->where('cashout_transactions.status', $filter_by_status);
                                    }
                                })
                                ->when($search_by != '', function($query) use($search_by, $search_input) {
                                    if ($search_by == 'transaction_hash')
                                    {
                                        return $query->where('cashout_transactions.transaction_hash', 'like', '%'.$search_input.'%');
                                    }
                                    elseif ($search_by == 'fullname')
                                    {
                                        return $query->where('user_details.fullname', 'like', '%'.$search_input.'%');
                                    }
                                    elseif ($search_by == 'alias_name')
                                    {
                                        return $query->where('users.alias_name', 'like', '%'.$search_input.'%');
                                    }
                                })
                                ->paginate(10);

            $total_cashout = \DB::table('cashout_transactions')
                                ->sum('usd_value');

            $total_cashout_referral = \DB::table('cashout_transactions')
                                        ->where('cashout_transaction_type', 'referral')
                                        ->sum('usd_value');

            $total_cashout_pairing = \DB::table('cashout_transactions')
                                        ->where('cashout_transaction_type', 'pairing')
                                        ->sum('usd_value');

            $total_cashout_pending = \DB::table('cashout_transactions')
                                        ->where('status', 'pending')
                                        ->sum('usd_value');

            $total_cashout_confirmed = \DB::table('cashout_transactions')
                                        ->where('status', 'confirmed')
                                        ->sum('usd_value');

            $total_cashout_denied = \DB::table('cashout_transactions')
                                        ->where('status', 'denied')
                                        ->sum('usd_value');

            $total = CashoutTransaction::count();

            $pending = CashoutTransaction::where('status', 'pending')
                                        ->count();

            $confirmed = CashoutTransaction::where('status', 'confirmed')
                                            ->whereDate('created_at', '>=', $previous->toDateString())
                                            ->whereDate('created_at', '<=', $now->toDateString())
                                            ->count();

            $denied = CashoutTransaction::where('status', 'denied')->count();

    		return response()->json([
                'cashout_transactions'      => $cashout_transactions,
                'total_cashout'             => $total_cashout,
                'total_cashout_referral'    => $total_cashout_referral,
                'total_cashout_pairing'     => $total_cashout_pairing,
                'total_cashout_pending'     => $total_cashout_pending,
                'total_cashout_confirmed'   => $total_cashout_confirmed,
                'total_cashout_denied'      => $total_cashout_denied,
                'total'                     => $total,
                'pending'                   => $pending,
                'confirmed'                 => $confirmed,
                'denied'                    => $denied
            ], 200);
            
    	}
    	catch(\Exception $e)
    	{
    		return response()->json(['error' => $e->getMessage()], 500);
    	}
    }

    public function get_cashout_transactions2(Request $request)
    {
        try
        {
            $now = Carbon::now();
            
            $previous = Carbon::now()->subDays(6);

            $search_by = $request->input('search_by');
            $search_input = $request->input('search_input');
            $filter_by_status = $request->input('filter_by_status');

            $cashout_transactions = \DB::table('cashout_transactions2')
                                ->selectRaw('cashout_transactions2.id as cashout_transaction_id, cashout_transactions2.left_points, cashout_transactions2.right_points, cashout_transactions2.referral_points, user_details.fullname, cashout_transactions2.created_at, users.alias_name, users.previous_cashout_pairing_level, users.current_cashout_pairing_level, cashout_transactions2.status, cashout_transactions2.cashout_transaction_type, cashout_transactions2.user_id')
                                ->leftJoin('user_details', 'cashout_transactions2.user_id', '=', 'user_details.user_id')
                                ->leftJoin('users', 'cashout_transactions2.user_id', '=', 'users.id')
                                ->when($filter_by_status != '', function($query) use($filter_by_status, $now, $previous) {
                                    if ($filter_by_status == 'confirmed')
                                    {
                                        return $query->where('cashout_transactions2.status', $filter_by_status)
                                                ->whereDate('cashout_transactions2.created_at', '>=', $previous->toDateString())
                                                ->whereDate('cashout_transactions2.created_at', '<=', $now->toDateString());
                                    }
                                    elseif ($filter_by_status == 'pending')
                                    {
                                        return $query->where('cashout_transactions2.status', $filter_by_status);
                                                   
                                    }
                                    else
                                    {
                                        return $query->where('cashout_transactions2.status', $filter_by_status);
                                    }
                                })
                                ->when($search_by != '', function($query) use($search_by, $search_input) {
                                    if ($search_by == 'transaction_hash')
                                    {
                                        return $query->where('cashout_transactions2.transaction_hash', 'like', '%'.$search_input.'%');
                                    }
                                    elseif ($search_by == 'fullname')
                                    {
                                        return $query->where('user_details.fullname', 'like', '%'.$search_input.'%');
                                    }
                                    elseif ($search_by == 'alias_name')
                                    {
                                        return $query->where('users.alias_name', 'like', '%'.$search_input.'%');
                                    }
                                })
                                ->paginate(10);

            $total_cashout = \DB::table('cashout_transactions2')
                                ->sum('usd_value');

            $total_cashout_referral = \DB::table('cashout_transactions2')
                                        ->where('cashout_transaction_type', 'referral')
                                        ->sum('usd_value');

            $total_cashout_pairing = \DB::table('cashout_transactions2')
                                        ->where('cashout_transaction_type', 'pairing')
                                        ->sum('usd_value');

            $total_cashout_pending = \DB::table('cashout_transactions2')
                                        ->where('status', 'pending')
                                        ->sum('usd_value');

            $total_cashout_confirmed = \DB::table('cashout_transactions2')
                                        ->where('status', 'confirmed')
                                        ->sum('usd_value');

            $total_cashout_denied = \DB::table('cashout_transactions2')
                                        ->where('status', 'denied')
                                        ->sum('usd_value');

            $total = CashoutTransaction2::count();

            $pending = CashoutTransaction2::where('status', 'pending')
                                        ->count();

            $confirmed = CashoutTransaction2::where('status', 'confirmed')
                                            ->whereDate('created_at', '>=', $previous->toDateString())
                                            ->whereDate('created_at', '<=', $now->toDateString())
                                            ->count();

            $denied = CashoutTransaction2::where('status', 'denied')->count();

            return response()->json([
                'cashout_transactions'      => $cashout_transactions,
                'total_cashout'             => $total_cashout,
                'total_cashout_referral'    => $total_cashout_referral,
                'total_cashout_pairing'     => $total_cashout_pairing,
                'total_cashout_pending'     => $total_cashout_pending,
                'total_cashout_confirmed'   => $total_cashout_confirmed,
                'total_cashout_denied'      => $total_cashout_denied,
                'total'                     => $total,
                'pending'                   => $pending,
                'confirmed'                 => $confirmed,
                'denied'                    => $denied
            ], 200);
            
        }
        catch(\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function cashout_transaction_details_view($cashout_transaction_id)
    {
        return view('pages.admin.cashout-transaction.cashout-transaction-details', [
            'cashout_transaction_id'    => $cashout_transaction_id
        ]);
    }

    public function cashout_transaction_details_view2($cashout_transaction_id)
    {
        return view('pages.admin.cashout-transaction2.cashout-transaction-details', [
            'cashout_transaction_id'    => $cashout_transaction_id
        ]);
    }

    public function get_cashout_transaction_details($cashout_transaction_id)
    {
        try
        {
            $now = Carbon::now();
            $previous = Carbon::now()->subDays(6);

            $cashout_transaction = CashoutTransaction::where('id', $cashout_transaction_id)->first();

            $member = User::with('detail')->where('id', $cashout_transaction->user_id)->first();

            return response()->json([
                'cashout_transaction'   => $cashout_transaction,
                'member'                => $member,
                'status'                => 'ok'
            ], 200);
        }
        catch(\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function get_cashout_transaction_details2($cashout_transaction_id)
    {
        try
        {
            $now = Carbon::now();
            $previous = Carbon::now()->subDays(6);

            $cashout_transaction = CashoutTransaction2::where('id', $cashout_transaction_id)->first();

            $member = \App\Models\LevelTwoUser::with(['user_detail', 'user'])->where('user_id', $cashout_transaction->user_id)->first();

            return response()->json([
                'cashout_transaction'   => $cashout_transaction,
                'member'                => $member,
                'status'                => 'ok'
            ], 200);
        }
        catch(\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function confirm_cashout_transaction(Request $request)
    {
        try
        {
            $cashout_transaction_type = $request->input('transaction_type');

            $data = [
                'cashout_transaction_id'        => $request->input('cashout_transaction_id'),
                'transaction_hash'              => $request->input('transaction_hash'),
                'btc_value'                     => $request->input('btc_value'),
                'usd_value'                     => $request->input('usd_value'),
                'deduction_gc'                  => $request->input('deduction_gc'),
                'deduction_admin_fee'           => $request->input('deduction_admin_fee'),
                'deduction_leveling'            => $request->input('deduction_leveling'),
                'deduction_commission'          => $request->input('deduction_commission'),
                'confirmed_left_points'         => $request->input('confirmed_left_points'),
                'confirmed_right_points'        => $request->input('confirmed_right_points'),
                'confirmed_referral_points'     => $request->input('confirmed_referral_points'),
            ];

            if ($this->transaction_lib->confirm_cashout_transaction($data)) 
            {
                if ($cashout_transaction_type == 'all' || $cashout_transaction_type == 'pairing')
                {
                    $user = User::findOrFail($request->input('user_id'));

                    $current_cashout_pairing_level = $user->current_cashout_pairing_level;
                    $previous_cashout_pairing_level = $user->previous_cashout_pairing_level;

                    $level_to_add = 0;

                    if ($request->input('confirmed_right_points') <= $request->input('confirmed_left_points'))
                    {
                        $level_to_add = $request->input('confirmed_right_points') / 100;
                    }
                    else
                    {
                        $level_to_add = $request->input('confirmed_left_points') / 100;
                    }

                    $user->current_cashout_pairing_level = $user->previous_cashout_pairing_level + $level_to_add;
                    $user->previous_cashout_pairing_level = $current_cashout_pairing_level;

                    if ($user->save())
                    {
                        return response()->json(['status' => 'ok'], 200);
                    }

                    return response()->json(['status' => 'fail'], 200);
                }

                return response()->json(['status' => 'ok'], 200);
            }

            return response()->json(['status' => 'fail'], 200);
        }
        catch(\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function confirm_cashout_transaction2(Request $request)
    {
        try
        {
            $cashout_transaction_type = $request->input('transaction_type');

            $data = [
                'cashout_transaction_id'        => $request->input('cashout_transaction_id'),
                'transaction_hash'              => $request->input('transaction_hash'),
                'btc_value'                     => $request->input('btc_value'),
                'usd_value'                     => $request->input('usd_value'),
                'deduction_gc'                  => $request->input('deduction_gc'),
                'deduction_admin_fee'           => $request->input('deduction_admin_fee'),
                'deduction_leveling'            => $request->input('deduction_leveling'),
                'deduction_commission'          => $request->input('deduction_commission'),
                'confirmed_left_points'         => $request->input('confirmed_left_points'),
                'confirmed_right_points'        => $request->input('confirmed_right_points'),
                'confirmed_referral_points'     => $request->input('confirmed_referral_points'),
            ];

            if ($this->transaction_lib->confirm_cashout_transaction2($data)) 
            {
                if ($cashout_transaction_type == 'all' || $cashout_transaction_type == 'pairing')
                {
                    $user = \App\Models\LevelTwoUser::where('user_id', $request->input('user_id'))->first();

                    $current_cashout_pairing_level = $user->level_two_user_current_cashout_pairing_level;
                    $previous_cashout_pairing_level = $user->level_two_user_previous_cashout_pairing_level;

                    $level_to_add = 0;

                    if ($request->input('confirmed_right_points') <= $request->input('confirmed_left_points'))
                    {
                        $level_to_add = $request->input('confirmed_right_points') / 100;
                    }
                    else
                    {
                        $level_to_add = $request->input('confirmed_left_points') / 100;
                    }

                    $user->level_two_user_current_cashout_pairing_level = $user->level_two_user_previous_cashout_pairing_level + $level_to_add;
                    $user->level_two_user_previous_cashout_pairing_level = $current_cashout_pairing_level;

                    if ($user->save())
                    {
                        return response()->json(['status' => 'ok'], 200);
                    }

                    return response()->json(['status' => 'fail'], 200);
                }

                return response()->json(['status' => 'ok'], 200);
            }

            return response()->json(['status' => 'fail'], 200);
        }
        catch(\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function deny_cashout_transaction(Request $request) 
    {
        try
        {
            $transaction = CashoutTransaction::findOrFail($request->input('cashout_transaction_id'));

            $transaction->status = 'denied';

            if ($transaction->save())
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
    
    public function get_member_right_points(Request $request)
    {
        try
        {
            

            
        }
        catch(\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function get_member_left_points(Request $request)
    {
        try
        {
            $user = User::where('id', $request->input('user_id'))->first();

            if ($user == null)
            {
                return response()->json(['count' => 0], 200);
            }

            $left_downline = User::where('referral_id', $user->referral_link)
                                    ->where('active', 1)
                                    ->orderBy('last_activated_at', 'desc')
                                    ->first();

            $dynamic_referral_links = [];

            $dynamic_referral_links[] = $left_downline->referral_link;

            $count = 1;

            for ($i = 0; $i <= 12; $i++)
            {
                $downlines = User::whereIn('referral_id', $dynamic_referral_links)->get();

                $dynamic_referral_links = [];

                if (count($downlines) > 0)
                {
                    foreach ($downlines as $downline)
                    {
                        $dynamic_referral_links[] = $downline->referral_link;
                        $count++;
                    }
                }
                else
                {
                    break;
                    return $count;
                }
            }

            return response()->json(['count' => $count], 200);
        }
        catch(\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
        
    }

    public function get_member_total_cashout_points(Request $request)
    {
        try
        {
            $total_cashout_points = CashoutTransaction::where('user_id', $request->input('user_id'))->sum('confirmed_referral_points');

            return response()->json(['total_cashout_points' => $total_cashout_points], 200);

        }
        catch(\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function get_member_total_points(Request $request)
    {
        try
        {
            $user = User::where('user_id', $request->input('user_id'))->first();

            return response()->json(['total_points' => $user->points], 200);

        }
        catch(\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update_cashout_transaction_hash(Request $request)
    {
        try
        {
            $transaction = CashoutTransaction::findOrFail($request->input('cashout_transaction_id'));

            $transaction->transaction_hash = $request->input('transaction_hash');

            if ($transaction->save())
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

    public function update_cashout_transaction_hash2(Request $request)
    {
        try
        {
            $transaction = CashoutTransaction2::findOrFail($request->input('cashout_transaction_id'));

            $transaction->transaction_hash = $request->input('transaction_hash');

            if ($transaction->save())
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
