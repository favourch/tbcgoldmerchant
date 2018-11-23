<!-- Sidebar Menu -->
<div class="ui vertical inverted sidebar menu">
	<a class="item @if(Route::currentRouteName() == 'home.view') active @endif" href="{{ route('home.view') }}">Home</a>
	<a class="item @if(Route::currentRouteName() == 'about.view') active @endif" href="{{ route('about.view') }}">About</a>
	<a class="item @if(Route::currentRouteName() == 'contacts.view') active @endif" href="{{ route('contacts.view') }}">Contacts</a>
	<a class="item @if(Route::currentRouteName() == 'merchant.view') active @endif" href="{{ route('merchant.view') }}">Merchant</a>
	<a class="item @if(Route::currentRouteName() == 'sign-in.view') active @endif" href="{{ route('sign-in.view') }}">Member Area</a>
	<a class="item @if(Route::currentRouteName() == 'sign-up-with-sponsor.view') active @endif" href="{{ route('sign-up-with-sponsor.view') }}">Sign Up</a>
</div>