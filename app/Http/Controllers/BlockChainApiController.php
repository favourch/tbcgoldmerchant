<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Library\CompanyAssetLib;
use App\Library\BlockChainApiLib;
use App\Library\TransactionLib;

use App\Models\UserPlan;

class BlockChainApiController extends Controller
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

    public function handle_callback_url(Request $request)
    {
    	$secret = $request->input('secret');

    	if ($secret != $this->block_chain_api_lib->get_secret_key())
    	{
    		$error = [
    			'error' 	=> 'ERROR_VALIDATION',
    			'message'	=> 'No direct script allowed.'
    		];

    		return $error;
    	}

    	$transaction_no = $request->input('transaction_no');

    	$wallet_address = $request->input('address');

    	$transaction_hash = $request->input('transaction_hash');

    	$confirmation_no = $request->input('confirmations');

    	if ($confirmation_no >= 20)
    	{
    		$data = [
    			'transaction_no' 	=> $transaction_no,
    			'confirmation_no' 	=> $confirmation_no
    		];

    		$this->transaction_lib->update_confirmation_no($data);

    		echo '*ok*';
    	}
    	else
    	{
    		$data = [
    			'transaction_no' 	=> $transaction_no,
    			'confirmation_no' 	=> $confirmation_no
    		];

    		$this->transaction_lib->update_confirmation_no($data);
    	}
    }

    public function handle_callback_url2(Request $request)
    {
        $secret = $request->input('secret');
    }

    public function get_btc_exchange_rates()
    {
        try
        {
            $url = 'https://blockchain.info/ticker';

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $output = curl_exec($ch);

            $decoded_output = json_decode($output, true);

            return $decoded_output;
        }
        catch(\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function get_usd_to_btc_value(Request $request)
    {
        try
        {
            return $this->block_chain_api_lib->get_usd_to_btc_value($request->input('usd_value'));
        }
        catch(\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500); 
        }
    }

    public function get_membership_price()
    {
        try
        {
            $btc_value = $this->block_chain_api_lib->get_to_btc_value();

            $plan = UserPlan::where('name', 'level-1')->first();

            return response()->json([
                'usd_price' => $plan->price,
                'btc_price' => $btc_value
            ], 200);
        }
        catch(\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500); 
        }
    }

    public function get_maintenance_price()
    {
        try
        {
            $admin_btc_wallet = $this->company_asset_lib->btc_wallet2;
            $maintenance_cost = $this->company_asset_lib->maintenance_cost;
            $btc_value = $this->block_chain_api_lib->get_usd_to_btc_value($maintenance_cost);

            return response()->json([
                'usd_price' => $maintenance_cost,
                'btc_price' => $btc_value
            ], 200);
        }
        catch(\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500); 
        }
    }
}
