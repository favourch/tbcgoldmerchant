<!doctype html>
<html lang="{{ app()->getLocale() }}">
  <head>
    @include('_partials.header')
  </head>
  <body>
    
    <!-- Page Contents -->
    <div class="pusher">
      <div id="app">
        @if(Auth::guard('admin')->check())
          @include('_partials.admin.menu')
        @endif
        <div class="ui vertical segment">
          <br /><br /><br />
          <div class="ui grid container stackable">
            @yield('content')
          </div>
        </div>
      </div>
    </div>
    <br /><br /><br /><br /><br />
    @include('_partials.scripts')
    
  </body>
</html>