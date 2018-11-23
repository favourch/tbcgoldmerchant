<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Admin;

use Auth;

class SignInController extends Controller
{
    public function __construct()
    {

    }

    public function sign_in_view()
    {
    	return view('pages.admin.auth.sign-in');
    }

    public function sign_in(Request $request)
    {
		try
    	{
	    	if (Auth::guard('admin')->attempt([
	    		'email' 	=> $request->input('email'), 
	    		'password' 	=> $request->input('password'), 
	    		'active' 	=> 1
	    	])) 
	    	{
			    return response()->json(['status' => 'ok'], 200);
			}
	    	
	    	$user_count = Admin::where('email', $request->input('email'))->where('active', false)->count();

	    	if ($user_count > 0)
	    	{
	    		return response()->json(['status' => 'fail', 'error' => 'Account is not yet verified.'], 200);
	    	}

	    	return response()->json(['status' => 'fail', 'error' => 'Wrong username or password.'], 200);
	    	
    	}
    	catch(\Exception $e)
    	{
    		return response()->json(['error' => $e->getMessage()], 500);
    	}
    }
}
