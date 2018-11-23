<div class="ui top fixed menu">
  <div class="item">
    <i class="gg circle icon big red"></i>
  </div>
  <div class="right menu">
    
    @if(Auth::check() && Auth::user()->active == 1)
    <a class="item @if(Route::currentRouteName() == 'member.dashboard.view') active @endif" href="{{ route('member.dashboard.view') }}">
      <i class="th icon"></i>
      Dashboard
    </a>
    <a class="item @if(Route::currentRouteName() == 'member.merchant.view') active @endif" href="{{ route('member.merchant.view') }}">
      <i class="map icon"></i>
      Merchant
    </a>
    <a class="item @if(Route::currentRouteName() == 'member.genology.view') active @endif" href="{{ route('member.genology.view') }}">
      <i class="sitemap icon"></i>
      Matrix 1 - Genealogy
    </a>
    
    @if(!is_null(Auth::user()->level_two_user))
    <a class="item @if(Route::currentRouteName() == 'member.genology2.view') active @endif" href="{{ route('member.genology2.view') }}">
      <i class="sitemap icon"></i>
      Matrix 2 - Genealogy
    </a>
    @endif
  {{--   <a class="item @if(Route::currentRouteName() == 'member.ways-to-earn.view') active @endif" href="{{ route('member.ways-to-earn.view') }}">
      <i class="question circle outline icon"></i>
      Ways to Earn
    </a> --}}
    
    <div class="ui dropdown item ">
      <i class="user circle outline icon"></i> {{ Auth::user()->detail->fullname }} <i class="dropdown icon"></i>
      <div class="menu">
        <a class="item @if(Route::currentRouteName() == 'member.account.view') active @endif" href="{{ route('member.account.view') }}">
          <i class="lock icon"></i> Account Credentials
        </a>
        <a class="item @if(Route::currentRouteName() == 'member.bonus-points-logs.view') active @endif" href="{{ route('member.bonus-points-logs.view') }}">
          <i class="gift icon"></i> Bonus Points Logs
        </a>
        <a class="item @if(Route::currentRouteName() == 'member.cashout.view') active @endif" href="{{ route('member.cashout.view') }}">
          <i class="money bill alternate outline icon"></i> Cash Out
        </a>
        <a class="item @if(Route::currentRouteName() == 'member.monthly-maintenance.view') active @endif" href="{{ route('member.monthly-maintenance.view') }}">
          <i class="calendar outline icon"></i> Monthly Maintenance
        </a>
        <a class="item @if(Route::currentRouteName() == 'member.downline.add.view') active @endif" href="{{ route('member.downline.add.view') }}">
          <i class="linkify icon"></i> Refer A Friend
        </a>
        <a class="item" href="{{ route('sign-out') }}">
          <i class="power off icon"></i>
          Logout
        </a>
      </div>
    </div>
    @else
    <a class="item" href="{{ route('sign-out') }}">
      <i class="power off icon"></i>
      Logout
    </a>
    @endif
  </div>
</div>