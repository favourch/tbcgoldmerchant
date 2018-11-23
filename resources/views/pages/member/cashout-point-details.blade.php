@extends('layouts.member')
@section('content')

<div class="row">
	<div class="sixteen wide column">
		<member-cashout-point-details :cashout_transaction_id="{{ $cashout_transaction_id }}"></member-cashout-point-details>
	</div>
</div>

@endsection