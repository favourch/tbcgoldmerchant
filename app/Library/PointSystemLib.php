<?php

namespace App\Library;

use App\Models\BonusPointsLog;
use App\User;
use App\Models\UserPlan;
use App\Models\LevelTwoUser;

class PointSystemLib
{
	public function __construct()
	{

	}

	public function add_bonus_point($user_id, $referred_id, $position = null)
	{
		$user = User::where('id', $user_id)->first();

		$plan = UserPlan::where('id', $user->plan_id)->first();
		$user->points += $plan->bonus_points;

		if ($position == 'left')
		{
			$user->left_points += $plan->bonus_points;
		}
		elseif ($position == 'right')
		{
			$user->right_points += $plan->bonus_points;
		}

		if ($user->save())
		{
			$point_log = new BonusPointsLog;

			$point_log->points 			= $plan->bonus_points;
			$point_log->action_type 	= 'bonus-point';
			$point_log->action_detail 	= 'downline bonus point';
			$point_log->user_id 		= $user->id;
			$point_log->from_id 		= $referred_id;

			$point_log->save();

			return true;
		}

		return false;
	}

	public function add_bonus_point2($user_id, $referred_id, $position = null)
	{
		$user = LevelTwoUser::where('user_id', $user_id)->first();

		$plan = UserPlan::where('id', $user->plan_id)->first();

		if ($position == 'left')
		{
			$user->level_two_user_left_points += $plan->bonus_points;
		}
		elseif ($position == 'right')
		{
			$user->level_two_user_right_points += $plan->bonus_points;
		}

		if ($user->save())
		{
			$point_log = new BonusPointsLog;

			$point_log->points 			= $plan->bonus_points;
			$point_log->action_type 	= 'level-2-bonus-point';
			$point_log->action_detail 	= 'downline bonus point';
			$point_log->user_id 		= $user->user_id;
			$point_log->from_id 		= $referred_id;

			$point_log->save();

			return true;
		}

		return false;
	}

	public function add_referral_point($user_id, $referred_id)
	{
		$user = User::where('id', $user_id)->first();

		$plan = UserPlan::where('id', $user->plan_id)->first();

		// if ($this->check_referred_member_position($user->id, $user->referral_link, $referred_id) == 1)
		// {
		// 	$user->left_points += $plan->bonus_points;
		// }
		// else
		// {
		// 	$user->right_points += $plan->bonus_points;
		// }

		$user->referral_points += $plan->bonus_points;

		if ($user->save())
		{
			$sponsor = User::where('referral_link', $user->referral_id)->first();

			$point_log = new BonusPointsLog;

			$point_log->points 			= $plan->bonus_points;
			$point_log->action_type 	= 'direct-referral-point';
			$point_log->action_detail 	= 'direct-referral bonus point';
			$point_log->user_id 		= $user->id;
			$point_log->from_id 		= $referred_id;

			$point_log->save();

			return true;
		}

		return false;
	}

	public function add_referral_point2($user_id, $referred_id)
	{
		$user = LevelTwoUser::where('user_id', $user_id)->first();

		$plan = UserPlan::where('id', $user->plan_id)->first();

		$user->level_two_user_referral_points += $plan->bonus_points;

		if ($user->save())
		{
			$sponsor = LevelTwoUser::where('level_two_user_referral_link', $user->level_two_user_referral_id)->first();

			$point_log = new BonusPointsLog;

			$point_log->points 			= $plan->bonus_points;
			$point_log->action_type 	= 'level-2-direct-referral-point';
			$point_log->action_detail 	= 'direct-referral bonus point';
			$point_log->user_id 		= $user->user_id;
			$point_log->from_id 		= $referred_id;

			$point_log->save();

			return true;
		}

		return false;
	}

	private function check_referred_member_position($sponsor_id, $sponsor_link, $referred_id)
	{
		$downlines = User::where('referral_id', $sponsor_link)
							->where('active', 1)
							->orderBy('last_activated_at', 'asc')
							->get();

		$count = 0;

		foreach ($downlines as $dl)
		{
			$count++;
			
			if ($referred_id == $dl->id)
			{	
				break;
			}
		}

		return $count;
	}

	public function add_matching_point($sponsor_id, $referred_id)
	{
		$sponsor = User::where('id', $sponsor_id)->first();

		$sponsor->points += 100;

		if ($sponsor->save())
		{
			$referred = User::where('id', $referred_id)->first();

			$point_log = new BonusPointsLog;

			$point_log->points 			= 100;
			$point_log->action_type 	= 'matching-point';
			$point_log->action_detail 	= 'matching bonus point';
			$point_log->user_id 		= $sponsor->id;
			$point_log->from_id 		= $referred->id;

			$point_log->save();

			return true;
		}

		return false;
	}

	public function add_monthly_points2($referral_link, $user_id)
	{
		$dynamic_referral_link = [];

		$dynamic_referral_link[] = $referral_link;

		$points = 0;

		for ($i = 1; $i <= 10; $i++)
		{
			$downlines = User::whereIn('referral_id', $dynamic_referral_link)->where('active', 1)->get();

			$dynamic_referral_link = [];

			if (count($downlines) > 0)
			{
				foreach ($downlines as $downline)
				{
					if ($i <= 5)
					{
						$points += 0.4;
					}
					else
					{
						$points += 0.2;
					}

					$dynamic_referral_link[] = $downline->referral_link;
				}
			}
			else
			{
				break;
			}
		}

		$user = User::where('id', $user_id)->first();

		$user->unilevel_bonus = $points;

		if ($user->save())
		{
			return true;
		}

		return false;
	}

	public function add_monthly_points($referral_id, $user_id)
	{
		$from = User::where('id', $user_id)->first();

		$dynamic_referral_id = $referral_id;

		for ($i = 1; $i <= 10; $i++)
		{
			$upline = User::where('referral_link', $dynamic_referral_id)->first();

			if (!is_null($upline))
			{
				$points = 0;

				if ($i <= 5)
				{
					$upline->unilevel_bonus += 0.4;
					$points = 0.4;
				}
				else
				{
					$upline->unilevel_bonus += 0.2;
					$points = 0.2;
				}

				$upline->save();

				$point_log = new BonusPointsLog;
				$point_log->points 			= $points;
				$point_log->action_type 	= 'unilevel-bonus';
				$point_log->action_detail 	= 'unilevel bonus from level ' . $i;
				$point_log->user_id 		= $upline->id;
				$point_log->from_id 		= $from->id;

				$point_log->save();

				$dynamic_referral_id = $upline->referral_id;
			}
			else
			{
				break;
			}
		}
	}
}