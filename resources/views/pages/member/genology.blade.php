@extends('layouts.member')
@section('content')

<div class="row">
  <div class="sixteen wide column">

    {{-- <p>Monolines content is in progress.</p> --}}
   	<member-genology parent_referral_link="{{ Auth::user()->referral_link }}"></member-genology>

  </div>
</div>

@endsection