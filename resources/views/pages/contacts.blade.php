@extends('layouts.main')
@section('content')

  <div class="ui text masthead fluid">
    {{-- <img src="{{asset('images/contact-us2.jpg')}}" class="ui massive rounded bordered image" style="width: 100%;" /> --}}
  </div>
  
  <div class="ui grid container">
    <div class="row">
      {{-- <div class="sixteen wide column">
        <div class="ui divider"></div>
      </div> --}}
      <div class="ten wide column">
        <contact-us></contact-us>
      </div>
      <div class="six wide column">
        
          <img src="{{asset('images/contact-us3.jpg')}}" class="ui centered meduim rounded image" /> 
       
        
      </div>
      <div class="sixteen wide column">
        <br />
      </div>
    </div>
    
  </div>
  
</div>

@endsection