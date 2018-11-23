<!-- Following Menu -->
<div class="ui large top fixed hidden menu">
	<div class="ui container">
		<a class="item @if(Route::currentRouteName() == 'home.view') active @endif" href="{{ route('home.view') }}">
			<i class="home icon"></i>
			Home
		</a>
		<a class="item @if(Route::currentRouteName() == 'about.view') active @endif" href="{{ route('about.view') }}">
			<i class="globe icon"></i>
			News / Updates
		</a>
		<a class="item @if(Route::currentRouteName() == 'contacts.view') active @endif" href="{{ route('contacts.view') }}">
			<i class="envelope outline icon"></i>
			Contact Us
		</a>
		<a class="item @if(Route::currentRouteName() == 'merchant.view') active @endif" href="{{ route('merchant.view') }}">
	      <i class="map icon"></i>
	      Merchant
	    </a>
		<div class="right menu">
	      <div class="item">
			<div class="ui buttons">
				<a class="ui red button" href="{{ route('sign-in.view') }}"><i class="users icon"></i> Members Area</a>
				<div class="or"></div>
				<a class="ui positive button" href="{{ route('sign-up-with-sponsor.view') }}"><i class="edit outline icon"></i> Sign Up</a>
			</div>
	      </div>
	    </div>
	</div>
</div>