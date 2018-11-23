@extends('layouts.admin')
@section('content')

<div class="row">
	<div class="sixteen wide column">
		<admin-unilevel-transaction-details :unilevel_transaction_id="{{ $unilevel_transaction_id }}"></admin-unilevel-transaction-details>
	</div>
</div>

@endsection