<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', config('app.name'))</title>
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
            --restore-bg:    #d3c2faff;
            --restore:       #5a31b8ff;
            --restore-border:  #9e70fcff;
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
            z-index: 1000;
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
            background: none; border: none; text-decoration: none;
            font-size: 0.95rem; font-weight: 500;
            color: var(--text-muted); cursor: pointer;
            border-bottom: 2px solid transparent;
            margin-bottom: -1px; font-family: inherit;
            transition: color 0.15s, border-color 0.15s;
            outline: none; display: flex; align-items: center; gap: .35rem;
        }
        .nav-tab-btn:hover { color: var(--text); border-bottom-color: #d1d5db; }
        .nav-tab-btn.active { color: #2563eb; border-bottom-color: #2563eb; }

        /* ── Main wrapper ─────────────────────────────────────────── */
        .main { max-width: 1100px; margin: 2.5rem auto; padding: 0 1.5rem; }
        .main-narrow { max-width: 600px; }

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
            display: inline-flex; align-items: center; gap: .35rem; justify-content: center;
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
        .btn-restore {background: var(--restore-bg); color: var(--restore); border-color: var(--restore-border); }
        .btn-restore:hover { background: #d9bbffff; }

        /* ── Panel (Employees & Forms) ────────────────────────────── */
        .panel { background: var(--card-bg); border: 1px solid var(--border); border-radius: var(--radius); box-shadow: var(--shadow); }
        .panel-header {
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: .75rem; padding: 1.25rem 1.75rem;
            border-bottom: 1px solid var(--border);
        }
        .panel-header h2 { font-size: 1rem; font-weight: 600; }
        .panel-actions   { display: flex; align-items: center; gap: .65rem; flex-wrap: wrap; }
        .panel-body      { padding: 2rem; }
        .panel-body h2   { font-size: 1.25rem; margin-bottom: 1.5rem; font-weight: 600; }

        /* ── Forms ────────────────────────────────────────────────── */
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
        .form-actions { display: flex; justify-content: flex-end; gap: .6rem; margin-top: 1.5rem; padding-top: 1.2rem; border-top: 1px solid var(--border); }

        /* ── Tables & Badges ──────────────────────────────────────── */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: .875rem; }
        thead th {
            text-align: left; padding: .75rem 1.25rem; font-weight: 500; font-size: .78rem; 
            text-transform: uppercase; letter-spacing: .04em; color: var(--text-muted);
            border-bottom: 1px solid var(--border); background: var(--bg); white-space: nowrap;
        }
        tbody tr { border-bottom: 1px solid var(--border); transition: background .12s; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: #f9fafb; }
        tbody td { padding: .85rem 1.25rem; color: var(--text); vertical-align: middle; }

        .badge        { display: inline-block; padding: .18rem .55rem; font-size: .74rem; font-weight: 500; border-radius: 99px; background: #f3f4f6; color: var(--text-muted); }
        .badge-you    { background: var(--info-bg);   color: var(--info);         }
        .badge-admin  { background: var(--admin-bg);  color: var(--admin-color);  }
        .badge-hr     { background: var(--hr-bg);     color: var(--hr-color);     }

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

        /* ── Other Utils ──────────────────────────────────────────── */
        .search-form  { display: flex; align-items: center; gap: .4rem; }
        .search-input {
            padding: .42rem .75rem; font-size: .84rem; font-family: inherit;
            border: 1px solid var(--border); border-radius: 6px;
            background: var(--bg); color: var(--text); outline: none;
            width: 220px; transition: border-color .18s;
        }
        .search-input:focus { border-color: #9ca3af; }
        
        .pagination-wrap {
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: .5rem; padding: 1rem 1.5rem;
            border-top: 1px solid var(--border); font-size: .82rem; color: var(--text-muted);
        }
        .pagination-links { 
            display: flex; gap: .25rem; 
            overflow-x: auto; padding-bottom: .4rem; 
            max-width: 100%; scrollbar-width: thin;
        }
        .pagination-links::-webkit-scrollbar { height: 6px; }
        .pagination-links::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .pagination-links a, .pagination-links span {
            display: inline-flex; align-items: center; justify-content: center;
            width: 30px; height: 30px; border-radius: 5px; font-size: .82rem;
            text-decoration: none; color: var(--text-muted);
            border: 1px solid var(--border); transition: all .14s;
            flex-shrink: 0;
        }
        .pagination-links a:hover { background: var(--bg); color: var(--text); }
        .pagination-links span.active   { background: var(--btn-bg); color: #fff; border-color: var(--btn-bg); }
        .pagination-links span.disabled { opacity: .4; cursor: not-allowed; }

        .empty-state { text-align: center; padding: 3.5rem 1.5rem; }
        .empty-state svg { margin-bottom: 1rem; opacity: .35; }
        .empty-state p   { color: var(--text-muted); font-size: .88rem; }

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
        
        .color-swatches { display: flex; flex-wrap: wrap; gap: .5rem; margin-top: .45rem; }
        .color-swatch   { width: 26px; height: 26px; border-radius: 6px; border: 2px solid transparent; cursor: pointer; transition: transform .15s, border-color .15s; }
        .color-swatch:hover { transform: scale(1.15); }
        .color-swatch.selected { border-color: var(--text) !important; }
        
        .roles-empty     { text-align: center; padding: 4rem 2rem; color: var(--text-muted); }
        .roles-empty svg { opacity: .3; margin-bottom: 1rem; }
        .roles-empty p   { font-size: .88rem; }
        .roles-empty h3  { font-size: 1rem; font-weight: 600; color: var(--text); margin-bottom: .3rem; }

        .tab-content { display: none; }
        .tab-content.active { display: block; animation: fade-in 0.2s ease; }
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(4px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── Protected notice ─────────────────────────────────────── */
        .notice {
            display: flex; align-items: center; gap: .5rem; padding: .65rem 1rem;
            border-radius: 6px; font-size: .82rem; background: #fffbeb;
            color: #92400e; border: 1px solid #fde68a; margin-bottom: 1.25rem;
        }

        /* ── Confirm delete backdrop ──────────────────────────────── */
        .modal-backdrop {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,.35); z-index: 2000;
            align-items: center; justify-content: center; padding: 1rem;
        }
        .modal-backdrop.open { display: flex; }
        .modal {
            background: var(--card-bg); border-radius: var(--radius);
            box-shadow: 0 8px 40px rgba(0,0,0,.18); width: 100%; max-width: 400px;
            padding: 2rem; text-align: center; animation: modal-in .2s ease;
        }
        @keyframes modal-in {
            from { opacity:0; transform:translateY(-10px) scale(.97); }
            to   { opacity:1; transform:translateY(0) scale(1); }
        }
        .confirm-icon {
            width: 48px; height: 48px; border-radius: 50%;
            background: var(--danger-bg); color: var(--danger);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1rem;
        }
        .confirm-footer { display: flex; justify-content: center; gap: .6rem; margin-top: 1.25rem; }
        /* ── Expense Module ───────────────────────────────────────── */
        .expense-amount { font-weight: 600; font-variant-numeric: tabular-nums; white-space: nowrap; }
        .amount-symbol  { font-size: .82em; color: var(--text-muted); margin-right: .1rem; }

        /* ── Category Section ────────────────────────────────────── */
        .cat-section { margin-bottom: .2rem; }

        .cat-field-row {
            display: flex; align-items: center; gap: .4rem;
        }
        .cat-field-row select { flex: 1; }

        .cat-btn {
            display: inline-flex; align-items: center; justify-content: center;
            width: 34px; height: 34px; border-radius: 6px;
            border: 1px solid var(--border); background: var(--card-bg);
            cursor: pointer; transition: all .15s; flex-shrink: 0;
            color: var(--text-muted);
        }
        .cat-btn:hover { background: var(--bg); color: var(--text); }
        .cat-btn-add:hover  { border-color: #93c5fd; color: var(--info); background: var(--info-bg); }
        .cat-btn-remove:hover { border-color: var(--danger-border); color: var(--danger); background: var(--danger-bg); }

        .cat-inline-form {
            margin: .6rem 0 1rem; padding: .85rem 1rem;
            background: var(--bg); border: 1px solid var(--border);
            border-radius: var(--radius); border-left: 3px solid var(--info);
        }
        .cat-inline-form.cat-inline-child {
            margin-left: 1.25rem; border-left-color: #7c3aed;
        }

        .cat-inline-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: .6rem; flex-wrap: wrap; gap: .4rem;
        }
        .cat-inline-title {
            font-size: .82rem; font-weight: 600; color: var(--text);
            display: flex; align-items: center; gap: .3rem;
        }
        .cat-inline-parent-label {
            font-size: .75rem; color: var(--text-muted);
        }
        .cat-inline-parent-label strong { color: var(--text); }

        .cat-inline-body {
            display: flex; align-items: flex-end; gap: .5rem; flex-wrap: wrap;
        }
        .cat-inline-fields { flex: 1; min-width: 160px; }
        .cat-inline-input {
            width: 100%; padding: .45rem .7rem; font-size: .85rem; font-family: inherit;
            border: 1px solid var(--border); border-radius: 6px;
            background: var(--card-bg); color: var(--text); outline: none;
            transition: border-color .18s, box-shadow .18s;
        }
        .cat-inline-input:focus { border-color: #9ca3af; box-shadow: 0 0 0 3px rgba(156,163,175,.15); }

        .cat-inline-actions { display: flex; gap: .35rem; flex-shrink: 0; }

        .cat-inline-feedback {
            margin-top: .4rem; font-size: .78rem; min-height: 1.1em;
        }
        .cat-inline-feedback.success { color: var(--success); }
        .cat-inline-feedback.error   { color: var(--danger); }
    </style>
</head>
<body>

    <x-navbar />

    <div class="main @yield('main-class')">
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

        @yield('content')
    </div>

    {{-- Shared Scripts --}}
    <script>
        const ROLE_COLORS = [
            '#2563eb', '#7c3aed', '#db2777', '#dc2626', '#ea580c', '#d97706',
            '#16a34a', '#0891b2', '#0d9488', '#4f46e5', '#9333ea', '#be185d', '#374151'
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

        renderSwatches('create-color-swatches', 'create-role-color', ROLE_COLORS[0]);
        renderSwatches('edit-color-swatches',   'edit-role-color',   ROLE_COLORS[0]);
    </script>
</body>
</html>
