{{-- Dashoard Tab --}}

<div id="dashboard-tab" class="tab-content {{ $dashTabActive ? 'active' : '' }}">
    <div class="dash-card">
        <h1>Welcome, {{ $user->name }}! 👋</h1>
        <p>
            You are logged in as <strong>{{ $user->email }}</strong>.
            @if($user->isAdmin())
                You have <strong>Admin</strong> access — full control over all users and roles.
            @elseif($user->isHr())
                You have <strong>HR</strong> access — you can add, edit, and delete users.
            @else
                You can view all users and edit your own profile.
            @endif
        </p>
    </div>
</div>
