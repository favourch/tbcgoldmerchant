<?php

namespace App\Http\Controllers\Member;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\PointTransaction;

class PointTransactionController extends Controller
{
	public function __construct()
	{

	}

    public function get_point_transactions($user_id, Request $request)
    {
        try
        {
            $limit = $request->input('limit');

            $point_transactions = PointTransaction::where('user_id', $user_id)
                                                    ->when($limit != null, function($query) use($limit) {
                                                        return $query->take(5);
                                                    })
                                                    ->orderBy('created_at', 'desc')
                                                    ->paginate(10);

            $total_cashout = PointTransaction::where('user_id', $user_id)->sum('usd_value');
            $total_cashout_referral = PointTransaction::where('user_id', $user_id)->where('point_transaction_type', 'referral')->sum('usd_value');
            $total_cashout_pairing = PointTransaction::where('user_id', $user_id)->where('point_transaction_type', 'pairing')->sum('usd_value');

            return response()->json([
                'point_transactions' => $point_transactions, 
                'total_cashout' => $total_cashout, 
                'total_cashout_referral'    => $total_cashout_referral,
                'total_cashout_pairing' => $total_cashout_pairing,
            ], 200);
        }
        catch(\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
