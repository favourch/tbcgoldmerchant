<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        @include('_partials.header')
    </head>
   	<body>
      
      @include('_partials.following-menu')
      @include('_partials.sidebar-menu')
      <!-- Page Contents -->
      <div class="pusher">
        <div id="app">
          @include('_partials.menu')
          <br />
          <div class="ui container">
    			  @yield('content')
          </div>
          
          @include('_partials.footer')
        </div>
      </div>
   		
      @include('_partials.scripts')
      
   	</body>
</html>