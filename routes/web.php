<?php

use Carbon\Carbon;

Route::get('/', 'PageController@home_view')->name('home.view');

Route::get('/login', function() {
	return redirect()->route('sign-in.view');
})->name('login');

Route::get('/news-and-updates', 'PageController@about_view')
		->name('about.view');
Route::get('/contacts', 'PageController@contacts_view')
		->name('contacts.view');
Route::get('/merchant', 'PageController@merchant_view')
		->name('merchant.view');

Route::post('/api/send-message', 'PageController@send_message');

Route::get('/company-btc-account', 'Admin\AdminController@get_company_btc_account');

Route::get('/blockchain/callback', 'BlockChainApiController@handle_callback_url')
		->name('blockchain.callback');

Route::get('/api/blockchain/btc-exchange-rates', 'BlockChainApiController@get_btc_exchange_rates');

Route::prefix('member')->group(function () {

	Route::post('/api/validate-email', 'Member\SignUpController@validate_email');
	Route::post('/api/validate-alias-unique', 'Member\SignUpController@validate_alias_unique');
	Route::post('/api/validate-alias-exists', 'Member\SignUpController@validate_alias_exists');
	Route::post('/api/validate-alias-exists2', 'Member\SignUpController@validate_alias_exists2');
	Route::post('/api/validate-sponsor-alias', 'Member\SignUpController@validate_sponsor_alias');
	Route::post('/api/validate-sponsor-alias2', 'Member\SignUpController@validate_sponsor_alias2');
	Route::post('/api/validate-sponsor-alias3', 'Member\SignUpController@validate_sponsor_alias3');
	Route::post('/api/validate-transaction-hash', 'Member\SignUpController@validate_transaction_hash');


	Route::post('/transaction-hash/update/{user_id}', 'Member\TransactionController@update_hash')->name('member.transaction-hash.update');


	Route::get('/sign-in', 'Member\SignInController@sign_in_view')
			->name('sign-in.view')
			->middleware('guest');
	Route::post('/api/sign-in', 'Member\SignInController@sign_in');

	Route::get('/sign-out', 'Member\SignOutController@sign_out')
			->name('sign-out');

	Route::get('/confirm-email/{email}', 'Member\MemberController@confirm_email')
			->name('member.confirm-email');


	Route::get('/sign-up/{referral_link}', 'Member\SignUpController@sign_up_view')
			->name('sign-up.view');
	Route::post('/api/sign-up', 'Member\SignUpController@sign_up');
	Route::get('/sign-up-with-sponsor', 'Member\SignUpController@sign_up_with_sponsor_view')
			->name('sign-up-with-sponsor.view');
	Route::post('/api/sign-up-with-sponsor', 'Member\SignUpController@sign_up_with_sponsor');

	Route::get('/downline/add', 'Member\AccountController@add_downline_view')
			->name('member.downline.add.view');
	Route::post('/api/downline/add', 'Member\AccountController@add_downline');

	Route::get('/api/referral/details', 'Member\MemberController@get_referral_details');

	
	// Monthly Maintenance
	Route::get('/monthly-maintenance', 'Member\AccountController@monthly_maintenance_view')
			->name('member.monthly-maintenance.view')
			->middleware('auth');
	Route::post('/api/monthly-maintenance/pay', 'Member\AccountController@pay_monthly_maintenance');
    Route::post('/monthly-maintenance/update/transaction-hash', 'Member\AccountController@update_monthly_maintenance_hash')->name('member.monthly-maintenance.transaction-hash.update');
    
    Route::post('/api/monthly-maintenance/update/transaction-hash', 'Member\AccountController@update_monthly_maintenance_hash2');

    Route::get('/dashboard', 'Member\DashboardController@dashboard_view')
    		->name('member.dashboard.view')
    		->middleware('auth');

	Route::get('/account', 'Member\MemberController@account_view')
			->name('member.account.view')
			->middleware('auth');

	Route::get('/bonus-points-logs', 'Member\MemberController@bonus_points_logs_view')
			->name('member.bonus-points-logs.view')
			->middleware('auth');

	Route::get('/genology', 'Member\MemberController@genology_view')
			->name('member.genology.view')
			->middleware('auth');

	Route::get('/merchant', 'Member\MemberController@merchant_view')
			->name('member.merchant.view')
			->middleware('auth');

	Route::get('/ways-to-earn', 'Member\MemberController@ways_to_earn_view')
			->name('member.ways-to-earn.view')
			->middleware('auth');
	
	Route::get('/api/account-details', 'Member\MemberController@account_details')
			->middleware('auth');

	Route::get('/api/downlines', 'Member\MemberController@get_member_downlines')
            ->middleware('auth');

    Route::get('/api/first-downlines', 'Member\MemberController@get_first_downlines')
            ->middleware('auth');

    Route::get('/matrix-two/genology', 'Member\MemberController@genology2_view')
            ->name('member.genology2.view')
            ->middleware('auth');

    Route::get('/api/level2/downlines', 'Member\MemberController@get_member_level2_downlines')
			->middleware('auth');

	Route::get('/api/level2/first-downlines', 'Member\MemberController@get_first_level2_downlines')
			->middleware('auth');

	Route::post('/api/give-recruit', 'Member\MemberController@give_recruit')
			->middleware('auth');
	Route::get('/api/bonus-points-logs', 'Member\MemberController@get_member_bonus_points_logs')
			->middleware('auth');

	Route::get('/api/merchants', 'Member\MerchantController@get_merchants');

	Route::post('/api/password/update', 'Member\AccountController@update_password');
	Route::post('/api/details/update', 'Member\AccountController@update_details');

	Route::get('/api/{user_id}/transactions', 'Member\TransactionController@get_member_transactions');


	Route::get('/api/get-direct-uplines', 'Member\AccountController@get_direct_uplines');




	Route::get('/cashout', 'Member\CashoutTransactionController@cashout_view')
			->name('member.cashout.view')
			->middleware('auth');;

    Route::post('/api/cashout', 'Member\CashoutTransactionController@cashout');
	Route::post('/api/cashout/matrix-2', 'Member\CashoutTransactionController@cashout_matrix_2');

	Route::get('/cashout/details/{cashout_transaction_id}', 'Member\CashoutTransactionController@cashout_details_view')
		->name('member.cashout-details.view');

	Route::get('/api/cashout/details/{cashout_transaction_id}', 'Member\CashoutTransactionController@get_cashout_details');

	Route::get('/api/cashout/{user_id}', 'Member\CashoutTransactionController@get_cashout_transactions');
	

    Route::post('/api/cashout/unilevel', 'Member\CashoutTransactionController@cashout_unilevel');

    Route::post('/api/membership/upgrade', 'Member\MemberController@upgrade_membership');
});


