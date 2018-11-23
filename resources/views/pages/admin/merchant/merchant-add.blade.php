@extends('layouts.admin')
@section('content')

<div class="row">
	<div class="sixteen wide column">
		<div class="ui grid container">
			<div class="row">
				<div class="sixteen wide right aligned column">
					<a href="{{ route('admin.merchants.view') }}" class="ui button">
						<i class="arrow left icon"></i> Merchant Listing
					</a>
				</div>
			</div>
		</div>
		<admin-merchant-merchant-add></admin-merchant-merchant-add>
	</div>
</div>

@endsection