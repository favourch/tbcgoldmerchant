@extends('layouts.admin')
@section('content')

<div class="row">
	<div class="sixteen wide column">
		<admin-cashout-transaction-details :cashout_transaction_id="{{ $cashout_transaction_id }}"></admin-cashout-transaction-details>
	</div>
</div>

@endsection