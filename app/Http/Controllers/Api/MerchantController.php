<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Merchant;

class MerchantController extends Controller
{
    public function get_merchants()
    {
    	try
    	{
    		$merchants = Merchant::paginate(4);

    		return response()->json(['merchants' => $merchants], 200);
    	}
    	catch(\Exception $e)
    	{
    		return response()->json(['error' => $e->getMessage()], 500);
    	}
    }
}
