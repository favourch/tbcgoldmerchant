<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;

class TbcController extends Controller
{
    public function __construct()
    {

    }

    public function get_tbc_rates()
    {
    	try
    	{
    		$result = DB::table('company_assets')
    					->where('id', 1)
    					->first();

    		return response()->json(['result' => $result], 200);
    	}
    	catch(\Exception $e)
    	{
    		return response()->json(['error' => $e->getMessage()], 500);
    	}
    }
}
