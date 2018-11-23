<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Message;

class AdminMessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function get_messages(Request $request)
    {
        try
        {
            $messages = Message::with('user_detail', 'user')
                                ->where('user_id', $request->input('user_id'))
                                ->Orwhere('user_id', 0)
                                ->get();

            return response()->json(['messages' => $messages], 200);
        }
        catch(\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
