<?php

namespace App\Http\Controllers\Member;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\MembershipTransaction;

use Validator;

class TransactionController extends Controller
{
    public function __construct()
    {

    }

    public function get_member_transactions($user_id)
    {
    	try
    	{
    		$transactions = MembershipTransaction::with('plan')->where('user_id', $user_id)->paginate(5);

    		return response()->json(['transactions' => $transactions], 200);
    	}
    	catch(\Exception $e)
    	{
    		return response()->json(['error' => $e->getMessage()], 500);
    	}
    }

    public function update_hash($user_id, Request $request)
    {
        if ($request->input('transaction_hash') != 'cash')
        {
            $transaction = MembershipTransaction::where('user_id', $user_id)->first();

            if ($transaction->transaction_hash != $request->input('transaction_hash'))
            {
                $validator = Validator::make($request->all(), [
                    'transaction_hash' => 'required|unique:membership_transactions,transaction_hash'
                ]);

                if ($validator->fails())
                {
                    return redirect()->back()->withErrors($validator)->withInput();
                }

                $transaction->transaction_hash = $request->input('transaction_hash');

                if ($transaction->save())
                {
                    session()->flash('status', 'ok');
                    return redirect()->back();
                }

                session()->flash('status', 'fail');
                return redirect()->back();
            }
        }

        $transaction = MembershipTransaction::where('user_id', $user_id)->first();
        
        $transaction->transaction_hash = $request->input('transaction_hash');

        if ($transaction->save())
        {
            session()->flash('status', 'ok');
            return redirect()->back();
        }

        session()->flash('status', 'fail');
        return redirect()->back();
    }
}
