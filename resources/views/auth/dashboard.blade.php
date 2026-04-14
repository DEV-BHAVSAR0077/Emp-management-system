<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard — {{ config('app.name') }}</title>
    <meta name="description" content="Manage all registered users from your dashboard." />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet" />
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:         #f4f5f7;
            --card-bg:    #ffffff;
            --border:     #dde1e7;
            --text:       #1a1d23;
            --text-muted: #6b7280;
            --btn-bg:     #374151;
            --btn-hover:  #1f2937;
            --btn-text:   #ffffff;
            --radius:     8px;
            --shadow:     0 1px 4px rgba(0,0,0,.08), 0 4px 16px rgba(0,0,0,.06);
            --success:    #15803d;
            --success-bg: #f0fdf4;
            --danger:     #b91c1c;
            --danger-bg:  #fef2f2;
            --danger-border: #fecaca;
            --info:       #1d4ed8;
            --info-bg:    #eff6ff;
            --warn:       #92400e;
            --warn-bg:    #fffbeb;
            --warn-border:#fde68a;
            --hr-color:   #6d28d9;
            --hr-bg:      #f5f3ff;
            --admin-color:#b45309;
            --admin-bg:   #fef3c7;
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

        /* ── Top Nav ──────────────────────────────────────────────── */
        .navbar {
            background: var(--card-bg);
            border-bottom: 1px solid var(--border);
            padding: .9rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .navbar .brand {
            font-size: 1.05rem;
            font-weight: 600;
            color: var(--text);
            text-decoration: none;
        }

        .navbar .nav-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .navbar .nav-user {
            font-size: .84rem;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: .45rem;
        }

        .btn-logout {
            padding: .45rem .95rem;
            font-size: .84rem;
            font-family: inherit;
            font-weight: 500;
            background: var(--btn-bg);
            color: var(--btn-text);
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background .18s;
        }
        .btn-logout:hover { background: var(--btn-hover); }

        /* ── Main content ─────────────────────────────────────────── */
        .main {
            max-width: 1100px;
            margin: 2.5rem auto;
            padding: 0 1.5rem;
        }

        /* ── Welcome card ─────────────────────────────────────────── */
        .dash-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 1.75rem 2.25rem;
            margin-bottom: 1.75rem;
        }

        .dash-card h1 {
            font-size: 1.15rem;
            font-weight: 600;
            margin-bottom: .3rem;
        }

        .dash-card p {
            color: var(--text-muted);
            font-size: .88rem;
        }

        /* ── Alert ────────────────────────────────────────────────── */
        .alert {
            padding: .75rem 1rem;
            border-radius: var(--radius);
            font-size: .85rem;
            margin-bottom: 1.4rem;
            border: 1px solid transparent;
            display: flex;
            align-items: flex-start;
            gap: .5rem;
        }
        .alert-success {
            background: var(--success-bg);
            color: var(--success);
            border-color: #bbf7d0;
        }
        .alert-error {
            background: var(--danger-bg);
            color: var(--danger);
            border-color: var(--danger-border);
        }
        .alert-warn {
            background: var(--warn-bg);
            color: var(--warn);
            border-color: var(--warn-border);
        }

        /* ── Users Panel ──────────────────────────────────────────── */
        .panel {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        .panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: .75rem;
            padding: 1.25rem 1.75rem;
            border-bottom: 1px solid var(--border);
        }

        .panel-header h2 {
            font-size: 1rem;
            font-weight: 600;
        }

        .panel-actions {
            display: flex;
            align-items: center;
            gap: .65rem;
            flex-wrap: wrap;
        }

        /* Search form */
        .search-form {
            display: flex;
            align-items: center;
            gap: .4rem;
        }

        .search-input {
            padding: .42rem .75rem;
            font-size: .84rem;
            font-family: inherit;
            border: 1px solid var(--border);
            border-radius: 6px;
            background: var(--bg);
            color: var(--text);
            outline: none;
            width: 220px;
            transition: border-color .18s;
        }
        .search-input:focus { border-color: #9ca3af; }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .42rem .9rem;
            font-size: .84rem;
            font-family: inherit;
            font-weight: 500;
            border: 1px solid transparent;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            transition: background .18s, border-color .18s, color .18s;
            white-space: nowrap;
        }
        .btn-primary {
            background: var(--btn-bg);
            color: var(--btn-text);
            border-color: var(--btn-bg);
        }
        .btn-primary:hover { background: var(--btn-hover); border-color: var(--btn-hover); }

        .btn-ghost {
            background: transparent;
            color: var(--text-muted);
            border-color: var(--border);
        }
        .btn-ghost:hover { background: var(--bg); color: var(--text); }

        .btn-sm { padding: .3rem .65rem; font-size: .79rem; }

        .btn-edit {
            background: var(--info-bg);
            color: var(--info);
            border-color: #bfdbfe;
        }
        .btn-edit:hover { background: #dbeafe; }

        .btn-danger {
            background: var(--danger-bg);
            color: var(--danger);
            border-color: var(--danger-border);
        }
        .btn-danger:hover { background: #fee2e2; }

        /* ── Table ────────────────────────────────────────────────── */
        .table-wrap { overflow-x: auto; }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: .875rem;
        }

        thead th {
            text-align: left;
            padding: .75rem 1.25rem;
            font-weight: 500;
            font-size: .78rem;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border);
            background: var(--bg);
            white-space: nowrap;
        }

        tbody tr {
            border-bottom: 1px solid var(--border);
            transition: background .12s;
        }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: #f9fafb; }

        tbody td {
            padding: .85rem 1.25rem;
            color: var(--text);
            vertical-align: middle;
        }

        /* ── Badges ───────────────────────────────────────────────── */
        .badge {
            display: inline-block;
            padding: .18rem .55rem;
            font-size: .74rem;
            font-weight: 500;
            border-radius: 99px;
            background: #f3f4f6;
            color: var(--text-muted);
        }
        .badge-you {
            background: var(--info-bg);
            color: var(--info);
        }
        .badge-admin {
            background: var(--admin-bg);
            color: var(--admin-color);
        }
        .badge-hr {
            background: var(--hr-bg);
            color: var(--hr-color);
        }
        .badge-user {
            background: #f3f4f6;
            color: var(--text-muted);
        }
        /* role badge in nav */
        .role-pill {
            display: inline-block;
            padding: .12rem .5rem;
            border-radius: 99px;
            font-size: .72rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .03em;
        }
        .role-pill-admin { background: var(--admin-bg); color: var(--admin-color); }
        .role-pill-hr    { background: var(--hr-bg);    color: var(--hr-color);    }
        .role-pill-user  { background: #f3f4f6;         color: var(--text-muted);  }

        .avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: var(--btn-bg);
            color: #fff;
            font-size: .78rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            text-transform: uppercase;
        }

        .user-cell {
            display: flex;
            align-items: center;
            gap: .75rem;
        }

        .user-cell .user-name { font-weight: 500; }
        .user-cell .user-id  { font-size: .75rem; color: var(--text-muted); }

        /* Actions column */
        .actions-cell {
            display: flex;
            gap: .4rem;
            align-items: center;
        }

        /* ── Pagination ───────────────────────────────────────────── */
        .pagination-wrap {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: .5rem;
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--border);
            font-size: .82rem;
            color: var(--text-muted);
        }

        .pagination-links {
            display: flex;
            gap: .25rem;
        }

        .pagination-links a,
        .pagination-links span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 5px;
            font-size: .82rem;
            text-decoration: none;
            color: var(--text-muted);
            border: 1px solid var(--border);
            transition: all .14s;
        }
        .pagination-links a:hover { background: var(--bg); color: var(--text); }
        .pagination-links span.active {
            background: var(--btn-bg);
            color: #fff;
            border-color: var(--btn-bg);
        }
        .pagination-links span.disabled { opacity: .4; cursor: not-allowed; }

        /* ── Empty state ──────────────────────────────────────────── */
        .empty-state {
            text-align: center;
            padding: 3.5rem 1.5rem;
        }
        .empty-state svg { margin-bottom: 1rem; opacity: .35; }
        .empty-state p { color: var(--text-muted); font-size: .88rem; }

        /* ── Modal ────────────────────────────────────────────────── */
        .modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.35);
            z-index: 200;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .modal-backdrop.open { display: flex; }

        .modal {
            background: var(--card-bg);
            border-radius: var(--radius);
            box-shadow: 0 8px 40px rgba(0,0,0,.18);
            width: 100%;
            max-width: 480px;
            padding: 2rem;
            animation: modal-in .2s ease;
        }
        @keyframes modal-in {
            from { opacity: 0; transform: translateY(-12px) scale(.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        .modal-title {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 1.4rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .modal-close {
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-muted);
            padding: .2rem;
            border-radius: 4px;
            line-height: 1;
            transition: color .15s;
        }
        .modal-close:hover { color: var(--text); }

        /* ── Form elements ────────────────────────────────────────── */
        .form-group {
            margin-bottom: 1.1rem;
        }
        .form-group label {
            display: block;
            font-size: .82rem;
            font-weight: 500;
            margin-bottom: .38rem;
            color: var(--text);
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: .52rem .75rem;
            font-size: .88rem;
            font-family: inherit;
            border: 1px solid var(--border);
            border-radius: 6px;
            color: var(--text);
            background: var(--card-bg);
            outline: none;
            transition: border-color .18s, box-shadow .18s;
        }
        .form-group input:focus,
        .form-group select:focus {
            border-color: #9ca3af;
            box-shadow: 0 0 0 3px rgba(156,163,175,.15);
        }
        .form-group input.input-error,
        .form-group select.input-error { border-color: #f87171; }

        .field-error {
            display: flex;
            align-items: center;
            gap: .32rem;
            font-size: .78rem;
            color: var(--danger);
            margin-top: .35rem;
        }
        .field-hint {
            font-size: .76rem;
            color: var(--text-muted);
            margin-top: .28rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: .8rem;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: .6rem;
            margin-top: 1.5rem;
            padding-top: 1.2rem;
            border-top: 1px solid var(--border);
        }

        /* ── Confirm modal ────────────────────────────────────────── */
        .confirm-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--danger-bg);
            color: var(--danger);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }
        .confirm-body { text-align: center; }
        .confirm-body p { color: var(--text-muted); font-size: .88rem; margin-top: .4rem; }
        .confirm-body strong { color: var(--text); }
        .confirm-footer { justify-content: center; }
    </style>
</head>
<body>

{{-- Navigation bar --}}
<nav class="navbar">
    <span class="brand">{{ config('app.name', 'MyApp') }}</span>
    <div class="nav-right">
        <span class="nav-user">
            {{ $user->email }}
            @if($user->isAdmin())
                <span class="role-pill role-pill-admin">Admin</span>
            @elseif($user->isHr())
                <span class="role-pill role-pill-hr">HR</span>
            @else
                <span class="role-pill role-pill-user">User</span>
            @endif
        </span>
        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
            @csrf
            <button type="submit" id="btn-logout" class="btn-logout">Sign Out</button>
        </form>
    </div>
</nav>

{{-- Main area --}}
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

    {{-- Welcome card --}}
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

    {{-- Users Panel --}}
    <div class="panel">
        <div class="panel-header">
            <h2>All Users
                <span class="badge" style="margin-left:.5rem; font-size:.72rem;">{{ $users->total() }}</span>
            </h2>
            <div class="panel-actions">
                {{-- Search --}}
                <form method="GET" action="{{ route('dashboard') }}" class="search-form" id="form-search">
                    <input
                        type="text"
                        name="search"
                        id="input-search"
                        class="search-input"
                        placeholder="Search name or email…"
                        value="{{ $search }}"
                        autocomplete="off"
                    />
                    @if ($search)
                        <a href="{{ route('dashboard') }}" class="btn btn-ghost btn-sm" id="btn-clear-search" title="Clear search">✕</a>
                    @endif
                    <button type="submit" class="btn btn-ghost btn-sm" id="btn-search">Search</button>
                </form>

                {{-- Add User — only Admin/HR --}}
                @if($user->canManageUsers())
                <button class="btn btn-primary btn-sm" id="btn-open-create" onclick="openCreateModal()">
                    Add User
                </button>
                @endif
            </div>
        </div>

        @if($search)
            <div style="padding:.6rem 1.75rem; font-size:.82rem; color:var(--text-muted); background:var(--info-bg); border-bottom:1px solid #bfdbfe;">
                Showing results for <strong>"{{ $search }}"</strong> — {{ $users->total() }} {{ Str::plural('user', $users->total()) }} found.
            </div>
        @endif

        <div class="table-wrap">
            @if($users->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Joined</th>
                        <th>Last Updated</th>
                        <th style="text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $index => $u)
                    <tr id="user-row-{{ $u->id }}">
                        <td style="color:var(--text-muted); width:50px;">
                            {{ $users->firstItem() + $index }}
                        </td>
                        <td>
                            <div class="user-cell">
                                <div class="avatar">{{ mb_substr($u->name, 0, 2) }}</div>
                                <div>
                                    <div class="user-name">
                                        {{ $u->name }}
                                        @if($u->id === Auth::id())
                                            <span class="badge badge-you">You</span>
                                        @endif
                                    </div>
                                    <div class="user-id">#{{ $u->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $u->email }}</td>
                        <td>
                            @if($u->role === 'admin')
                                <span class="badge badge-admin">Admin</span>
                            @elseif($u->role === 'hr')
                                <span class="badge badge-hr">HR</span>
                            @else
                                <span class="badge badge-user">User</span>
                            @endif
                        </td>
                        <td style="color:var(--text-muted);">{{ $u->created_at->format('d M Y') }}</td>
                        <td style="color:var(--text-muted);">{{ $u->updated_at->format('d M Y') }}</td>
                        <td>
                            <div class="actions-cell" style="justify-content:center;">

                                {{--
                                    Edit button visibility:
                                    - Admin/HR: can edit anyone
                                    - Regular user: can only edit their own row
                                --}}
                                @if($user->canManageUsers() || $u->id === Auth::id())
                                <button
                                    class="btn btn-edit btn-sm"
                                    id="btn-edit-{{ $u->id }}"
                                    onclick="openEditModal({{ $u->id }}, '{{ addslashes($u->name) }}', '{{ addslashes($u->email) }}', '{{ $u->role }}')"
                                    title="Edit user"
                                >
                                    <svg width="11" height="11" viewBox="0 0 20 20" fill="currentColor"><path d="M2.695 14.763l-1.262 3.154a.5.5 0 00.65.65l3.155-1.262a4 4 0 001.343-.885L17.5 5.5a2.121 2.121 0 00-3-3L3.58 13.42a4 4 0 00-.885 1.343z"/></svg>
                                    @if($u->id === Auth::id() && !$user->canManageUsers())
                                        Edit Profile
                                    @else
                                        Edit
                                    @endif
                                </button>
                                @endif

                                {{--
                                    Delete button visibility:
                                    - Only Admin/HR; never on own row
                                --}}
                                @if($user->canManageUsers() && $u->id !== Auth::id())
                                <button
                                    class="btn btn-danger btn-sm"
                                    id="btn-delete-{{ $u->id }}"
                                    onclick="openDeleteModal({{ $u->id }}, '{{ addslashes($u->name) }}')"
                                    title="Delete user"
                                >
                                    <svg width="11" height="11" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd"/></svg>
                                    Delete
                                </button>
                                @endif

                                {{-- No actions for regular users looking at other people's rows --}}
                                @if(!$user->canManageUsers() && $u->id !== Auth::id())
                                    <span style="font-size:.75rem; color:var(--text-muted);">—</span>
                                @endif

                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
                <div class="empty-state">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    <p>
                        @if($search)
                            No users match your search. <a href="{{ route('dashboard') }}" style="color:var(--info);">Clear search</a>
                        @else
                            No users found. Add the first one!
                        @endif
                    </p>
                </div>
            @endif
        </div>

        {{-- Pagination --}}
        @if($users->hasPages())
        <div class="pagination-wrap">
            <div>
                Showing {{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }} users
            </div>
            <div class="pagination-links">
                {{-- Previous --}}
                @if($users->onFirstPage())
                    <span class="disabled">‹</span>
                @else
                    <a href="{{ $users->previousPageUrl() }}" id="btn-prev-page">‹</a>
                @endif

                {{-- Page numbers --}}
                @foreach(range(1, $users->lastPage()) as $page)
                    @if($page == $users->currentPage())
                        <span class="active">{{ $page }}</span>
                    @else
                        <a href="{{ $users->url($page) }}" id="btn-page-{{ $page }}">{{ $page }}</a>
                    @endif
                @endforeach

                {{-- Next --}}
                @if($users->hasMorePages())
                    <a href="{{ $users->nextPageUrl() }}" id="btn-next-page">›</a>
                @else
                    <span class="disabled">›</span>
                @endif
            </div>
        </div>
        @endif
    </div>{{-- /.panel --}}

</div>{{-- /.main --}}

{{-- ════════════════════════════════════════════════════════════════
     CREATE USER MODAL — only rendered for admin/HR
     ════════════════════════════════════════════════════════════════ --}}
@if($user->canManageUsers())
<div class="modal-backdrop" id="modal-create" role="dialog" aria-modal="true" aria-labelledby="modal-create-title">
    <div class="modal">
        <div class="modal-title">
            <span id="modal-create-title">Add New User</span>
            <button class="modal-close" onclick="closeModal('modal-create')" aria-label="Close">
                <svg width="18" height="18" viewBox="0 0 20 20" fill="currentColor"><path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z"/></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('users.store') }}" id="form-create-user" novalidate>
            @csrf

            <div class="form-group">
                <label for="create_name">Full Name</label>
                <input type="text" id="create_name" name="name" placeholder="John Doe"
                    value="{{ old('name') }}"
                    class="{{ $errors->has('name') ? 'input-error' : '' }}" required />
                @error('name')
                    <span class="field-error">
                        <svg width="11" height="11" viewBox="0 0 16 16" fill="currentColor"><path d="M8 1a7 7 0 1 0 0 14A7 7 0 0 0 8 1zm.75 10.5h-1.5v-1.5h1.5v1.5zm0-3h-1.5V4h1.5v4.5z"/></svg>
                        {{ $message }}
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <label for="create_email">Email Address</label>
                <input type="email" id="create_email" name="email" placeholder="you@example.com"
                    value="{{ old('email') }}"
                    class="{{ $errors->has('email') ? 'input-error' : '' }}" required />
                @error('email')
                    <span class="field-error">
                        <svg width="11" height="11" viewBox="0 0 16 16" fill="currentColor"><path d="M8 1a7 7 0 1 0 0 14A7 7 0 0 0 8 1zm.75 10.5h-1.5v-1.5h1.5v1.5zm0-3h-1.5V4h1.5v4.5z"/></svg>
                        {{ $message }}
                    </span>
                @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="create_password">Password</label>
                    <input type="password" id="create_password" name="password"
                        placeholder="Min. 8 characters"
                        class="{{ $errors->has('password') ? 'input-error' : '' }}" required />
                    @error('password')
                        <span class="field-error">
                            <svg width="11" height="11" viewBox="0 0 16 16" fill="currentColor"><path d="M8 1a7 7 0 1 0 0 14A7 7 0 0 0 8 1zm.75 10.5h-1.5v-1.5h1.5v1.5zm0-3h-1.5V4h1.5v4.5z"/></svg>
                            {{ $message }}
                        </span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="create_password_confirmation">Confirm Password</label>
                    <input type="password" id="create_password_confirmation"
                        name="password_confirmation" placeholder="Repeat password" required />
                </div>
            </div>

            {{-- Role selector — only admin sees this --}}
            @if($user->isAdmin())
            <div class="form-group">
                <label for="create_role">Role</label>
                <select id="create_role" name="role">
                    <option value="user" {{ old('role', 'user') === 'user' ? 'selected' : '' }}>User</option>
                    <option value="hr"   {{ old('role') === 'hr'    ? 'selected' : '' }}>HR</option>
                    <option value="admin"{{ old('role') === 'admin'  ? 'selected' : '' }}>Admin</option>
                </select>
                @error('role')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>
            @endif

            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal('modal-create')">Cancel</button>
                <button type="submit" class="btn btn-primary" id="btn-create-submit">Create User</button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- ════════════════════════════════════════════════════════════════
     EDIT USER MODAL
     ════════════════════════════════════════════════════════════════ --}}
<div class="modal-backdrop" id="modal-edit" role="dialog" aria-modal="true" aria-labelledby="modal-edit-title">
    <div class="modal">
        <div class="modal-title">
            <span id="modal-edit-title">Edit User</span>
            <button class="modal-close" onclick="closeModal('modal-edit')" aria-label="Close">
                <svg width="18" height="18" viewBox="0 0 20 20" fill="currentColor"><path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z"/></svg>
            </button>
        </div>

        <form method="POST" action="" id="form-edit-user" novalidate>
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="edit_name">Full Name</label>
                <input type="text" id="edit_name" name="name" placeholder="John Doe" required />
                @error('name')
                    <span class="field-error">
                        <svg width="11" height="11" viewBox="0 0 16 16" fill="currentColor"><path d="M8 1a7 7 0 1 0 0 14A7 7 0 0 0 8 1zm.75 10.5h-1.5v-1.5h1.5v1.5zm0-3h-1.5V4h1.5v4.5z"/></svg>
                        {{ $message }}
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <label for="edit_email">Email Address</label>
                <input type="email" id="edit_email" name="email" placeholder="you@example.com" required />
                @error('email')
                    <span class="field-error">
                        <svg width="11" height="11" viewBox="0 0 16 16" fill="currentColor"><path d="M8 1a7 7 0 1 0 0 14A7 7 0 0 0 8 1zm.75 10.5h-1.5v-1.5h1.5v1.5zm0-3h-1.5V4h1.5v4.5z"/></svg>
                        {{ $message }}
                    </span>
                @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="edit_password">New Password</label>
                    <input type="password" id="edit_password" name="password" placeholder="Leave blank to keep" />
                    <span class="field-hint">Leave empty to keep current password.</span>
                    @error('password')
                        <span class="field-error">
                            <svg width="11" height="11" viewBox="0 0 16 16" fill="currentColor"><path d="M8 1a7 7 0 1 0 0 14A7 7 0 0 0 8 1zm.75 10.5h-1.5v-1.5h1.5v1.5zm0-3h-1.5V4h1.5v4.5z"/></svg>
                            {{ $message }}
                        </span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="edit_password_confirmation">Confirm Password</label>
                    <input type="password" id="edit_password_confirmation"
                        name="password_confirmation" placeholder="Repeat new password" />
                </div>
            </div>

            {{-- Role selector — only visible to admin --}}
            @if($user->isAdmin())
            <div class="form-group" id="edit-role-group">
                <label for="edit_role">Role</label>
                <select id="edit_role" name="role">
                    <option value="user">User</option>
                    <option value="hr">HR</option>
                    <option value="admin">Admin</option>
                </select>
                @error('role')
                    <span class="field-error">{{ $message }}</span>
                @enderror
                <span class="field-hint">Changing role grants or revokes management permissions.</span>
            </div>
            @endif

            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal('modal-edit')">Cancel</button>
                <button type="submit" class="btn btn-primary" id="btn-edit-submit">Save Changes</button>
            </div>
        </form>
    </div>
</div>

{{-- ════════════════════════════════════════════════════════════════
     DELETE CONFIRM MODAL — only rendered for admin/HR
     ════════════════════════════════════════════════════════════════ --}}
@if($user->canManageUsers())
<div class="modal-backdrop" id="modal-delete" role="dialog" aria-modal="true" aria-labelledby="modal-delete-title">
    <div class="modal" style="max-width:400px;">
        <div class="confirm-body">
            <div class="confirm-icon">
                <svg width="22" height="22" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd"/></svg>
            </div>
            <h2 id="modal-delete-title" style="font-size:1rem; font-weight:600;">Delete User</h2>
            <p>Are you sure you want to delete <strong id="delete-user-name">this user</strong>? This action cannot be undone.</p>
        </div>

        <form method="POST" action="" id="form-delete-user">
            @csrf
            @method('DELETE')
            <div class="modal-footer confirm-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal('modal-delete')">Cancel</button>
                <button type="submit" class="btn btn-danger" id="btn-confirm-delete">Yes, Delete</button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
    // ── Modal helpers ────────────────────────────────────────────────────
    function openModal(id) {
        document.getElementById(id).classList.add('open');
        document.body.style.overflow = 'hidden';
    }
    function closeModal(id) {
        document.getElementById(id).classList.remove('open');
        document.body.style.overflow = '';
    }

    // Close on backdrop click
    document.querySelectorAll('.modal-backdrop').forEach(function(backdrop) {
        backdrop.addEventListener('click', function(e) {
            if (e.target === backdrop) closeModal(backdrop.id);
        });
    });

    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-backdrop.open').forEach(function(m) {
                closeModal(m.id);
            });
        }
    });

    // ── Create modal ─────────────────────────────────────────────────────
    function openCreateModal() {
        openModal('modal-create');
        setTimeout(function() { document.getElementById('create_name').focus(); }, 50);
    }

    // ── Edit modal ───────────────────────────────────────────────────────
    function openEditModal(id, name, email, role) {
        document.getElementById('edit_name').value  = name;
        document.getElementById('edit_email').value = email;
        document.getElementById('edit_password').value = '';
        document.getElementById('edit_password_confirmation').value = '';

        // Set role if the selector exists (admin only)
        var roleSelect = document.getElementById('edit_role');
        if (roleSelect) {
            roleSelect.value = role || 'user';
        }

        var form = document.getElementById('form-edit-user');
        form.action = '/users/' + id;

        openModal('modal-edit');
        setTimeout(function() { document.getElementById('edit_name').focus(); }, 50);
    }

    // ── Delete modal ─────────────────────────────────────────────────────
    function openDeleteModal(id, name) {
        document.getElementById('delete-user-name').textContent = name;
        document.getElementById('form-delete-user').action = '/users/' + id;
        openModal('modal-delete');
    }

    // ── Auto-open modals on validation error (after POST redirect) ───────
    @if($errors->any() && old('_method') === 'PUT')
        openModal('modal-edit');
    @elseif($errors->any() && !old('_method'))
        @if($user->canManageUsers())
            openModal('modal-create');
        @endif
    @endif
</script>

</body>
</html>
