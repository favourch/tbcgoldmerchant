@extends('layouts.main')
@section('content')
	<div class="masthead"></div>
	<div class="ui grid">
		<div class="row">
			<div class="four wide column"></div>
			<div class="eight wide column">
				<h2 class="ui center aligned icon header">
					<i class="circular inverted users massive icon"></i>
					<font style="vertical-align: inherit;">
						<font style="vertical-align: inherit;"> 
							Become a Member Now! 
						</font>
					</font>
				</h2>
			</div>
		</div>
		@if(isset($has_two_recruit) && $has_two_recruit == true)
			<div class="row">
			<div class="three wide column"></div>
			<div class="ten wide column">
				<p>
					This sponsor has already two direct recruits, you cannot use this referral link anymore. Don't worry you can still register under to this sponsor by asking your sponsor to use his/her "Refer a Friend" functionality.
				</p>
			</div>
		</div>
		@else
			<div class="row">
				<div class="three wide column"></div>
				<div class="ten wide column">
					<member-auth-sign-up referral_link="{{ $referral_link }}" btc_value="{{ $btc_value }}" plan_price="{{ $plan_price }}" wallet_address="{{ $wallet_address }}" transaction_no="{{ $transaction_no }}"></member-auth-sign-up>
				</div>
			</div>
		@endif
	</div>
	<br />
@endsection