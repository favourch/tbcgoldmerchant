<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\User;

class UserController extends Controller
{
    public function __construct()
    {

    }

    public function account_details()
    {
    	try
    	{
    		$user = auth()->user();

			return response()->json(['user' => $user], 200);
    	}
    	catch(\Exception $e)
    	{
    		return response()->json(['error' => $e->getMessage()], 500);
    	}
    }
}
