@extends('layouts.member')
@section('content')

<div class="row">
  <div class="sixteen wide column">
  	@if(Auth::user()->level_two_user)
	   	<member-genology2 parent_referral_link="{{ Auth::user()->level_two_user->level_two_user_referral_link }}"></member-genology2>
	@endif
  </div>
</div>

@endsection