Route::prefix('tbcgoldadmin')->group(function () {

	// auth
	Route::get('/sign-in', 'Admin\SignInController@sign_in_view')
			->name('admin.sign-in.view')
			->middleware('guest:admin');
	Route::post('/api/sign-in', 'Admin\SignInController@sign_in');
	Route::get('/sign-out', 'Admin\SignOutController@sign_out')
			->name('admin.sign-out');

	// pages
    Route::get('/dashboard', 'Admin\AdminController@dashboard_view')
    		->name('admin.dashboard.view');
    
    Route::get('/account', 'Admin\AdminController@account_view')
    		->name('admin.account.view');
    Route::get('/users', 'Admin\AdminController@users_view')
    		->name('admin.users.view');


    // Membership Transaction
    Route::get('/transactions/membership', 'Admin\MembershipTransactionController@membership_transactions_view')
    		->name('admin.transactions.membership.view')
    		->middleware('auth:admin');
    Route::get('/transactions/membership/details/{membership_transaction_id}', 'Admin\MembershipTransactionController@membership_transaction_details_view')
    		->name('admin.transactions.membership.details.view')
    		->middleware('auth:admin');

    Route::get('/api/transactions/membership', 'Admin\MembershipTransactionController@get_membership_transactions');
    Route::post('/api/transactions/membership/confirm', 'Admin\MembershipTransactionController@confirm_membership_transaction');
    Route::post('/api/transactions/membership/deny', 'Admin\MembershipTransactionController@deny_membership_transaction');
    Route::get('/api/transactions/membership/details/{membership_transaction_id}', 'Admin\MembershipTransactionController@get_membership_transaction_details');



    // Transaction Cashout
    Route::get('/transactions/cashout', 'Admin\CashoutTransactionController@cashout_transactions_view')
    		->name('admin.transactions.cashout.view')
    		->middleware('auth:admin');

    Route::get('/transactions/cashout/details/{cashout_transaction_id}', 'Admin\CashoutTransactionController@cashout_transaction_details_view')
    		->name('admin.transactions.cashout.details.view')
    		->middleware('auth:admin');

    Route::get('/api/transactions/cashout', 'Admin\CashoutTransactionController@get_cashout_transactions');
    Route::get('/api/transactions/cashout/stats', 'Admin\CashoutTransactionController@get_cashout_transactions_stats');
    Route::get('/api/transactions/cashout/details/{cashout_transaction_id}', 'Admin\CashoutTransactionController@get_cashout_transaction_details');
    Route::post('/api/transactions/cashout/hash/update', 'Admin\CashoutTransactionController@update_cashout_transaction_hash');
    Route::post('/api/transactions/cashout/confirm', 'Admin\CashoutTransactionController@confirm_cashout_transaction');
    Route::post('/api/transactions/cashout/deny', 'Admin\CashoutTransactionController@deny_cashout_transaction');


    Route::get('/transactions/cashout/matrix-2', 'Admin\CashoutTransactionController@cashout_transactions2_view')
            ->name('admin.transactions.cashout-matrix-2.view')
            ->middleware('auth:admin');
    Route::get('/api/transactions/cashout/matrix-2', 'Admin\CashoutTransactionController@get_cashout_transactions2');

    Route::post('/api/transactions/cashout/matrix-2/confirm', 'Admin\CashoutTransactionController@confirm_cashout_transaction2');

    Route::post('/api/transactions/cashout/matrix-2/deny', 'Admin\CashoutTransactionController@deny_cashout_transaction2');

    Route::get('/transactions/cashout/matrix-2/details/{cashout_transaction_id}', 'Admin\CashoutTransactionController@cashout_transaction_details_view2')
            ->name('admin.transactions.cashout.details.view')
            ->middleware('auth:admin');

    Route::get('/api/transactions/cashout/matrix-2/details/{cashout_transaction_id}', 'Admin\CashoutTransactionController@get_cashout_transaction_details2');
    Route::post('/api/transactions/cashout/matrix-2/hash/update', 'Admin\CashoutTransactionController@update_cashout_transaction_hash2');

   	// Transaction Maintenance
    Route::get('/transactions/maintenance', 'Admin\MaintenanceTransactionController@maintenance_transactions_view')
    		->name('admin.transactions.maintenance.view')
            ->middleware('auth:admin');

    Route::get('/transactions/maintenance/details/{maintenance_transaction_id}', 'Admin\MaintenanceTransactionController@maintenance_transaction_details_view')
    		->name('admin.transactions.maintenance.details.view')
    		->middleware('auth:admin');

    Route::get('/api/transactions/maintenance/details/{maintenance_transaction_id}', 'Admin\MaintenanceTransactionController@get_maintenance_transaction_details');

    Route::get('/api/transactions/maintenance', 'Admin\MaintenanceTransactionController@get_maintenance_transactions');
    Route::post('/api/transactions/maintenance/confirm', 'Admin\MaintenanceTransactionController@confirm_maintenance_transaction');

    Route::post('/api/transactions/maintenance/deny', 'Admin\MaintenanceTransactionController@deny_maintenance_transaction');


    // Transactions Unilevel
    Route::get('/transactions/unilevel', 'Admin\UnilevelTransactionController@unilevel_transactions_view')
            ->name('admin.transactions.unilevel.view'); 

    Route::get('/api/transactions/unilevel', 'Admin\UnilevelTransactionController@get_unilevel_transactions');

    Route::get('/transactions/unilevel/details/{unilevel_transaction_id}', 'Admin\UnilevelTransactionController@unilevel_transaction_details_view')
            ->name('admin.transactions.unilevel.details.view')
            ->middleware('auth:admin');

    Route::get('/api/transactions/unilevel/details/{unilevel_transaction_id}', 'Admin\UnilevelTransactionController@get_unilevel_transaction_details');

    Route::post('/api/transactions/unilevel/confirm', 'Admin\UnilevelTransactionController@confirm_unilevel_transaction');

    Route::post('/api/transactions/unilevel/deny', 'Admin\UnilevelTransactionController@deny_unilevel_transaction');

    Route::post('/api/transactions/unilevel/hash/update', 'Admin\UnilevelTransactionController@update_transaction_hash');
    
    // Member
    Route::get('/api/members', 'Admin\MemberController@get_members');
    Route::get('/api/member/details/{user_id}', 'Admin\MemberController@get_member_details');
    Route::get('/members', 'Admin\MemberController@members_view')
    		->name('admin.members.view')
    		->middleware('auth:admin');
    Route::get('/member/add', 'Admin\MemberController@add_member_view')
    		->name('admin.member.add.view')
    		->middleware('auth:admin');
    Route::post('/api/member/add', 'Admin\MemberController@add_member');
    Route::get('/api/members/active', 'Admin\MemberController@get_active_members');
    Route::get('/member/edit/{user_id}', 'Admin\MemberController@edit_member_view')
    		->name('admin.member.edit.view')
    		->middleware('auth:admin');
    Route::post('/api/member/edit', 'Admin\MemberController@edit_member');
    Route::get('/api/member/sponsors', 'Admin\MemberController@get_member_sponsors');
    Route::get('/api/membership/plans', 'Admin\MemberController@get_membership_plans');
    Route::get('/api/members/stats', 'Admin\MemberController@get_member_stats');
    Route::post('/api/member/delete', 'Admin\MemberController@delete_member');

    // merchants
    Route::get('/merchants', 'Admin\MerchantController@merchants_view')
    		->name('admin.merchants.view')
    		->middleware('auth:admin');
    Route::get('/api/merchants', 'Admin\MerchantController@get_merchants');
    Route::get('/merchant/add', 'Admin\MerchantController@add_merchant_view')
    		->name('admin.merchant.add.view')
    		->middleware('auth:admin');
    Route::post('/api/merchant/add', 'Admin\MerchantController@add_merchant');
    Route::post('/api/merchant/delete', 'Admin\MerchantController@delete_merchant');
    Route::get('/merchant/update/{merchant_id}', 'Admin\MerchantController@update_merchant_view')
    		->name('admin.merchant.update.view')
    		->middleware('auth:admin');
    Route::get('/api/merchant/details/{merchant_id}', 'Admin\MerchantController@get_merchant_details');
    Route::post('/api/merchant/update', 'Admin\MerchantController@update_merchant');


    // admin account
    Route::get('/account', 'Admin\AdminController@account_view')
    		->name('admin.account.view')
    		->middleware('auth:admin');
    Route::get('/api/account/details', 'Admin\AdminController@account_details');
    Route::post('/api/account/update/username', 'Admin\AdminController@account_update_username');
    Route::post('/api/account/update/password', 'Admin\AdminController@account_update_password');
    Route::post('/api/account/update/wallets', 'Admin\AdminController@update_wallets');


    // blockchain
    Route::get('/api/get-usd-to-btc-value', 'BlockChainApiController@get_usd_to_btc_value');


    // message
    Route::get('/messages', 'Admin\MessageController@messages_view')
    		->name('admin.messages.view');
    Route::get('/api/messages', 'Admin\MessageController@messages');
    Route::get('/api/messages2', 'Admin\MessageController@messages2');
    Route::post('/api/messages', 'Admin\MessageController@send_new_message');
    Route::get('/api/message/{id}', 'Admin\MessageController@message');
    Route::post('/api/message/{id}', 'Admin\MessageController@update_message');
    Route::post('/api/message/delete/{id}', 'Admin\MessageController@delete_message');

    Route::post('/api/upgrade-member', 'Admin\MembershipTransactionController@upgrade_member');

    Route::get('/member/bonus-logs', 'Admin\MemberController@member_bonus_logs_view')->name('admin.member.bonus-logs.view');
    Route::get('/api/member/bonus-logs', 'Admin\MemberController@member_bonus_logs');
});

