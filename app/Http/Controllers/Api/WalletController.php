<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Library\CompanyAssetLib;

use App\Admin;
use App\Models\CompanyAsset;

use Auth;
use Hash;

class WalletController extends Controller
{
    public function get_admin_wallet(CompanyAssetLib $company_asset)
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
}
