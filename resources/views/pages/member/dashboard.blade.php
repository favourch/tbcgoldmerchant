@extends('layouts.member')
@section('content')

<div class="row">
	
	@if(Auth::user()->active == 1)
		<div class="sixteen wide column">
			<h3>Level 1</h3>
		</div>
		<div class="four wide column">
			<div class="ui red inverted padded center aligned segment">
				
				<h4>Referral: {{ number_format(Auth::user()->referral_points, 0, '.', ',') }}pts</h4>
			</div>
		</div>
		<div class="four wide column">
			<div class="ui red inverted padded center aligned segment">
				<h4>Pairing (Left): {{ number_format(Auth::user()->left_points, 0, '.', ',') }}pts</h4>
			</div>
		</div>

		<div class="four wide column">
			<div class="ui red inverted padded center aligned segment">
				<h4>Pairing (Right): {{ number_format(Auth::user()->right_points, 0, '.', ',') }}pts</h4>
			</div>
		</div>
		<div class="four wide column">
			<div class="ui blue inverted padded center aligned segment">
				<h4>Unilevel Bonus: ${{ number_format(Auth::user()->unilevel_bonus, 2, '.', ',') }}</h4>
			</div>
		</div>
		
		

		@if(Auth::user()->commission_balance > 0)
			<div class="eight wide column">
				<br />
				<div class="ui black inverted padded center aligned segment">		
					<h4>CD Balance: ${{ number_format(Auth::user()->commission_balance, 0, '.', ',') }}</h4>
				</div>
			</div>
		@endif

		@if(Auth::user()->upgrading_total > 0)
			<div class="eight wide column">
				<br />
				
				<div class="ui black inverted padded center aligned segment">		
					<h4>Upgrading Total Deducted: ${{ number_format(Auth::user()->upgrading_total, 0, '.', ',') }}</h4>
					<h4>Upgrading Balance: ${{ number_format(160 - Auth::user()->upgrading_total, 0, '.', ',') }}</h4>
				</div>
			</div>
		@endif
		
		

		@if(!is_null(Auth::user()->level_two_user))
			<div class="sixteen wide column">
				<div class="ui divider"></div>
				<h3>Level 2</h3>
			</div>

			<div class="four wide column">
				<div class="ui red inverted padded center aligned segment">
					
					<h4>Referral: {{ number_format(Auth::user()->level_two_user->level_two_user_referral_points, 0, '.', ',') }}pts</h4>
				</div>
			</div>
			<div class="four wide column">
				<div class="ui red inverted padded center aligned segment">
					<h4>Pairing (Left): {{ number_format(Auth::user()->level_two_user->level_two_user_left_points, 0, '.', ',') }}pts</h4>
				</div>
			</div>

			<div class="four wide column">
				<div class="ui red inverted padded center aligned segment">
					<h4>Pairing (Right): {{ number_format(Auth::user()->level_two_user->level_two_user_right_points, 0, '.', ',') }}pts</h4>
				</div>
			</div>
			<div class="four wide column">
				<div class="ui blue inverted padded center aligned segment">
					<h4>Unilevel Bonus: ${{ number_format(Auth::user()->level_two_user->level_two_user_unilevel_bonus, 2, '.', ',') }}</h4>
				</div>
			</div>
		@endif

		<div class="sixteen wide column">
			<div class="ui divider"></div>
		</div>

		<div class="six wide column">
			
			@if(Auth::user()->user_status == 'activated')
				
				@if($has_pending)

					<div class="ui raised padded red segment">
						<h3 class="ui centered header">Your payment is on process...</h3>
					</div>

				@else
					
					<includes-countdown-timer
						starttime="{{$activated_start_time}}"
						endtime="{{$activated_end_time}}"
						trans='{
							"day":"Day",
							"hours":"Hours",
							"minutes":"Minutes",
							"seconds":"Seconds",
							"expired":"Event has been expired.",
							"running":"Till the end of event.",
							"upcoming":"Till start of event.",
							"status": {
							"expired":"Expired",
							"running":"Running",
							"upcoming":"Future"
						}}'
					></includes-countdown-timer>
					
					@if($is_maintenance_payment_time)

						<a href="{{ route('member.monthly-maintenance.view') }}" class="ui green big center aligned fluid button">
							Pay Monthly Maintenance
						</a>

					@endif

				@endif
				

			@elseif(Auth::user()->user_status == 'deactivated')
				
				<div class="ui raised padded red segment">
					@if($has_pending)
						
						<h3 class="ui centered header">Your payment is on process...</h3>

					@else

						<h1 class="ui centered header"><i class="hourglass end massive red icon"></i></h1>
						<p>Please pay the monthly maintenance in order for your account to cashout.</p>
						<a href="{{ route('member.monthly-maintenance.view') }}" class="ui green big center aligned fluid button">
							Pay Monthly Maintenance
						</a>
					
					@endif
				</div>
				
			@endif
			
			@if(!Auth::user()->level_two_user)

				@if($has_level_two_pending == 0)
					<member-membership-upgrade 
						:plan1="{{ $plan1 }}" 
						:plan2="{{ $plan2 }}" 
						:plan3="{{ $plan3 }}" 
						:plan1_btc="{{ $plan1_btc }}" 
						:plan2_btc="{{ $plan2_btc }}" 
						:plan3_btc="{{ $plan3_btc }}" 
						:user_id="{{ Auth::user()->id }}" 
						:upgrading_deduction="{{ Auth::user()->upgrading_total }}" 
						:total_value_to_send_btc="{{ $total_value_to_send_btc }}" 
						:total_upgrade_cost="{{ $total_upgrade_cost }}" 
						:has_level_two_pending="{{ $has_level_two_pending }}"
					></member-membership-upgrade>
				@else
					<button class="ui pink huge fluid button">

						<i class="notched circle loading icon"></i> Upgrading is on process...
			
					</button>
				@endif

			@endif
		</div>

	<div class="ten wide column">

		<includes-messages user_id={{Auth::user()->id}}></includes-messages>

		
	</div>

	
	{{-- <div class="sixteen wide column">
		<div class="ui divider"></div>
		<member-cashout-history user_id="{{ Auth::user()->id }}" limit="5"></member-cashout-history>
	</div> --}}
	
	<div class="sixteen wide column">
		<includes-tbc-rates></includes-tbc-rates>
	</div>
	
	{{-- <div class="sixteen wide column">
		<br />
		<includes-btc-exchange-rates></includes-btc-exchange-rates>
	</div> --}}

	@else

	
	<includes-messages user_id={{Auth::user()->id}}></includes-messages>
	


		@if($transaction != null)
		<div class="ten wide column">
			<br />
			<div class="ui segment">
				
				@if(session()->has('status') && session()->get('status') == 'ok')
					<div class="ui warning message">
						<i class="close icon"></i>
						<div class="header">
							Success!
						</div>
						Transaction hash have been updated.
					</div>
				@endif
				@if(session()->has('status') && session()->get('status') == 'fail')
					<div class="ui warning message">
						<i class="close icon"></i>
						<div class="header">
							Fail!
						</div>
						Transaction hash failed to update.
					</div>
				@endif

				<form class="ui form" action="{{ route('member.transaction-hash.update', ['user_id' => Auth::user()->id]) }}" method="post">
					{{ csrf_field() }}
					
					<div class="field">
						<h3>Your Membership Status: {{ ucfirst($transaction->status) }}</h3>
						<p>The Admin is now working on it, please wait for the confirmation though this may take a while.</p>
						<hr>
						<h4>Membership Plan Details</h4>
						<table class="ui table">
							<tbody>
								<tr>
									<td style="width: 30%">Created Date:</td>
									<td>{{ date('F d, Y', strtotime($transaction->created_at)) }}</td>
								</tr>
								<tr>
									<td>Membership Cost:</td>
									<td>${{ number_format($transaction->plan_cost, 2, '.', ',') }}</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="field">
						<label>Transaction Hash<small>(You may change your transaction hash if you have entered a wrong hash)</small></label>
						<input type="text" name="transaction_hash" value="{{ old('transaction_hash', $transaction->transaction_hash) }}" />
						@if($errors->has('transaction_hash'))
							<p>{{ $errors->first('transaction_hash') }}</p>
						@endif
					</div>
					<div class="field">
						<button class="ui positive button">Update Transaction Hash</button>
					</div>
				</form>
			</div>
		</div>
		@endif
	@endif
</div>

@endsection