<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', 'Api\AuthController@login');
    Route::post('logout', 'Api\AuthController@logout');
    Route::post('refresh', 'Api\AuthController@refresh');
    Route::post('me', 'Api\AuthController@me');

});


Route::group([
	'middleware' => 'api',
], function ($router) {

	Route::get('merchants', 'Api\MerchantController@get_merchants');

	Route::get('member/upgrading-price', 'Api\AccountController@get_member_upgrading_price');
	Route::post('member/account/upgrade', 'Api\AccountController@upgrade_membership');

	Route::get('member/downlines/count', 'Api\AccountController@get_downlines_count');
	Route::get('member/downlines', 'Api\AccountController@get_member_downlines');
	Route::get('member/first-downlines', 'Api\AccountController@get_first_downlines');
	Route::get('member/bonus/logs', 'Api\AccountController@get_member_bonus_points_logs');
	Route::get('member/{user_id}/cashout/history', 'Api\AccountController@get_cashout_history');

	Route::get('admin/messages', 'Api\AdminMessageController@get_messages');
	Route::get('admin/wallets', 'Api\WalletController@get_admin_wallet');
	Route::get('membership/price', 'BlockChainApiController@get_membership_price');
	Route::get('monthly-maintenance/price', 'BlockChainApiController@get_maintenance_price');

	Route::post('member/email/validate/unique', 'Api\ValidatorController@validate_email');
	Route::post('member/alias/validate/unique', 'Api\ValidatorController@validate_alias_unique');
	Route::post('member/alias/validate/exists', 'Api\ValidatorController@validate_alias_exists');
	Route::post('member/alias/validate/exists2', 'Api\ValidatorController@validate_alias_exists2');
	Route::post('sponsor/alias/validate/exists', 'Api\ValidatorController@validate_sponsor_alias');
	Route::post('sponsor/alias/validate/exists2', 'Api\ValidatorController@validate_sponsor_alias2');
	Route::post('sponsor/alias/validate/exists3', 'Api\ValidatorController@validate_sponsor_alias3');
	Route::post('transaction-hash/validate/unique', 'Api\ValidatorController@validate_transaction_hash');

	Route::post('/membership/sign-up/submit', 'Api\SignUpController@submit_membership_details');

	Route::post('account/password/update', 'Api\AccountController@update_password');
	Route::post('account/details/update', 'Api\AccountController@update_details');

	Route::post('cashout/points', 'Api\CashoutController@cashout');
	Route::post('cashout/unilevel', 'Api\CashoutController@cashout_unilevel');

	Route::post('monthly-maintenance/pay', 'Api\MonthlyMaintenanceController@pay');
});