<?php

namespace App\Library;

use App\Models\MembershipTransaction;
use App\Models\CashoutTransaction;
use App\Models\CashoutTransaction2;
use App\Models\MaintenanceTransaction;
use App\Models\UnilevelTransaction;
use App\User;

use Carbon\Carbon;

class TransactionLib
{
    public function __construct()
    {

    }

    public function save_membership_transaction($data)
    {
        $transaction = new MembershipTransaction;

        $transaction->transaction_type          = $data['transaction_type'];
        $transaction->transaction_no            = $data['transaction_no'];
        $transaction->transaction_hash          = $data['transaction_hash'];
        $transaction->transaction_confirm_no    = $data['transaction_confirm_no'];
        $transaction->current_btc_value         = $data['current_btc_value'];
        $transaction->plan_id                   = $data['plan_id'];
        $transaction->plan_cost                 = $data['plan_price'];
        $transaction->points                    = $data['points'];
        $transaction->user_id                   = $data['user_id'];
        $transaction->user_tbc_wallet           = $data['user_tbc_wallet'];
        $transaction->admin_tbc_wallet          = $data['admin_tbc_wallet'];
        $transaction->admin_btc_wallet          = $data['admin_btc_wallet'];
        $transaction->upgrading_deduction       = $data['upgrading_deduction'];
        $transaction->status                    = 'pending';

        if ($transaction->save())
        {
            return true;
        }

        return false;
    }

    public function confirm_membership_transaction($data)
    {
        $transaction = MembershipTransaction::findOrFail($data['transaction_id']);

        $transaction->status = 'confirmed';

        if ($transaction->save())
        {
            $user = User::where('id', $transaction->user_id)->first();
            $user->confirmed_at = date('Y-m-d');

            if ($data['commission_balance'] != null)
            {
                $user->commission_balance = $data['commission_balance'];
            }

            $user->save();

            return true;
        }

        return false;
    }

    public function save_cashout_transaction($data)
    {
        $transaction = new CashoutTransaction;

        $transaction->user_id                       = $data['user_id'];
        $transaction->cashout_transaction_type      = $data['cashout_transaction_type'];

        $transaction->referral_points               = $data['referral_points'];
        $transaction->right_points                  = $data['right_points'];
        $transaction->left_points                   = $data['left_points'];

        $transaction->confirmed_referral_points     = 0;
        $transaction->confirmed_left_points         = 0;
        $transaction->confirmed_right_points        = 0;
        
        $transaction->transaction_hash              = null;
        $transaction->btc_value                     = 0.00;
        $transaction->usd_value                     = 0.00;

        $transaction->deduction_gc                  = 0.00;
        $transaction->deduction_admin_fee           = 0.10;
        $transaction->deduction_leveling            = 0.00;
        $transaction->deduction_commission          = 0.00;

        $transaction->status                        = 'pending';

        if ($transaction->save())
        {
            if ($data['cashout_transaction_type'] == 'pairing')
            {
                // $user = User::findOrFail($data['user_id']);

                // $current_cashout_pairing_level = $user->current_cashout_pairing_level;

                // $previous_cashout_pairing_level = $user->current_cashout_pairing_level;

                // $level_to_add = 0;

                // if ($data['right_points'] < $data['left_points'])
                // {
                //     $level_to_add = $data['right_points'] / 100;
                // }
                // else
                // {
                //     $level_to_add = $data['left_points'] / 100;
                // }

                // $user->current_cashout_pairing_level += $level_to_add;

                // if ($level_to_add > 0)
                // {
                //     $user->previous_cashout_pairing_level = $current_cashout_pairing_level;
                // }  

                // $user->save();
            }

            return true;
        }
        
        return false;
    }

    public function save_cashout_transaction_matrix_2($data)
    {
        $transaction = new CashoutTransaction2;

        $transaction->user_id                       = $data['user_id'];
        $transaction->cashout_transaction_type      = $data['cashout_transaction_type'];

        $transaction->referral_points               = $data['referral_points'];
        $transaction->right_points                  = $data['right_points'];
        $transaction->left_points                   = $data['left_points'];

        $transaction->confirmed_referral_points     = 0;
        $transaction->confirmed_left_points         = 0;
        $transaction->confirmed_right_points        = 0;
        
        $transaction->transaction_hash              = null;
        $transaction->btc_value                     = 0.00;
        $transaction->usd_value                     = 0.00;

        $transaction->deduction_gc                  = 0.00;
        $transaction->deduction_admin_fee           = 0.10;
        $transaction->deduction_leveling            = 0.00;
        $transaction->deduction_commission          = 0.00;

        $transaction->status                        = 'pending';

        if ($transaction->save())
        {
            if ($data['cashout_transaction_type'] == 'pairing')
            {
                // $user = User::findOrFail($data['user_id']);

                // $current_cashout_pairing_level = $user->current_cashout_pairing_level;

                // $previous_cashout_pairing_level = $user->current_cashout_pairing_level;

                // $level_to_add = 0;

                // if ($data['right_points'] < $data['left_points'])
                // {
                //     $level_to_add = $data['right_points'] / 100;
                // }
                // else
                // {
                //     $level_to_add = $data['left_points'] / 100;
                // }

                // $user->current_cashout_pairing_level += $level_to_add;

                // if ($level_to_add > 0)
                // {
                //     $user->previous_cashout_pairing_level = $current_cashout_pairing_level;
                // }  

                // $user->save();
            }

            return true;
        }
        
        return false;
    }

