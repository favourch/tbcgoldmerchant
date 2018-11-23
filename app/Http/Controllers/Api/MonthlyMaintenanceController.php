<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Hash;
use Auth;
use Carbon\Carbon;

use App\User;
use App\Models\UserDetail;
use App\Models\PointTransaction;
use App\Models\MaintenanceTransaction;
use App\Models\UserPlan;
use App\Models\UserRole;

use App\Library\CompanyAssetLib;
use App\Library\TransactionLib;
use App\Library\BlockChainApiLib;

class MonthlyMaintenanceController extends Controller
{
	protected $company_asset_lib;
    protected $transaction_lib;
    protected $block_chain_api_lib;

    public function __construct(CompanyAssetLib $company_asset_lib, TransactionLib $transaction_lib, BlockChainApiLib $block_chain_api_lib)
    {
        $this->company_asset_lib    = $company_asset_lib->get();
        $this->transaction_lib      = $transaction_lib;
        $this->block_chain_api_lib = $block_chain_api_lib;
    }

    public function pay(Request $request) 
    {
        try
        {
        	$payload = auth('api')->payload();
           
               	$data = [
                    'transaction_hash'  => $request->input('transaction_hash'),
                    'maintenance_cost'  => $request->input('maintenance_cost'),
                    'user_id'           => $payload->get('sub'),
                    'admin_btc_wallet'  => $request->input('admin_btc_wallet'),
                    'current_btc_value' => $request->input('btc_value'),
                ];

                if ($this->transaction_lib->save_maintenance_transaction($data))
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
