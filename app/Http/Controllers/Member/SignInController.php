<?php

namespace App\Http\Controllers\Member;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Validator;
use Auth;

use App\User;
use App\Models\UserDetail;
use App\Models\UserRole;
use App\Models\UserPlan;

class SignInController extends Controller
{
    public function __construct()
    {

    }

    public function sign_in_view()
    {
    	return view('pages.member.auth.sign-in');
    }

    public function sign_in(Request $request)
    {
    	try
    	{
	    	if (Auth::attempt([
	    		'email' 	=> $request->input('email'), 
	    		'password' 	=> $request->input('password')
	    	])) 
	    	{
			    return response()->json(['status' => 'ok'], 200);
			}
	    	
	    	$user_count = User::where('email', $request->input('email'))->where('active', false)->count();

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
