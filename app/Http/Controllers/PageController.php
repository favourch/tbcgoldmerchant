<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\ContactMessage;

class PageController extends Controller
{
    public function __construct()
    {

    }

    public function home_view()
    {
    	return view('pages.home');
    }

    public function about_view()
    {
    	return view('pages.about');
    }

    public function contacts_view()
    {
    	return view('pages.contacts');
    }

    public function merchant_view()
    {
        return view('pages.merchant');
    }

    public function send_message(Request $request)
    {
        try
        {
            $contact = new ContactMessage;

            $contact->fullname = $request->input('fullname');
            $contact->email = $request->input('email');
            $contact->subject = $request->input('subject');
            $contact->message = $request->input('message');

            if ($contact->save())
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