Route::get('/tbc-rates', 'TbcController@get_tbc_rates');

Route::get('/company-assets/{btc}/{usd}/{php}', function($btc, $usd, $php) {

	$data = [
		'tbc_usd_value' => $usd,
		'tbc_btc_value' => $btc,
		'tbc_php_value' => $php,
	];

	$update = \DB::table('company_assets')->where('id', 1)->update($data);
});

Route::get('monthly-deactivation', function() {
    
    $users = \DB::table('users')
                ->where('active', 1)
                ->get();

    foreach ($users as $user)
    {
        $count_downline = \DB::table('users')->where('referral_id', $user->referral_link)->count();

        if ($count_downline > 0)
        {
            if (date('Y-m-d', strtotime($user->end_activation_at)) <= date('Y-m-d'))
            {
                \DB::table('users')->where('id', $user->id)->update(['user_status' => 'deactivated']);
            }
        }
        else
        {
            // reactivate user
            $end_activation_at = Carbon::parse($user->end_activation_at);

            \DB::table('users')->where('id', $user->id)->update(['end_activation_at' => $end_activation_at->addMonth()]);
        }
    }
    
});

Route::get('set-upgrading-total', function() {

    $users = \App\User::all();

    foreach ($users as $user)
    {
        $leveling_total = \App\Models\CashoutTransaction::where('user_id', $user->id)->sum('deduction_leveling');

        if ($leveling_total > 0)
        {
            $update = \App\User::where('id', $user->id)->first();

            $update->upgrading_total = $leveling_total;

            $update->save();
        }
        
    }

});

