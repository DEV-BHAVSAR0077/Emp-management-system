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
        <a href="{{ route('users.index') }}" class="nav-tab-btn {{ $empTabActive ? 'active' : '' }}" id="tab-btn-emp">
            <svg width="14" height="14" viewBox="0 0 20 20" fill="currentColor"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.660.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/></svg>
            Users
        </a>
        @endcan
        @can('view-role')
        <a href="{{ route('roles.index') }}" class="nav-tab-btn {{ $rolesTabActive ? 'active' : '' }}" id="tab-btn-roles">
            <svg width="14" height="14" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            Roles
        </a>
        @endcan
        @can('view-expense')
        <a href="{{ route('expenses.index') }}" class="nav-tab-btn {{ $expensesTabActive ? 'active' : '' }}" id="tab-btn-expenses">
            <svg width="14" height="14" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M1 4a1 1 0 011-1h16a1 1 0 011 1v8a1 1 0 01-1 1H2a1 1 0 01-1-1V4zm12 4a3 3 0 11-6 0 3 3 0 016 0zM4 9a1 1 0 100-2 1 1 0 000 2zm13-1a1 1 0 11-2 0 1 1 0 012 0z" clip-rule="evenodd"/><path d="M1.75 14.5a.75.75 0 000 1.5c4.417 0 8.693.603 12.749 1.73 1.111.309 2.251-.512 2.251-1.696v-.784a.75.75 0 00-1.5 0v.784a.272.272 0 01-.35.25A49.043 49.043 0 001.75 14.5z"/></svg>
            Expense
        </a>
        @endcan
        @can('view-category')
        <a href="{{ route('categories.index') }}" class="nav-tab-btn {{ $categoriesTabActive ?? false ? 'active' : '' }}" id="tab-btn-categories">
            <svg width="14" height="14" viewBox="0 0 20 20" fill="currentColor"><path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
            Categories
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