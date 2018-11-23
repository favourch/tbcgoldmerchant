@extends('layouts.admin')
@section('content')

<div class="row">
	<div class="sixteen wide column">
		<admin-membership-transaction-details :membership_transaction_id="{{ $membership_transaction_id }}"></admin-membership-transaction-details>
	</div>
</div>

@endsection