<div class="ui top fixed menu">
  <div class="item">
    <i class="gg circle icon big red"></i>
  </div>
  <div class="right menu">
    <a class="item @if(Route::currentRouteName() == 'admin.dashboard.view') active @endif" href="{{ route('admin.dashboard.view') }}">
      <i class="th icon"></i>
      Dashboard
    </a>
    <a class="item @if(Route::currentRouteName() == 'admin.members.view') active @endif" href="{{ route('admin.members.view') }}">
      <i class="users icon"></i>
      Members
    </a>
    <a class="item @if(Route::currentRouteName() == 'admin.member.bonus-logs.view') active @endif" href="{{ route('admin.member.bonus-logs.view') }}">
      <i class="history icon"></i>
      Bonus Logs
    </a>
    <div class="ui dropdown item ">
      <i class="clipboard list icon"></i> Transactions <i class="dropdown icon"></i>
      <div class="menu">
        <a class="item @if(Route::currentRouteName() == 'admin.transactions.cashout.view') active @endif" href="{{ route('admin.transactions.cashout.view') }}">
          <i class="exchange icon"></i> Cash Out
        </a>
        <a class="item @if(Route::currentRouteName() == 'admin.transactions.cashout-matrix-2.view') active @endif" href="{{ route('admin.transactions.cashout-matrix-2.view') }}">
          <i class="exchange icon"></i> Cash Out - Matrix 2
        </a>
        <a class="item @if(Route::currentRouteName() == 'admin.transactions.membership.view') active @endif" href="{{ route('admin.transactions.membership.view') }}">
          <i class="street view icon"></i> Membership
        </a>
        <a class="item @if(Route::currentRouteName() == 'admin.transactions.maintenance.view') active @endif" href="{{ route('admin.transactions.maintenance.view') }}">
          <i class="calendar outline alternate icon"></i> Maintenance
        </a>
        <a class="item @if(Route::currentRouteName() == 'admin.transactions.unilevel.view') active @endif" href="{{ route('admin.transactions.unilevel.view') }}">
          <i class="sitemap icon"></i> Unilevel
        </a>
      </div>
    </div>
    <a class="item @if(Route::currentRouteName() == 'admin.merchants.view') active @endif" href="{{ route('admin.merchants.view') }}">
      <i class="map icon"></i>
      Merchants
    </a>
    @if(Auth::guard('admin')->check())
    <div class="ui dropdown item ">
      <i class="user circle outline icon"></i> {{ Auth::guard('admin')->user()->fullname }} <i class="dropdown icon"></i>
      <div class="menu">
        <a class="item @if(Route::currentRouteName() == 'admin.account.view') active @endif" href="{{ route('admin.account.view') }}">
          <i class="lock icon"></i> My Account
        </a>
        <a class="item @if(Route::currentRouteName() == 'admin.messages.view') active @endif" href="{{ route('admin.messages.view') }}">
          <i class="comments outline alternate icon"></i> Messages
        </a>
        <a class="item @if(Route::currentRouteName() == 'admin.users.view') active @endif" href="{{ route('admin.users.view') }}">
          <i class="users icon"></i> Users
        </a>
        <a class="item" href="{{ route('admin.sign-out') }}">
          <i class="power off icon"></i>
          Logout
        </a>
      </div>
    </div>
    @endif
  </div>
</div>