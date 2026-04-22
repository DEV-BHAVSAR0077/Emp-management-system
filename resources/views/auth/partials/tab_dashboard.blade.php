{{-- Dashoard Tab --}}

<div id="dashboard-tab" class="tab-content {{ $dashTabActive ? 'active' : '' }}">
    <div class="dash-card">
        <h1>Welcome, {{ $user->name }}! 👋</h1>
        <p>
            You are logged in as <strong>{{ $user->email }}</strong>.
            Your current assigned role is <strong>{{ $user->role }}</strong>. You can navigate the tabs above based on your granted permissions.
        </p>
    </div>
</div>
