<?php

namespace App\Http\Controllers\Member;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Auth;

class SignOutController extends Controller
{
    public function __construct()
    {

    }

    public function sign_out()
    {
    	Auth::logout();

    	return redirect()->route('sign-in.view');
    }
}
