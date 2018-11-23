<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\User;
use App\Models\UnilevelTransaction;
use Carbon\Carbon;

use App\Library\PointSystemLib;

class UnilevelTransactionController extends Controller
{
    protected $point_system_lib;

    public function __construct(PointSystemLib $point_system_lib)
    {
        $this->point_system_lib = $point_system_lib;
    }

    public function unilevel_transactions_view()
    {
    	return view('pages.admin.unilevel-transaction.unilevel-transactions');
    }

    public function get_unilevel_transactions(Request $request)
    {
    	try
    	{                      
            $search_by = $request->input('search_by');
            $search_input = $request->input('search_input');
            $filter_by_status = $request->input('filter_by_status');

            $unilevel_transactions = \DB::table('unilevel_transactions')
                                ->selectRaw('unilevel_transactions.id as unilevel_transaction_id, unilevel_transactions.transaction_hash, user_details.fullname, unilevel_transactions.created_at, unilevel_transactions.status, unilevel_transactions.user_id, users.alias_name, unilevel_transactions.unilevel_bonus')
                                ->leftJoin('user_details', 'unilevel_transactions.user_id', '=', 'user_details.user_id')
                                ->leftJoin('users', 'unilevel_transactions.user_id', '=', 'users.id')
                                ->when($filter_by_status != '', function($query) use($filter_by_status) {
                                    return $query->where('unilevel_transactions.status', $filter_by_status);
                                })
                                ->when($search_by != '', function($query) use($search_by, $search_input) {
                                    if ($search_by == 'transaction_hash')
                                    {
                                        return $query->where('unilevel_transactions.transaction_hash', 'like', '%'.$search_input.'%');
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

            $total_unilevel = \DB::table('unilevel_transactions')->sum('unilevel_bonus');
            $total_unilevel_pending = \DB::table('unilevel_transactions')->where('status', 'pending')->sum('unilevel_bonus');
            $total_unilevel_confirmed = \DB::table('unilevel_transactions')->where('status', 'confirmed')->sum('unilevel_bonus');
            $total_unilevel_denied = \DB::table('unilevel_transactions')->where('status', 'denied')->sum('unilevel_bonus');

            $total = \DB::table('unilevel_transactions')->count();
            $total_pending = \DB::table('unilevel_transactions')->where('status', 'pending')->count();
            $total_confirmed = \DB::table('unilevel_transactions')->where('status', 'confirmed')->count();
            $total_denied = \DB::table('unilevel_transactions')->where('status', 'denied')->count();

    		return response()->json([
                'unilevel_transactions'  	=> $unilevel_transactions,
                'total_unilevel'            => $total_unilevel,
                'total_unilevel_pending'    => $total_unilevel_pending,
                'total_unilevel_confirmed'  => $total_unilevel_confirmed,
                'total_unilevel_denied'     => $total_unilevel_denied,
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

    public function unilevel_transaction_details_view($unilevel_transaction_id)
    {
        return view('pages.admin.unilevel-transaction.unilevel-transaction-details', [
            'unilevel_transaction_id'    => $unilevel_transaction_id
        ]);
    }

    public function get_unilevel_transaction_details($unilevel_transaction_id)
    {
        try
        {
            $unilevel_transaction = UnilevelTransaction::with('user', 'user_detail')->where('id', $unilevel_transaction_id)->first();

            return response()->json(['unilevel_transaction' => $unilevel_transaction], 200);
        }
        catch(\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function confirm_unilevel_transaction(Request $request)
    {
        try
        {
            $transaction = UnilevelTransaction::findOrFail($request->input('unilevel_transaction_id'));

            $transaction->status = 'confirmed';
            $transaction->confirmed_unilevel_bonus = $request->input('confirmed_unilevel_bonus');
            $transaction->transaction_hash = $request->input('transaction_hash');
            $transaction->confirmed_at = date('Y-m-d H:i:s');

            if ($transaction->save())
            {
                $user = User::findOrFail($request->input('user_id'));
                $user->unilevel_bonus -= $request->input('confirmed_unilevel_bonus');

                if ($user->save())
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

    public function deny_unilevel_transaction(Request $request)
    {
        try
        {
            $transaction = UnilevelTransaction::findOrFail($request->input('unilevel_transaction_id'));

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

    public function update_transaction_hash(Request $request)
    {
        try
        {
            $transaction = UnilevelTransaction::findOrFail($request->input('unilevel_transaction_id'));

            $transaction->transaction_hash = $request->input('transaction_hash');

            if ($transaction->save())
            {
                return response()->json(['statuts' => 'ok'], 200);
            }

            return response()->json(['statuts' => 'fail'], 200);
        }
        catch(\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
