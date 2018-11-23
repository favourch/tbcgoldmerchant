@extends('layouts.member')
@section('content')

<div class="row">
	<div class="five wide column">
		@if($has_pending)

			<div class="ui raised padded red segment">
				<h3 class="ui centered header">Your payment is on process...</h3>
			</div>

		@else

			@if($is_maintenance_payment_time)
				<member-monthly-maintenance
					admin_btc_wallet="{{ $admin_btc_wallet }}" 
					:maintenance_cost="{{ $maintenance_cost }}"
					:btc_value="{{ $btc_value }}"
				>
				</member-monthly-maintenance>
			@else
				<div class="ui raised red padded segment">
					<p>Not yet meet the monthly maintenance payment time</p>
					<p><i>Note: Monthly maintenance payment time will display only if the current day is near on the first day of month (10 days or lower by 10 days before the first day of month) or on the day that your account should be deactivated for cashout.</i></p>
				</div>
			@endif

		@endif
		
	</div>
	<div class="sixteen wide column">
		<div class="ui divider"></div>
		<div class="">
			<h3>Monthly Maintenance History</h3>
			<table class="ui celled striped table">
				<thead>
					<tr>
						<th>Status</th>
						<th>Date</th>
						<th>Transaction Hash</th>
					</tr>
				</thead>
				<tbody>
					@if(count($maintenance_transactions) > 0)
						@foreach($maintenance_transactions as $maintenance_transaction)
						<tr>
							<td><label class="ui orange label">{{ strtoupper($maintenance_transaction->status) }}</label></td>
							<td>{{ date('F d, Y h:i:s a', strtotime($maintenance_transaction->created_at)) }}</td>
							<td style="width: 60%;">
								@if($maintenance_transaction->status !== 'confirmed')
								<form action="{{ route('member.monthly-maintenance.transaction-hash.update') }}" method="post">
									{{ csrf_field() }}
									<input type="hidden" name="maintenance_transaction_id" value="{{ $maintenance_transaction->id }}" />

									<div class="ui action fluid input">
										<input type="text" name="transaction_hash" value="{{ $maintenance_transaction->transaction_hash }}">
										<button class="ui positive button">UPDATE HASH</button>
									</div>
								</form>
								@else
									<strong>{{ $maintenance_transaction->transaction_hash }}</strong>
								@endif
							</td>
						</tr>
						@endforeach
					@else
					<tr>
						<td colspan="3">No transactions found...</td>
					</tr>
					@endif
				</tbody>
			</table>
		</div>
	</div>
</div>

@endsection