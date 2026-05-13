<nav class="navbar">
    <div class="nav-left">
        <a href="{{ route('dashboard') }}" class="brand">{{ config('app.name', 'MyApp') }}</a>
    </div>

    <div class="tabs">
        <a href="{{ route('dashboard') }}" class="nav-tab-btn {{ $dashTabActive ? 'active' : '' }}" id="tab-btn-dashboard">
            <svg width="14" height="14" viewBox="0 0 20 20" fill="currentColor"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>
            Dashboard
        </a>
        @can('view-user')
        <a href="{{ route('dashboard', ['tab' => 'emp']) }}" class="nav-tab-btn {{ $empTabActive ? 'active' : '' }}" id="tab-btn-emp">
            <svg width="14" height="14" viewBox="0 0 20 20" fill="currentColor"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.660.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/></svg>
            Users
        </a>
        @endcan
        @can('view-role')
        <a href="{{ route('dashboard', ['tab' => 'roles']) }}" class="nav-tab-btn {{ $rolesTabActive ? 'active' : '' }}" id="tab-btn-roles">
            <svg width="14" height="14" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            Roles
        </a>
        @endcan
    </div>

    <div class="nav-right">
        <span class="nav-user">
            {{ $user->email }}
            <span class="role-pill" style="background:{{ $myRoleColor }}20; color:{{ $myRoleColor }}; border:1px solid {{ $myRoleColor }}40;">
                {{ $user->role }}
            </span>
        </span>
        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
            @csrf
            <button type="submit" id="btn-logout" class="btn-logout">Sign Out</button>
        </form>
    </div>
</nav>