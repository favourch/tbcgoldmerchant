@extends('layouts.main')
@section('content')

  <div class="ui text masthead fluid">
    {{-- <img src="{{asset('images/news.jpg')}}" class="ui large rounded image" style="" /> --}}
  </div>

  <div class="ui grid container">
    <div class="row">
      <div class="sixteen wide column">
        <div class="ui raised segment">
        <member-merchants></member-merchants>  
      </div>
        <br /><br />
      </div>
      <div class="sixteen wide column">
        <div class="ui divider"></div>
        <h1 class="ui center aligned icon header">
          <i class="circular gift icon"></i>
          Gifts for Everyone!
        </h1>
        <div class="ui divider"></div>
      </div>
      <div class="sixteen wide column">
        
      
        <div class="ui special centered cards">
          
          <div class="card">
            <div class="blurring dimmable image">
              <div class="ui dimmer">
                <div class="content">
                  <div class="center">
                    <div class="ui inverted button">Groceries</div>
                  </div>
                </div>
              </div>
              <img src="{{ asset('images/grocery3.png') }}" class="ui rounded bordered image">
            </div>
          </div>

          <div class="card">
            <div class="blurring dimmable image">
              <div class="ui dimmer">
                <div class="content">
                  <div class="center">
                    <div class="ui inverted button">Gas</div>
                  </div>
                </div>
              </div>
              <img src="{{ asset('images/free-gas2.png') }}" class="ui rounded bordered image">
            </div>
          </div>

          <div class="card">
            <div class="blurring dimmable image">
              <div class="ui dimmer">
                <div class="content">
                  <div class="center">
                    <div class="ui inverted button">House & Lot</div>
                  </div>
                </div>
              </div>
              <img src="{{ asset('images/house-gift1.png') }}" class="ui rounded bordered image">
            </div>
          </div>

          <div class="card">
            <div class="blurring dimmable image">
              <div class="ui dimmer">
                <div class="content">
                  <div class="center">
                    <div class="ui inverted button">Car</div>
                  </div>
                </div>
              </div>
              <img src="{{ asset('images/car2.jpg') }}" class="ui rounded bordered image">
            </div>
          </div>

        </div>

      </div>
    </div>
  </div>
  <br /><br />
</div>

@endsection