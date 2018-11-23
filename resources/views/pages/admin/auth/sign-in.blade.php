@extends('layouts.admin')
@section('content')
	<div class="ui grid container">
		<div class="row">
			<div class="four wide column"></div>
			<div class="eight wide column">
				
				<admin-auth-sign-in></admin-auth-sign-in>

			</div>
		</div>
	</div>
@endsection