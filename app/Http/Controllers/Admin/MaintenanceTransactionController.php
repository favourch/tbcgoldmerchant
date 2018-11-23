<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\User;
use App\Models\MaintenanceTransaction;
use Carbon\Carbon;

use App\Library\PointSystemLib;

class MaintenanceTransactionController extends Controller
{
    protected $point_system_lib;

    public function __construct(PointSystemLib $point_system_lib)
    {
        $this->point_system_lib = $point_system_lib;
    }

    public function maintenance_transactions_view()
    {
    	return view('pages.admin.maintenance-transaction.maintenance-transactions');
    }

    public function get_maintenance_transactions(Request $request)
    {
    	try
    	{                      
            $search_by = $request->input('search_by');
            $search_input = $request->input('search_input');
            $filter_by_status = $request->input('filter_by_status');

            $maintenance_transactions = \DB::table('maintenance_transactions')
                                ->selectRaw('maintenance_transactions.id as maintenance_transaction_id, maintenance_transactions.transaction_hash, user_details.fullname, maintenance_transactions.created_at, maintenance_transactions.status, maintenance_transactions.user_id, users.alias_name, maintenance_transactions.maintenance_cost')
                                ->leftJoin('user_details', 'maintenance_transactions.user_id', '=', 'user_details.user_id')
                                ->leftJoin('users', 'maintenance_transactions.user_id', '=', 'users.id')
                                ->when($filter_by_status != '', function($query) use($filter_by_status) {
                                    return $query->where('maintenance_transactions.status', $filter_by_status);
                                })
                                ->when($search_by != '', function($query) use($search_by, $search_input) {
                                    if ($search_by == 'transaction_hash')
                                    {
                                        return $query->where('maintenance_transactions.transaction_hash', 'like', '%'.$search_input.'%');
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

            $total_maintenance = \DB::table('maintenance_transactions')->sum('maintenance_cost');
            $total_maintenance_pending = \DB::table('maintenance_transactions')->where('status', 'pending')->sum('maintenance_cost');
            $total_maintenance_confirmed = \DB::table('maintenance_transactions')->where('status', 'confirmed')->sum('maintenance_cost');
            $total_maintenance_denied = \DB::table('maintenance_transactions')->where('status', 'denied')->sum('maintenance_cost');

            $total = \DB::table('maintenance_transactions')->count();
            $total_pending = \DB::table('maintenance_transactions')->where('status', 'pending')->count();
            $total_confirmed = \DB::table('maintenance_transactions')->where('status', 'confirmed')->count();
            $total_denied = \DB::table('maintenance_transactions')->where('status', 'denied')->count();

    		return response()->json([
                'maintenance_transactions'  => $maintenance_transactions,
                'total_maintenance'             => $total_maintenance,
                'total_maintenance_pending'     => $total_maintenance_pending,
                'total_maintenance_confirmed'   => $total_maintenance_confirmed,
                'total_maintenance_denied'      => $total_maintenance_denied,
                'total'                     => $total,
                'total_pending'             => $total_pending,
                'total_confirmed'           => $total_confirmed,
                'total_denied'              => $total_denied,
            ], 200);
    	}
    	catch(\Exception $e)
    	{
    		return response()->json(['error' => $e->getMessage()], 500);
    	}
    }

    public function maintenance_transaction_details_view($maintenance_transaction_id)
    {
        return view('pages.admin.maintenance-transaction.maintenance-transaction-details', [
            'maintenance_transaction_id'    => $maintenance_transaction_id
        ]);
    }

    public function get_maintenance_transaction_details($maintenance_transaction_id)
    {
        try
        {
            $maintenance_transaction = MaintenanceTransaction::with('user', 'user_detail')->where('id', $maintenance_transaction_id)->first();

            return response()->json(['maintenance_transaction' => $maintenance_transaction], 200);
        }
        catch(\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function confirm_maintenance_transaction(Request $request)
    {
        try
        {
            $transaction = MaintenanceTransaction::findOrFail($request->input('maintenance_transaction_id'));

            $transaction->status = 'confirmed';
            $transaction->confirmed_at = date('Y-m-d');

            if ($transaction->save())
            {
                $user = User::findOrFail($transaction->user_id);

                $today = Carbon::parse(date('Y-m-d H:i:s'));

                $user->user_status = 'activated';
                $user->last_activated_at = $today;

                // $end_activation_at = Carbon::parse($user->end_activation_at);
                // $this_month = new Carbon('first day of this month');
                $user->end_activation_at = $today->addDays(30);

                if ($user->save())
                {
                    $this->point_system_lib->add_monthly_points($user->referral_id, $user->id);

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

    public function deny_maintenance_transaction(Request $request)
    {
        try
        {
            $transaction = MaintenanceTransaction::findOrFail($request->input('maintenance_transaction_id'));

            $transaction->status = 'denied';
            $transaction->denied_at = date('Y-m-d');

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
