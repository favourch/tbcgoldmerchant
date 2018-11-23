<!doctype html>
<html lang="{{ app()->getLocale() }}">
  <head>
    @include('_partials.header')
  </head>
  <body>
    
    <!-- Page Contents -->
    <div class="pusher">
      <div id="app">
        @include('_partials.member.menu')
        <div class="ui vertical segment">
          <br /><br /><br />
          <div class="ui grid container stackable">
            @yield('content')
          </div>
        </div>
      </div>
    </div>
    
    @include('_partials.scripts')
    
  </body>
</html>