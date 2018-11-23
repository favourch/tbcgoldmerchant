@extends('layouts.admin')
@section('content')

<div class="row">
	<div class="sixteen wide column">
		<admin-cashout-transaction-matrix-2-details :cashout_transaction_id="{{ $cashout_transaction_id }}"></admin-cashout-transaction-matrix-2-details>
	</div>
</div>

@endsection