Route::get('/setup-unilevel', function () {
    // L1 == $2.5
    // L2 == $0.5
    // l3 == $0.5
    // l4 == $0.8
    // l5 == $0.8
    // l6 == $0.5
    // l7 == $0.5
    // l8 == $0.3
    // l9 == $0.3
    // l10 == $0.3
    $users = \App\User::select('id', 'referral_link', 'referral_id', 'unilevel_bonus')->get();

    foreach ($users as $user)
    {
        $dynamic_referral_id = $user->referral_id;

        for ($i = 1; $i <= 10; $i++)
        {
            $upline = \App\User::select('id', 'referral_link', 'referral_id', 'unilevel_bonus')->where('referral_link', $dynamic_referral_id)->first();

            if (!is_null($upline))
            {
                $upline_downline_count = \App\User::where('referral_id', $upline->referral_link)->count();

                $points = 0;

                // 1-2 L1 == level 1
                // 3-4 L1 == level 4
                // 5-6 L1 == level 7
                // 7 L2 == level 10
    
                if ($i == 1)
                {
                    $points = 2.5;
                }
                elseif ($i == 2)
                {
                    if ($upline_downline_count >= 3) 
                    {
                        $points = 0.5;
                    }
                }
                elseif ($i == 3)
                {
                    if ($upline_downline_count >= 3) 
                    {
                        $points = 0.5;
                    }
                }
                elseif ($i == 4)
                {
                    if ($upline_downline_count >= 3) 
                    {
                        $points = 0.8;
                    }
                }
                elseif ($i == 5)
                {
                    if ($upline_downline_count >= 5) 
                    {
                        $points = 0.8;
                    }
                }
                elseif ($i == 6)
                {
                    if ($upline_downline_count >= 5) 
                    {
                        $points = 0.5;
                    }
                }
                elseif ($i == 7)
                {
                    if ($upline_downline_count >= 5) 
                    {
                        $points = 0.5;
                    }
                }
                elseif ($i == 8)
                {
                    if ($upline_downline_count >= 7) 
                    {
                        $points = 0.3;
                    }
                }
                elseif ($i == 9)
                {
                    if ($upline_downline_count >= 7) 
                    {
                        $points = 0.3;
                    }
                }
                elseif ($i == 10)
                {
                    if ($upline_downline_count >= 7) 
                    {
                        $points = 0.3;
                    }
                }

                if ($points > 0)
                {
                    $upline->unilevel_bonus += $points;

                    $upline->save();

                    $point_log = new \App\Models\BonusPointsLog;
                    $point_log->points          = $points;
                    $point_log->action_type     = 'unilevel-bonus';
                    $point_log->action_detail   = 'unilevel bonus from level ' . $i;
                    $point_log->user_id         = $upline->id;
                    $point_log->from_id         = $user->id;

                    $point_log->save();
                }
                
                $dynamic_referral_id = '';
                $dynamic_referral_id = $upline->referral_id;
            }
        }
    } 
});