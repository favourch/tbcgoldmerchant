<?php

namespace App\Library;

use App\Models\CompanyAsset;

class CompanyAssetLib {

	public function __construct()
	{

	}

	public function get()
	{
		$data = CompanyAsset::where('id', 1)->first();
		return $data;
	}

}