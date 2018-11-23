@extends('layouts.admin')
@section('content')

<div class="row">
	<div class="sixteen wide column">
		<admin-member-edit 
			:user_id="{{$user_id}}"
			:plan1="{{ $plan1 }}" 
			:plan2="{{ $plan2 }}" 
			:plan3="{{ $plan3 }}" 
			:plan1_btc="{{ $plan1_btc }}" 
			:plan2_btc="{{ $plan2_btc }}" 
			:plan3_btc="{{ $plan3_btc }}" 
			:upgrading_deduction="{{ $member->upgrading_total }}" 
			:total_value_to_send_btc="{{ $total_value_to_send_btc }}" 
			:total_upgrade_cost="{{ $total_upgrade_cost }}" 
			:has_level_two_pending="{{ $has_level_two_pending }}"
			:has_level_two_confirmed="{{ $has_level_two_confirmed }}"
		></admin-member-edit>
	</div>
</div>

@endsection