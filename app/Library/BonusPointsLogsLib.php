<?php

namespace App\Library;

use App\Models\BonusPointsLog;

class BonusPointsLogsLib {

	public function __construct()
	{

	}

	public function save($data)
	{
		$model = new BonusPointsLog;

		$model->points 			= $data['points'];
		$model->action_type 	= $data['action_type'];
		$model->action_detail 	= $data['action_detail'];
		$model->user_id 		= $data['user_id'];
		$model->from_id 		= $data['from_id'];
		
		if ($model->save())
		{
			return true;
		}

		return false;
	}

}