<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Create Role — {{ config('app.name') }}</title>
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
            --danger:     #b91c1c;
            --danger-bg:  #fef2f2;
        }

        html, body {
            height: 100%;
            font-family: 'Inter', system-ui, sans-serif;
            background: var(--bg);
            color: var(--text);
            font-size: 15px;
            line-height: 1.6;
        }

        /* ── Navbar ───────────────────────────────────────────────── */
        .navbar {
            background: var(--card-bg);
            border-bottom: 1px solid var(--border);
            padding: .9rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .navbar .brand { font-size: 1.05rem; font-weight: 600; color: var(--text); text-decoration: none; }
        .navbar .nav-right { display: flex; align-items: center; gap: 1rem; }

        /* ── Layout ───────────────────────────────────────────────── */
        .main { max-width: 620px; margin: 2.5rem auto; padding: 0 1.5rem; }

        .panel {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 2rem;
        }

        .panel-heading {
            display: flex;
            align-items: center;
            gap: .6rem;
            margin-bottom: 1.75rem;
        }
        .panel-heading h2 { font-size: 1.2rem; font-weight: 600; }

        /* ── Forms ────────────────────────────────────────────────── */
        .form-group { margin-bottom: 1.2rem; }
        .form-group label {
            display: block;
            font-size: .82rem;
            font-weight: 500;
            margin-bottom: .38rem;
            color: var(--text);
        }
        .form-group input:not([type="hidden"]):not([type="checkbox"]),
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: .55rem .75rem;
            font-size: .88rem;
            font-family: inherit;
            border: 1px solid var(--border);
            border-radius: 6px;
            color: var(--text);
            background: var(--card-bg);
            outline: none;
            transition: border-color .18s, box-shadow .18s;
        }
        .form-group textarea { resize: vertical; min-height: 80px; }
        .form-group input:not([type="hidden"]):not([type="checkbox"]):focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #9ca3af;
            box-shadow: 0 0 0 3px rgba(156,163,175,.15);
        }
        .form-group input.input-error,
        .form-group select.input-error { border-color: #f87171; }

        .field-error { display: flex; align-items: center; gap: .32rem; font-size: .78rem; color: var(--danger); margin-top: .35rem; }
        .field-hint  { font-size: .76rem; color: var(--text-muted); margin-top: .28rem; }

        /* ── Permissions Scrollable List ──────────────────────────── */
        .permissions-scroll {
            max-height: 220px;
            overflow-y: auto;
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: .8rem 1rem;
            background: var(--bg);
        }
        .permissions-scroll label {
            display: flex;
            align-items: center;
            gap: .5rem;
            margin-bottom: .5rem;
            font-weight: 400;
            font-size: .88rem;
            cursor: pointer;
            color: var(--text);
            padding: .2rem 0;
        }
        .permissions-scroll label:last-child {
            margin-bottom: 0;
        }
        .permissions-scroll input[type="checkbox"] {
            width: 1.1rem;
            height: 1.1rem;
            accent-color: #2563eb;
            cursor: pointer;
            margin: 0;
        }

        /* ── Color swatches ───────────────────────────────────────── */
        .color-swatches { display: flex; flex-wrap: wrap; gap: .5rem; margin-top: .5rem; }
        .color-swatch {
            width: 30px; height: 30px;
            border-radius: 7px;
            border: 2px solid transparent;
            cursor: pointer;
            transition: transform .15s, border-color .15s;
        }
        .color-swatch:hover { transform: scale(1.15); }
        .color-swatch.selected { border-color: var(--text) !important; transform: scale(1.1); }

        /* ── Preview card ─────────────────────────────────────────── */
        .preview-wrap { margin-bottom: 1.5rem; }
        .preview-label { font-size: .78rem; font-weight: 500; color: var(--text-muted); margin-bottom: .5rem; }
        .role-preview {
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
            max-width: 240px;
            box-shadow: 0 2px 8px rgba(0,0,0,.07);
        }
        .preview-accent { height: 6px; transition: background .2s; }
        .preview-body   { padding: 1rem 1.2rem .8rem; }
        .preview-icon   { width: 40px; height: 40px; border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; margin-bottom: .6rem; transition: background .2s, color .2s; }
        .preview-name   { font-size: .95rem; font-weight: 600; margin-bottom: .15rem; }
        .preview-desc   { font-size: .78rem; color: var(--text-muted); }

        /* ── Buttons ──────────────────────────────────────────────── */
        .btn {
            display: inline-flex; align-items: center; gap: .35rem;
            padding: .48rem .95rem;
            font-size: .84rem; font-family: inherit; font-weight: 500;
            border: 1px solid transparent; border-radius: 6px;
            cursor: pointer; text-decoration: none;
            transition: background .18s, border-color .18s;
        }
        .btn-primary { background: var(--btn-bg); color: var(--btn-text); }
        .btn-primary:hover { background: var(--btn-hover); }
        .btn-ghost { background: transparent; color: var(--text-muted); border-color: var(--border); }
        .btn-ghost:hover { background: var(--bg); color: var(--text); }

        .form-actions { display: flex; justify-content: flex-end; gap: .6rem; margin-top: 1.75rem; padding-top: 1.25rem; border-top: 1px solid var(--border); }

        /* ── Role pill ────────────────────────────────────────────── */
        .role-pill { display: inline-block; padding: .12rem .5rem; border-radius: 99px; font-size: .72rem; font-weight: 600; text-transform: uppercase; letter-spacing: .03em; }
    </style>
</head>
<body>

{{-- Navbar --}}
<nav class="navbar">
    <a href="{{ route('dashboard') }}" class="brand">{{ config('app.name', 'MyApp') }}</a>
    <div class="nav-right">
        @php
            $myRoleObj   = $roles->firstWhere('name', $user->role);
            $myRoleColor = $myRoleObj?->color ?? '#374151';
        @endphp
        <span style="font-size:.84rem; color:var(--text-muted); display:flex; align-items:center; gap:.45rem;">
            {{ $user->email }}
            <span class="role-pill" style="background:{{ $myRoleColor }}20; color:{{ $myRoleColor }}; border:1px solid {{ $myRoleColor }}40;">
                {{ $user->role }}
            </span>
        </span>
        <a href="{{ route('dashboard', ['tab' => 'roles']) }}" class="btn btn-ghost" style="padding:.3rem .6rem;">
            ← Back to Roles
        </a>
    </div>
</nav>

{{-- Page --}}
<div class="main">
    <div class="panel">
        <div class="panel-heading">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor" style="color:#2563eb;">
                <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <h2>Create New Role</h2>
        </div>

        {{-- Live Preview --}}
        <div class="preview-wrap">
            <div class="preview-label">Live Preview</div>
            <div class="role-preview">
                <div class="preview-accent" id="preview-accent" style="background:#2563eb;"></div>
                <div class="preview-body">
                    <div class="preview-icon" id="preview-icon" style="background:#2563eb22; color:#2563eb;">🛡️</div>
                    <div class="preview-name" id="preview-name">Role Name</div>
                    <div class="preview-desc" id="preview-desc" style="color:var(--text-muted);">Role description…</div>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('roles.store') }}" novalidate>
            @csrf

            <div class="form-group">
                <label for="name">Role Name <span style="color:var(--danger);">*</span></label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    placeholder="e.g. Manager, Developer, Support…"
                    maxlength="50"
                    value="{{ old('name') }}"
                    class="{{ $errors->has('name') ? 'input-error' : '' }}"
                    required
                    oninput="document.getElementById('preview-name').textContent = this.value || 'Role Name'"
                />
                @error('name')<span class="field-error">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea
                    id="description"
                    name="description"
                    placeholder="What does this role do?"
                    maxlength="255"
                    oninput="document.getElementById('preview-desc').textContent = this.value || 'Role description…'"
                >{{ old('description') }}</textarea>
                @error('description')<span class="field-error">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label>Permissions</label>
                <div class="permissions-scroll">
                    @foreach($permissions as $permission)
                        <label>
                            <input type="checkbox"
                                name="permissions[]"
                                value="{{ $permission->id }}"
                                data-name="{{ $permission->name }}">
                            {{ $permission->display_name }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="form-group">
                <label>Card Color <span style="color:var(--danger);">*</span></label>
                <input type="hidden" id="color" name="color" value="{{ old('color', '#2563eb') }}" />
                <div class="color-swatches" id="color-swatches"></div>
                <span class="field-hint">Choose a color for this role's card accent.</span>
                @error('color')<span class="field-error">{{ $message }}</span>@enderror
            </div>

            <div class="form-actions">
                <a href="{{ route('dashboard', ['tab' => 'roles']) }}" class="btn btn-ghost">Cancel</a>
                <button type="submit" class="btn btn-primary" id="btn-create-role">
                    <svg width="13" height="13" viewBox="0 0 20 20" fill="currentColor"><path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"/></svg>
                    Create Role
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const ROLE_COLORS = [
        '#2563eb','#7c3aed','#db2777','#dc2626','#ea580c',
        '#d97706','#16a34a','#0891b2','#0d9488','#4f46e5',
        '#9333ea','#be185d','#374151',
    ];

    let selectedColor = document.getElementById('color').value || ROLE_COLORS[0];

    function renderSwatches() {
        const container = document.getElementById('color-swatches');
        container.innerHTML = '';
        ROLE_COLORS.forEach(function(color) {
            const s = document.createElement('div');
            s.className = 'color-swatch' + (color === selectedColor ? ' selected' : '');
            s.style.background = color;
            s.title = color;
            s.onclick = function() {
                selectedColor = color;
                document.getElementById('color').value = color;
                container.querySelectorAll('.color-swatch').forEach(function(el){ el.classList.remove('selected'); });
                s.classList.add('selected');
                updatePreview(color);
            };
            container.appendChild(s);
        });
    }

    function updatePreview(color) {
        document.getElementById('preview-accent').style.background = color;
        document.getElementById('preview-icon').style.background   = color + '22';
        document.getElementById('preview-icon').style.color        = color;
    }

    renderSwatches();
    updatePreview(selectedColor);

    // Permission constraints logic
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('input[type="checkbox"][name="permissions[]"]');
        const getCheckbox = (nameVal) => Array.from(checkboxes).find(c => c.dataset.name === nameVal);

        checkboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                const pName = this.dataset.name;

                // Checking create/edit/delete automatically checks 'view'
                if (this.checked) {
                    if (['create-user', 'edit-user', 'delete-user'].includes(pName)) {
                        const v = getCheckbox('view-user');
                        if(v) v.checked = true;
                    }
                    if (['create-role', 'edit-role', 'delete-role'].includes(pName)) {
                        const v = getCheckbox('view-role');
                        if(v) v.checked = true;
                    }
                }

                // Unchecking 'view' automatically unchecks create/edit/delete
                if (!this.checked) {
                    if (pName === 'view-user') {
                        ['create-user', 'edit-user', 'delete-user'].forEach(n => {
                            const c = getCheckbox(n);
                            if(c) c.checked = false;
                        });
                    }
                    if (pName === 'view-role') {
                        ['create-role', 'edit-role', 'delete-role'].forEach(n => {
                            const c = getCheckbox(n);
                            if(c) c.checked = false;
                        });
                    }
                }
            });
        });
    });
</script>

</body>
</html>
