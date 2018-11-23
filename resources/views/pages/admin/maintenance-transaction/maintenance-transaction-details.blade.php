@extends('layouts.admin')
@section('content')

<div class="row">
	<div class="sixteen wide column">
		<admin-maintenance-transaction-details :maintenance_transaction_id="{{ $maintenance_transaction_id }}"></admin-maintenance-transaction-details>
	</div>
</div>

@endsection