@extends('layouts.home-layout')
@section('content')
  <div class="ui inverted vertical masthead centered aligned segment" style="background: url({{ asset('images/bitcoin.jpeg')  }})">

    @include('_partials.home-menu')

    <div class="ui text container">
      <h1 class="ui inverted header" style="font-size: 3.7em;">
        <span style="color: gold; font-family:'Shrikhand', cursive;">TBC GOLD MERCHANT</span>
      </h1>
      <h2>
        
      </h2>
      {{-- <a class="ui huge red button" href="{{ route('sign-up-with-sponsor.view') }}">Sign Up Now !</a> --}}
    </div>

  </div>
  
    <div class="ui vertical stripe segment">
    <div class="ui middle aligned stackable grid container">
      <div class="row">
        <div class="eight wide column">
          <h3 class="ui header">
            <i class="users icon"></i> Membership Benefits 
          </h3>
          <p>
            Gain points by recruiting members, big income and special gifts are waiting to achieve when you have much recruits 
          </p>
          <p>
            Get the most out of your recruits with these wonderful <strong>bonuses rewards</strong>:
            <ul style="margin-left: 30px;">
              <li>Direct Referral Bonus</li>
              <li>Matching Bonus</li>
              <li>Unilevel Bonus</li>
              <li>Pool Mining</li>
              <li>Gift Cheque Bonus</li>
            </ul>
          </p>
          
        </div>
        <div class="six wide right floated column">

          <div class="ui move right reveal">
            <div class="visible content" style="height: 250px;">
              <img src="{{ asset('images/home-4.jpg') }}" class="ui large rounded image" style="height: 100%">
            </div>
            <div class="hidden content" style="height: 250px;">
              <img src="{{ asset('images/help-team.jpg') }}" class="ui large rounded image" style="height: 100%">
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>

  <div class="ui vertical stripe segment">
    <div class="ui middle aligned stackable grid container">
      <div class="row">
        <div class="sixteen wide column">
          <h3 class="ui header centered">Amazing Gifts for Everyone!</h3>

        </div>
        <div class="six wide centered floated column">
          <br />
          <includes-carousel height="250px"></includes-carousel>
        </div>
        <div class="sixteen wide centered floated column">
          <p style="text-align: center">Members can easily recieve gifts thru thier downlines. Everytime a member reached 5th direct recruits(5th, 10th, and so on..) he/she can get one of our best gift products.</p>
        </div>
      </div>
      <div class="row">
        <div class="center aligned column">
          <a class="ui huge positive button" href="{{ route('sign-in.view') }}"><i class="check icon"></i> Check Them Out</a>
        </div>
      </div>
    </div>
  </div>


 {{--  <div class="ui vertical stripe segment">
    <div class="ui text container">
      
      <h2 class="ui header"><i class="bullhorn icon"></i> News / Updates</h2>
      <h4 class="ui header">Did We Tell You About Our Bananas?</h4>
      <p>Yes I know you probably disregarded the earlier boasts as non-sequitur filler content, but its really true. It took years of gene splicing and combinatory DNA research, but our bananas can really dance.</p>
      <a class="ui tiny button">Read More...</a>
    </div>
  </div> --}}
  
</div>

@endsection