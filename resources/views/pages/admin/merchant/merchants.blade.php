@extends('layouts.admin')
@section('content')

<div class="row">
	<div class="sixteen wide column">
		<div class="ui grid container">
			<div class="row">
				<div class="sixteen wide right aligned column">
					<a href="{{ route('admin.merchant.add.view') }}" class="ui positive button">Add Merchant</a>
				</div>
			</div>
		</div>
		<admin-merchant-merchants></admin-merchant-merchants>
	</div>
</div>

@endsection