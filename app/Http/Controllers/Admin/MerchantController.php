<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Merchant;

class MerchantController extends Controller
{
    public function __construct()
    {

    }

    public function merchants_view()
    {
    	return view('pages.admin.merchant.merchants');
    }

    public function get_merchants(Request $request)
    {
    	try
    	{
    		$merchants = Merchant::paginate(10);

    		return response()->json(['merchants' => $merchants], 200);
    	}
    	catch(\Exception $e)
    	{
    		return response()->json(['error' => $e->getMessage()], 500);
    	}
    }

    public function merchant_view()
    {
    	return view('pages.admin.merchant.merchant');
    }

    public function get_merchant_details($merchant_id)
    {
    	try
    	{
    		$merchant = Merchant::findOrFail($merchant_id);

    		return response()->json(['merchant' => $merchant], 200);
    	}
    	catch(\Exception $e)
    	{
    		return response()->json(['error' => $e->getMessage()], 500);
    	}
    }

    public function update_merchant_view($merchant_id)
    {
    	return view('pages.admin.merchant.merchant-update', [
    		'merchant_id' => $merchant_id
    	]);
    }

    public function update_merchant(Request $request)
    {
    	try
    	{
    		$merchant = Merchant::findOrFail($request->input('merchant_id'));

    		$merchant->name 		= $request->input('name');
    		$merchant->address		= $request->input('address');
    		$merchant->contact_no	= $request->input('contact_no');
    		$merchant->email		= $request->input('email');

    		if ($request->hasFile('photo'))
    		{
    			$file = $request->file('photo');

    			$filename = time() . '.' . $file->getClientOriginalExtension();

    			$file->move(public_path('upload/merchant'), $filename);

    			$merchant->photo 		= env('APP_URL') . '/upload/merchant/' . $filename;
    			$merchant->photo_alt 	= strtolower(str_replace(' ', '-', $file->getClientOriginalName()));
    		}

    		if ($merchant->save())
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

    public function add_merchant_view()
    {
    	return view('pages.admin.merchant.merchant-add');
    }

    public function add_merchant(Request $request)
    {
    	try
    	{
    		$merchant = new Merchant;

    		$merchant->name 		= $request->input('name');
    		$merchant->address		= $request->input('address');
    		$merchant->contact_no	= $request->input('contact_no');
    		$merchant->email		= $request->input('email');

    		if ($request->hasFile('photo') && $request->file('photo') != null)
    		{
    			$file = $request->file('photo');

    			$filename = time() . '.' . $file->getClientOriginalExtension();

    			$file->move(public_path('upload/merchant'), $filename);

    			$merchant->photo 		= env('APP_URL') . '/upload/merchant/' . $filename;
    			$merchant->photo_alt 	= strtolower(str_replace(' ', '-', $file->getClientOriginalName()));
    		}

    		if ($merchant->save())
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

    public function delete_merchant(Request $request)
    {
    	try
    	{
    		$delete = \DB::table('merchants')->where('id', $request->input('merchant_id'))->delete();

    		if ($delete)
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
