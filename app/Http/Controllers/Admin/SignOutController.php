<?php

namespace App\Http\Controllers\Admin;

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
    	Auth::guard('admin')->logout();

    	return redirect()->route('admin.sign-in.view');
    }
}
