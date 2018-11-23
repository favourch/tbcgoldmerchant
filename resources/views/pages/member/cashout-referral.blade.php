@extends('layouts.member')
@section('content')

<div class="row">
	{{-- <div class="sixteen wide column">
		<div class="ui top attached tabular menu">
			<a class="item active" data-tab="first">Cash Out</a>
			<a class="item" data-tab="second">Cash Out History</a>
		</div>
		<div class="ui bottom attached tab segment active" data-tab="first">
			@if($count_cash_out_today > 0)
				<p>You have already cash out for today. Come back here on next friday. Thank you.</p>
			@else
				<member-exchange-points min_exchange_points="{{$min_exchange_points}}" max_exchange_points="{{$max_exchange_points}}"></member-exchange-points>

			@endif
		</div>
		<div class="ui bottom attached tab segment" data-tab="second">
			<member-cashout-history user_id="{{ Auth::user()->id }}" limit="null"></member-cashout-history>
		</div>
	</div> --}}


	<div class="sixteen wide column">
		<div class="ui pointing secondary menu">
			<a class="item active" data-tab="first">First</a>
			<a class="item" data-tab="second">Second</a>
			<a class="item" data-tab="third">Third</a>
		</div>
	</div>
</div>

@endsection