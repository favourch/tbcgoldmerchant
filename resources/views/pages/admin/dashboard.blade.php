@extends('layouts.admin')
@section('content')

<div class="row">
	<div class="sixteen wide column">
		@if(session()->has('greetings'))
		<div class="ui icon message">
			<i class="inbox icon"></i>
			<div class="content">
				<div class="header">
					{{ session()->get('greetings') }}
				</div>
				<p>Get the best news in your e-mail every day.</p>
			</div>
		</div>
		@endif
		<p>Dashboard content is in progress.</p>
	</div>
</div>

@endsection