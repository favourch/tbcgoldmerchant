@extends('layouts.member')
@section('content')

<div class="row">
	<div class="sixteen wide column">
		<div class="ui mini breadcrumb">
			<a class="section">Dashboard</a>
			<i class="right chevron icon divider"></i>
			<div class="active section">Bonus Points Logs</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="sixteen wide column">
		<div class="ui segment">
			<h3>Bonus Points Logs</h3>
			<member-bonus-points-logs></member-bonus-points-logs>
		</div>
	</div>
</div>
@endsection