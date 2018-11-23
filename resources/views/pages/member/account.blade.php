@extends('layouts.member')
@section('content')

<div class="row">
	<div class="sixteen wide column">
		<div class="ui mini breadcrumb">
			<a class="section">Dashboard</a>
			<i class="right chevron icon divider"></i>
			<div class="active section">Account Credentials</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="sixteen wide column">
		<member-account referral_link="{{ Auth::user()->referral_link }}"></member-account>
	</div>
</div>
@endsection