@extends('layouts.member')
@section('content')

<div class="row">
	<div class="sixteen wide column">
		<member-downline-add btc_value="{{ $btc_value }}" plan_price="{{ $plan_price }}" wallet_address="{{ $wallet_address }}" transaction_no="{{ $transaction_no }}" direct_sponsor="{{ Auth::user()->alias_name }}"></member-downline-add>
	</div>
</div>

@endsection