@extends('layouts.member')
@section('content')

<div class="row">
	<div class="two wide column"></div>
	<div class="twelve wide column">
		<div class="ui pointing secondary menu">
			<div class="item active" data-tab="cashout-history">Cash Out History</div>
			<div class="item" data-tab="cashout-points">Cash Out</div>
			<div class="item" data-tab="cashout-points-matrix-2">Cash Out - Matrix 2</div>
			<div class="item" data-tab="cashout-unilevel">
				Unilevel Cash Out
			</div>
		</div>
		
			@if(Auth::check() && Auth::user()->active)
				<div class="ui segment">
					
					<div class="ui tab active" data-tab="cashout-history">
						<member-cashout-history 
							user_id="{{ Auth::user()->id }}"
						>
						</member-cashout-history>
					</div>
					<div class="ui tab" data-tab="cashout-points">
						
						@if($count_cashout > 0)
							<div class="ui message">
								<div class="header">
									Maxed Cash Out
								</div>
								<p>You have already request a cash out for today. Come back here on next Friday. Thank you.</p>
							</div>
						@else

							@if(Auth::check() && Auth::user()->user_status == 'activated')

								<member-cashout-points 
									:user_id="{{ Auth::user()->id }}" 
									:min_cashout_points="{{$min_cashout_points}}" 
									:max_cashout_points="{{$max_cashout_points}}"
									:min_cashout_referral_points="{{$min_cashout_referral_points}}"
									:min_cashout_referral_points="{{$min_cashout_referral_points}}"
								>
								</member-cashout-points>
							
							@else

								<div class="ui red padded segment">
									<h4>
									<i class="exclamation red circular icon"></i> Account Deactivated
									</h4>
									<p>
										Sorry you cannot request for cash out. Please pay the monthly maintenance to request for cashout in <a href="{{ route('member.monthly-maintenance.view') }}">here</a>.
									</p>
								</div>

							@endif
							
						@endif

					</div>

					<div class="ui tab" data-tab="cashout-points-matrix-2">
						
						@if($count_cashout2 > 0)
							<div class="ui message">
								<div class="header">
									Maxed Cash Out
								</div>
								<p>You have already request a cash out for today. Come back here on next Friday. Thank you.</p>
							</div>
						@else

							@if(Auth::check() && Auth::user()->user_status == 'activated')

								<member-cashout-points-matrix-2 
									:user_id="{{ Auth::user()->id }}" 
									:min_cashout_points="{{$min_cashout_points}}" 
									:max_cashout_points="{{$max_cashout_points}}"
									:min_cashout_referral_points="{{$min_cashout_referral_points}}"
									:min_cashout_referral_points="{{$min_cashout_referral_points}}"
								>
								</member-cashout-points-matrix-2>
							
							@else

								<div class="ui red padded segment">
									<h4>
									<i class="exclamation red circular icon"></i> Account Deactivated
									</h4>
									<p>
										Sorry you cannot request for cash out. Please pay the monthly maintenance to request for cashout in <a href="{{ route('member.monthly-maintenance.view') }}">here</a>.
									</p>
								</div>

							@endif
							
						@endif

					</div>

					<div class="ui tab" data-tab="cashout-unilevel">
						@if($count_unilevel_cashout > 0)
							<div class="ui message">
								<div class="header">
									Maxed Cash Out
								</div>
								<p>You have already request a cash out for today. Come back here on next Friday. Thank you.</p>
							</div>
						@else
						@if(date('d') == '05')
							<member-cashout-unilevel :user_id="{{ Auth::user()->id }}"></member-cashout-unilevel>
						@else
							Sorry not a cashout day for unilevel bonus. You can cashout here on every 5th day of the month thanks.
						@endif
						@endif
					</div>
					
				</div>
			@else
				<div class="ui red padded segment">
					<h4>
						<i class="exclamation red circular icon"></i> Account Deactivated
					</h4>
					<p>
						Sorry you cannot request for cash out. Your account has been deactivated due to any reasons. Please contact our Admin.
					</p>
				</div>
			@endif
	</div>
</div>

@endsection