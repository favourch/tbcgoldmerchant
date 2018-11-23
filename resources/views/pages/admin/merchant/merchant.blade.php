@extends('layouts.admin')
@section('content')

<div class="row">
	<div class="sixteen wide column">
		<admin-merchant-merchant merchant_id="{{ $merchant_id }}"></admin-merchant-merchant>
	</div>
</div>

@endsection