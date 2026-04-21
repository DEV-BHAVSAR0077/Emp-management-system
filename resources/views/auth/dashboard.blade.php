<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard — {{ config('app.name') }}</title>
    <meta name="description" content="Manage all registered users and roles from your dashboard." />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet" />
    <style>
        /* ── Reset & Base ─────────────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:            #f4f5f7;
            --card-bg:       #ffffff;
            --border:        #dde1e7;
            --text:          #1a1d23;
            --text-muted:    #6b7280;
            --btn-bg:        #374151;
            --btn-hover:     #1f2937;
            --btn-text:      #ffffff;
            --radius:        8px;
            --shadow:        0 1px 4px rgba(0,0,0,.08), 0 4px 16px rgba(0,0,0,.06);
            --success:       #15803d;
            --success-bg:    #f0fdf4;
            --danger:        #b91c1c;
            --danger-bg:     #fef2f2;
            --danger-border: #fecaca;
            --info:          #1d4ed8;
            --info-bg:       #eff6ff;
            --warn:          #92400e;
            --warn-bg:       #fffbeb;
            --warn-border:   #fde68a;
            --hr-color:      #6d28d9;
            --hr-bg:         #f5f3ff;
            --admin-color:   #b45309;
            --admin-bg:      #fef3c7;
        }

        html, body {
            height: 100%;
            font-family: 'Inter', system-ui, sans-serif;
            background: var(--bg);
            color: var(--text);
            font-size: 15px;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        /* ── Navbar ───────────────────────────────────────────────── */
        .navbar {
            background: var(--card-bg);
            border-bottom: 1px solid var(--border);
            padding: 0 2rem;
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: stretch;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .navbar .nav-left  { display: flex; align-items: center; justify-content: flex-start; }
        .navbar .brand     { font-size: 1.05rem; font-weight: 600; color: var(--text); text-decoration: none; padding: 1rem 0; }
        .navbar .nav-right { display: flex; align-items: center; justify-content: flex-end; gap: 1rem; }
        .navbar .nav-user  { font-size: .84rem; color: var(--text-muted); display: flex; align-items: center; gap: .45rem; }

        .btn-logout {
            padding: .45rem .95rem;
            font-size: .84rem; font-family: inherit; font-weight: 500;
            background: var(--btn-bg); color: var(--btn-text);
            border: none; border-radius: 6px; cursor: pointer;
            transition: background .18s;
        }
        .btn-logout:hover { background: var(--btn-hover); }

        /* ── Nav Tabs ─────────────────────────────────────────────── */
        .tabs { display: flex; gap: 1.5rem; align-items: stretch; }
        .nav-tab-btn {
            padding: 0 0.25rem;
            background: none; border: none;
            font-size: 0.95rem; font-weight: 500;
            color: var(--text-muted); cursor: pointer;
            border-bottom: 2px solid transparent;
            margin-bottom: -1px; font-family: inherit;
            transition: color 0.15s, border-color 0.15s;
            outline: none; display: flex; align-items: center; gap: .35rem;
        }
        .nav-tab-btn:hover { color: var(--text); border-bottom-color: #d1d5db; }
        .nav-tab-btn.active { color: #2563eb; border-bottom-color: #2563eb; }

        /* ── Tab Content ──────────────────────────────────────────── */
        .tab-content { display: none; }
        .tab-content.active { display: block; animation: fade-in 0.2s ease; }
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(4px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── Main wrapper ─────────────────────────────────────────── */
        .main { max-width: 1100px; margin: 2.5rem auto; padding: 0 1.5rem; }

        /* ── Welcome card ─────────────────────────────────────────── */
        .dash-card {
            background: var(--card-bg); border: 1px solid var(--border);
            border-radius: var(--radius); box-shadow: var(--shadow);
            padding: 1.75rem 2.25rem; margin-bottom: 1.75rem;
        }
        .dash-card h1 { font-size: 1.15rem; font-weight: 600; margin-bottom: .3rem; }
        .dash-card p  { color: var(--text-muted); font-size: .88rem; }

        /* ── Alerts ───────────────────────────────────────────────── */
        .alert {
            padding: .75rem 1rem; border-radius: var(--radius);
            font-size: .85rem; margin-bottom: 1.4rem;
            border: 1px solid transparent;
            display: flex; align-items: flex-start; gap: .5rem;
        }
        .alert-success { background: var(--success-bg); color: var(--success); border-color: #bbf7d0; }
        .alert-error   { background: var(--danger-bg);  color: var(--danger);  border-color: var(--danger-border); }
        .alert-warn    { background: var(--warn-bg);     color: var(--warn);    border-color: var(--warn-border); }

        /* ── Buttons ──────────────────────────────────────────────── */
        .btn {
            display: inline-flex; align-items: center; gap: .35rem;
            padding: .42rem .9rem; font-size: .84rem; font-family: inherit;
            font-weight: 500; border: 1px solid transparent; border-radius: 6px;
            cursor: pointer; text-decoration: none;
            transition: background .18s, border-color .18s, color .18s;
            white-space: nowrap;
        }
        .btn-primary { background: var(--btn-bg); color: var(--btn-text); border-color: var(--btn-bg); }
        .btn-primary:hover { background: var(--btn-hover); border-color: var(--btn-hover); }
        .btn-ghost { background: transparent; color: var(--text-muted); border-color: var(--border); }
        .btn-ghost:hover { background: var(--bg); color: var(--text); }
        .btn-sm { padding: .3rem .65rem; font-size: .79rem; }
        .btn-edit  { background: var(--info-bg);   color: var(--info);   border-color: #bfdbfe; }
        .btn-edit:hover  { background: #dbeafe; }
        .btn-danger { background: var(--danger-bg); color: var(--danger); border-color: var(--danger-border); }
        .btn-danger:hover { background: #fee2e2; }

        /* ── Panel (Employees tab) ────────────────────────────────── */
        .panel { background: var(--card-bg); border: 1px solid var(--border); border-radius: var(--radius); box-shadow: var(--shadow); }
        .panel-header {
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: .75rem; padding: 1.25rem 1.75rem;
            border-bottom: 1px solid var(--border);
        }
        .panel-header h2 { font-size: 1rem; font-weight: 600; }
        .panel-actions   { display: flex; align-items: center; gap: .65rem; flex-wrap: wrap; }

        .search-form  { display: flex; align-items: center; gap: .4rem; }
        .search-input {
            padding: .42rem .75rem; font-size: .84rem; font-family: inherit;
            border: 1px solid var(--border); border-radius: 6px;
            background: var(--bg); color: var(--text); outline: none;
            width: 220px; transition: border-color .18s;
        }
        .search-input:focus { border-color: #9ca3af; }

        /* ── Table ────────────────────────────────────────────────── */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: .875rem; }
        thead th {
            text-align: left; padding: .75rem 1.25rem;
            font-weight: 500; font-size: .78rem; text-transform: uppercase;
            letter-spacing: .04em; color: var(--text-muted);
            border-bottom: 1px solid var(--border); background: var(--bg);
            white-space: nowrap;
        }
        tbody tr { border-bottom: 1px solid var(--border); transition: background .12s; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: #f9fafb; }
        tbody td { padding: .85rem 1.25rem; color: var(--text); vertical-align: middle; }

        /* ── Badges ───────────────────────────────────────────────── */
        .badge        { display: inline-block; padding: .18rem .55rem; font-size: .74rem; font-weight: 500; border-radius: 99px; background: #f3f4f6; color: var(--text-muted); }
        .badge-you    { background: var(--info-bg);   color: var(--info);         }
        .badge-admin  { background: var(--admin-bg);  color: var(--admin-color);  }
        .badge-hr     { background: var(--hr-bg);     color: var(--hr-color);     }
        .badge-user   { background: #f3f4f6;           color: var(--text-muted);  }

        .role-pill        { display: inline-block; padding: .12rem .5rem; border-radius: 99px; font-size: .72rem; font-weight: 600; text-transform: uppercase; letter-spacing: .03em; }
        .role-pill-admin  { background: var(--admin-bg); color: var(--admin-color); }
        .role-pill-hr     { background: var(--hr-bg);    color: var(--hr-color);    }
        .role-pill-user   { background: #f3f4f6;          color: var(--text-muted); }

        .avatar {
            width: 34px; height: 34px; border-radius: 50%;
            background: var(--btn-bg); color: #fff; font-size: .78rem;
            font-weight: 600; display: inline-flex; align-items: center;
            justify-content: center; flex-shrink: 0; text-transform: uppercase;
        }
        .user-cell       { display: flex; align-items: center; gap: .75rem; }
        .user-cell .user-name { font-weight: 500; }
        .user-cell .user-id   { font-size: .75rem; color: var(--text-muted); }
        .actions-cell    { display: flex; gap: .4rem; align-items: center; }

        /* ── Pagination ───────────────────────────────────────────── */
        .pagination-wrap {
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: .5rem; padding: 1rem 1.5rem;
            border-top: 1px solid var(--border); font-size: .82rem; color: var(--text-muted);
        }
        .pagination-links { display: flex; gap: .25rem; }
        .pagination-links a,
        .pagination-links span {
            display: inline-flex; align-items: center; justify-content: center;
            width: 30px; height: 30px; border-radius: 5px; font-size: .82rem;
            text-decoration: none; color: var(--text-muted);
            border: 1px solid var(--border); transition: all .14s;
        }
        .pagination-links a:hover { background: var(--bg); color: var(--text); }
        .pagination-links span.active   { background: var(--btn-bg); color: #fff; border-color: var(--btn-bg); }
        .pagination-links span.disabled { opacity: .4; cursor: not-allowed; }

        /* ── Empty state ──────────────────────────────────────────── */
        .empty-state { text-align: center; padding: 3.5rem 1.5rem; }
        .empty-state svg { margin-bottom: 1rem; opacity: .35; }
        .empty-state p   { color: var(--text-muted); font-size: .88rem; }


        /* ── Form elements ────────────────────────────────────────── */
        .form-group { margin-bottom: 1.1rem; }
        .form-group label { display: block; font-size: .82rem; font-weight: 500; margin-bottom: .38rem; color: var(--text); }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%; padding: .52rem .75rem; font-size: .88rem; font-family: inherit;
            border: 1px solid var(--border); border-radius: 6px; color: var(--text);
            background: var(--card-bg); outline: none;
            transition: border-color .18s, box-shadow .18s;
        }
        .form-group textarea { resize: vertical; min-height: 72px; }
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus { border-color: #9ca3af; box-shadow: 0 0 0 3px rgba(156,163,175,.15); }
        .form-group input.input-error,
        .form-group select.input-error { border-color: #f87171; }
        .field-error { display: flex; align-items: center; gap: .32rem; font-size: .78rem; color: var(--danger); margin-top: .35rem; }
        .field-hint  { font-size: .76rem; color: var(--text-muted); margin-top: .28rem; }
        .form-row    { display: grid; grid-template-columns: 1fr 1fr; gap: .8rem; }


        /* ── Roles Tab — Card Grid ────────────────────────────────── */
        .roles-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; }
        .roles-header h2 { font-size: 1rem; font-weight: 600; display: flex; align-items: center; gap: .55rem; }
        .roles-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(270px, 1fr)); gap: 1.25rem; }

        .role-card {
            background: var(--card-bg); border: 1px solid var(--border);
            border-radius: 12px; box-shadow: var(--shadow); overflow: hidden;
            transition: transform .18s, box-shadow .18s; position: relative;
        }
        .role-card:hover { transform: translateY(-3px); box-shadow: 0 6px 30px rgba(0,0,0,.11); }
        .role-card-accent   { height: 6px; width: 100%; }
        .role-card-body     { padding: 1.3rem 1.4rem 1rem; }
        .role-card-icon     { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; margin-bottom: .85rem; }
        .role-card-name     { font-size: 1rem; font-weight: 600; margin-bottom: .3rem; }
        .role-card-desc     { font-size: .82rem; color: var(--text-muted); line-height: 1.5; min-height: 2.1em; }
        .role-card-footer   { display: flex; align-items: center; justify-content: flex-end; gap: .4rem; padding: .75rem 1.4rem; border-top: 1px solid var(--border); background: #fafafa; }

        /* ── Color swatches ───────────────────────────────────────── */
        .color-swatches { display: flex; flex-wrap: wrap; gap: .5rem; margin-top: .45rem; }
        .color-swatch   { width: 26px; height: 26px; border-radius: 6px; border: 2px solid transparent; cursor: pointer; transition: transform .15s, border-color .15s; }
        .color-swatch:hover { transform: scale(1.15); }
        .color-swatch.selected { border-color: var(--text) !important; }

        /* ── Roles empty state ────────────────────────────────────── */
        .roles-empty     { text-align: center; padding: 4rem 2rem; color: var(--text-muted); }
        .roles-empty svg { opacity: .3; margin-bottom: 1rem; }
        .roles-empty p   { font-size: .88rem; }
        .roles-empty h3  { font-size: 1rem; font-weight: 600; color: var(--text); margin-bottom: .3rem; }
    </style>
</head>
<body>

{{-- ── Active Tab Detection ─────────────────────────────────────── --}}
@php
    $activeTab      = request('tab', '');
    $empTabActive   = $activeTab === 'emp' || request('search') || request('page');
    $rolesTabActive = $activeTab === 'roles';
    $dashTabActive  = !$empTabActive && !$rolesTabActive;
    // rolesMap: keyed by role name for badge lookups throughout partials
    $rolesMap = $roles->keyBy('name');
    $myRoleObj = $rolesMap->get($user->role);
    $myRoleColor = $myRoleObj?->color ?? '#374151';
@endphp

{{-- ── Navbar ────────────────────────────────────────────────────── --}}
<nav class="navbar">
    <div class="nav-left">
        <span class="brand">{{ config('app.name', 'MyApp') }}</span>
    </div>

    <div class="tabs">
        <button class="nav-tab-btn {{ $dashTabActive ? 'active' : '' }}" id="tab-btn-dashboard" onclick="switchTab('dashboard-tab', this)">
            <svg width="14" height="14" viewBox="0 0 20 20" fill="currentColor"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>
            Dashboard
        </button>
        <button class="nav-tab-btn {{ $empTabActive ? 'active' : '' }}" id="tab-btn-emp" onclick="switchTab('emp-tab', this)">
            <svg width="14" height="14" viewBox="0 0 20 20" fill="currentColor"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.660.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/></svg>
            Employees
        </button>
        @if($user->isAdmin())
        <button class="nav-tab-btn {{ $rolesTabActive ? 'active' : '' }}" id="tab-btn-roles" onclick="switchTab('roles-tab', this)">
            <svg width="14" height="14" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            Roles
        </button>
        @endif
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

{{-- ── Main Content ──────────────────────────────────────────────── --}}
<div class="main">

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="alert alert-success" role="alert">
            <svg width="15" height="15" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-error" role="alert">
            <svg width="15" height="15" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/></svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- ── Tab Partials ──────────────────────────────────────────── --}}
    @include('auth.partials.tab_dashboard')
    @include('auth.partials.tab_employees')
    @include('auth.partials.tab_roles')

</div>{{-- /.main --}}

{{-- ── Shared JavaScript ─────────────────────────────────────────── --}}
<script>
    // ── Color palette (used by Roles tab) ───────────────────────────────
    const ROLE_COLORS = [
        '#2563eb', // Blue
        '#7c3aed', // Violet
        '#db2777', // Pink
        '#dc2626', // Red
        '#ea580c', // Orange
        '#d97706', // Amber
        '#16a34a', // Green
        '#0891b2', // Cyan
        '#0d9488', // Teal
        '#4f46e5', // Indigo
        '#9333ea', // Purple
        '#be185d', // Rose
        '#374151', // Gray
    ];

    function renderSwatches(containerId, hiddenInputId, selectedColor) {
        const container = document.getElementById(containerId);
        if (!container) return;
        container.innerHTML = '';
        ROLE_COLORS.forEach(function(color) {
            const swatch = document.createElement('div');
            swatch.className = 'color-swatch' + (color === selectedColor ? ' selected' : '');
            swatch.style.background = color;
            swatch.title = color;
            swatch.onclick = function() {
                container.querySelectorAll('.color-swatch').forEach(function(s) { s.classList.remove('selected'); });
                swatch.classList.add('selected');
                document.getElementById(hiddenInputId).value = color;
            };
            container.appendChild(swatch);
        });
    }


    // ── Tab switching ────────────────────────────────────────────────────
    function switchTab(tabId, btnElement) {
        document.querySelectorAll('.tab-content').forEach(function(c) { c.classList.remove('active'); });
        document.querySelectorAll('.nav-tab-btn').forEach(function(b) { b.classList.remove('active'); });
        document.getElementById(tabId).classList.add('active');
        btnElement.classList.add('active');
        if (tabId === 'dashboard-tab' && window.location.search) {
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    }

    // ── Init color swatches on load ──────────────────────────────────────
    renderSwatches('create-color-swatches', 'create-role-color', ROLE_COLORS[0]);
    renderSwatches('edit-color-swatches',   'edit-role-color',   ROLE_COLORS[0]);
</script>

</body>
</html>
