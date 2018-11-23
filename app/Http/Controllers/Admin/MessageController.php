<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Message;

class MessageController extends Controller
{
    public function __construct()
    {

    }

    public function messages_view()
    {
    	return view('pages.admin.message.messages');
    }

    public function messages()
    {
    	try
    	{
    		$messages = Message::with('user_detail', 'user')->paginate(10);

    		return response()->json(['messages' => $messages], 200);
    	}
    	catch(\Exception $e)
    	{
    		return response()->json(['error' => $e->getMessage()], 500);
    	}
    }

    public function messages2(Request $request)
    {
    	try
    	{
    		$messages = Message::with('user_detail', 'user')
    							->where('user_id', $request->input('user_id'))
    							->Orwhere('user_id', 0)
    							->paginate(5);

    		return response()->json(['messages' => $messages], 200);
    	}
    	catch(\Exception $e)
    	{
    		return response()->json(['error' => $e->getMessage()], 500);
    	}
    }

    public function send_new_message(Request $request)
    {
    	try
    	{
    		$message = new Message;

    		$message->subject = $request->input('subject');
    		$message->message = $request->input('message');
    		$message->admin_id = 1;
    		$message->user_id = $request->input('user_id');

    		if ($message->save())
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

    public function message($id)
    {
    	try
    	{
    		$message = Message::findOrFail($id);

    		return response()->json(['message' => $message], 200);
    	}
    	catch(\Exception $e)
    	{
    		return response()->json(['error' => $e->getMessage()], 500);
    	}
    }

    public function update_message($id, Request $request)
    {
    	try
    	{
    		$message = Message::findOrFail($id);

    		$message->subject = $request->input('subject');
    		$message->message = $request->input('message');
    		$message->user_id = $request->input('user_id');

    		if ($message->save())
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

    public function delete_message($id, Request $request)
    {
    	try
    	{
    		$message = Message::findOrFail($id);

    		if ($message->delete())
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
