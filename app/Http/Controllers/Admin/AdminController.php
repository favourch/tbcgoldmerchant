<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Library\CompanyAssetLib;

use App\Admin;
use App\Models\CompanyAsset;

use Auth;
use Hash;

class AdminController extends Controller
{
    public function __construct()
    {

    }

    public function dashboard_view()
    {
        return view('pages.admin.dashboard');
    }

    public function get_company_btc_account(CompanyAssetLib $company_asset)
    {
        try
        {
            $company = $company_asset->get();

            return response()->json(['company' => $company], 200);
        }
        catch(\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function account_view()
    {
        return view('pages.admin.account');
    }

    public function account_details()
    {
        try
        {
            $account = Admin::where('id', Auth::guard('admin')->user()->id)->first();

            $wallets = \DB::table('company_assets')->where('id', 1)->first();

            return response()->json(['account' => $account, 'wallets' => $wallets], 200);
        }
        catch(\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function account_update_username(Request $request)
    {
        try
        {
            $account = Admin::where('id', $request->input('account_id'))->first();

            $account->email = $request->input('username');
            $account->fullname = $request->input('fullname');

            if ($account->save())
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

    public function update_wallets(Request $request)
    {
        try
        {
            $wallet = CompanyAsset::where('id', 1)->first();

            $wallet->btc_wallet = $request->input('membership_wallet');
            $wallet->btc_wallet2 = $request->input('maintenance_wallet');

            if ($wallet->save())
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

    public function account_update_password(Request $request)
    {
        try
        {
            $account = Admin::where('id', $request->input('account_id'))->first();

            if (Hash::check($request->input('old_password'), $account->password) == true)
            {
                $account->password = bcrypt($request->input('new_password'));

                if ($account->save())
                {
                    return response()->json(['status' => 'ok'], 200);
                }

                return response()->json(['status' => 'fail'], 200);
            }

            return response()->json(['status' => 'error'], 200);
            
        }
        catch(\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