    public function confirm_cashout_transaction($data)
    {
        $transaction = CashoutTransaction::findOrFail($data['cashout_transaction_id']);

        $transaction->status                    = 'confirmed';
        $transaction->transaction_hash          = $data['transaction_hash'];
        $transaction->btc_value                 = $data['btc_value'];
        $transaction->usd_value                 = $data['usd_value'];
        $transaction->confirmed_referral_points = $data['confirmed_referral_points'];
        $transaction->confirmed_right_points    = $data['confirmed_right_points'];
        $transaction->confirmed_left_points     = $data['confirmed_left_points'];
        $transaction->deduction_gc              = $data['deduction_gc'];
        $transaction->deduction_admin_fee       = $data['deduction_admin_fee'];
        $transaction->deduction_leveling        = $data['deduction_leveling'];
        $transaction->deduction_commission      = $data['deduction_commission'];

        $transaction->confirmed_at              = date('Y-m-d H:i:s');

        if ($transaction->save())
        {
            $points = $data['confirmed_right_points'] + $data['confirmed_left_points'];


            $user = User::where('id', $transaction->user_id)->first();
            $user->upgrading_total += $data['deduction_leveling'];
            $user->points -= $points;
            $user->left_points -= $data['confirmed_left_points'];
            $user->right_points -= $data['confirmed_right_points'];
            $user->referral_points -= $data['confirmed_referral_points'];
            $user->commission_balance -= $data['deduction_commission'];
            
            
            if ($user->save())
            {
                return true;
            }

            return false;
        }

        return false;
    }

    public function confirm_cashout_transaction2($data)
    {
        $transaction = CashoutTransaction2::findOrFail($data['cashout_transaction_id']);

        $transaction->status                    = 'confirmed';
        $transaction->transaction_hash          = $data['transaction_hash'];
        $transaction->btc_value                 = $data['btc_value'];
        $transaction->usd_value                 = $data['usd_value'];
        $transaction->confirmed_referral_points = $data['confirmed_referral_points'];
        $transaction->confirmed_right_points    = $data['confirmed_right_points'];
        $transaction->confirmed_left_points     = $data['confirmed_left_points'];
        $transaction->deduction_gc              = $data['deduction_gc'];
        $transaction->deduction_admin_fee       = $data['deduction_admin_fee'];
        $transaction->deduction_leveling        = $data['deduction_leveling'];
        $transaction->deduction_commission      = $data['deduction_commission'];

        $transaction->confirmed_at              = date('Y-m-d H:i:s');

        if ($transaction->save())
        {
            $points = $data['confirmed_right_points'] + $data['confirmed_left_points'];


            $user_level_two_user = \App\Models\LevelTwoUser::where('user_id', $transaction->user_id)->first();
            $user_level_two_user->level_two_user_upgrading_total += $data['deduction_leveling'];
            $user_level_two_user->level_two_user_left_points -= $data['confirmed_left_points'];
            $user_level_two_user->level_two_user_right_points -= $data['confirmed_right_points'];
            $user_level_two_user->level_two_user_referral_points -= $data['confirmed_referral_points'];
            $user_level_two_user->level_two_user_commission_balance -= $data['deduction_commission'];
            
            
            if ($user_level_two_user->save())
            {
                return true;
            }

            return false;
        }

        return false;
    }


    public function save_maintenance_transaction($data)
    {
        $transaction = new MaintenanceTransaction;

        $transaction->transaction_hash = $data['transaction_hash'];
        $transaction->maintenance_cost = $data['maintenance_cost'];
        $transaction->user_id = $data['user_id'];
        $transaction->admin_btc_wallet = $data['admin_btc_wallet'];
        $transaction->current_btc_value = $data['current_btc_value'];
        $transaction->status = 'pending';

        if ($transaction->save())
        {
            return true;
        }

        return false;
    }

    public function save_unilevel_transaction($data)
    {
        $transaction = new UnilevelTransaction;

        $transaction->transaction_hash = null;
        $transaction->unilevel_bonus = $data['unilevel_bonus'];
        $transaction->user_id = $data['user_id'];
        $transaction->member_btc_wallet = null;
        $transaction->current_btc_value = $data['current_btc_value'];
        $transaction->status = 'pending';
        $transaction->deduction_admin_fee = 0;
        $transaction->deduction_commission = 0;
        $transaction->confirmed_unilevel_bonus = 0;

        if ($transaction->save())
        {
            return true;
        }

        return false;
    }

    public function confirm_maintenance_transaction($maintenance_transaction_id)
    {
        $transaction = MaintenanceTransaction::findOrFail($maintenance_transaction_id);

        $transaction->status = 'confirmed';

        if ($transaction->save())
        {
            $user = User::findOrFail($transaction->user_id);

            $user->last_activated_at = date('Y-m-d H:i:s');

            $end_activation_at = Carbon::parse($user->end_activation_at);
            $user->end_activation_at = $end_activation_at->addMonth();

            if ($user->save())
            {
                return $user->referral_id;
            }

            return false;
        }

        return false;
    }
}