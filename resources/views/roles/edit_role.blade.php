<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Role — {{ config('app.name') }}</title>
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
            --danger-border: #fecaca;
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
        .form-group input,
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
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #9ca3af;
            box-shadow: 0 0 0 3px rgba(156,163,175,.15);
        }
        .form-group input.input-error,
        .form-group select.input-error { border-color: #f87171; }

        .field-error { display: flex; align-items: center; gap: .32rem; font-size: .78rem; color: var(--danger); margin-top: .35rem; }
        .field-hint  { font-size: .76rem; color: var(--text-muted); margin-top: .28rem; }

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

        /* ── Live Preview Card ────────────────────────────────────── */
        .preview-wrap  { margin-bottom: 1.5rem; }
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
        .btn-ghost   { background: transparent; color: var(--text-muted); border-color: var(--border); }
        .btn-ghost:hover   { background: var(--bg); color: var(--text); }
        .btn-danger  { background: var(--danger-bg); color: var(--danger); border-color: var(--danger-border); }
        .btn-danger:hover  { background: #fee2e2; }

        .form-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: .6rem;
            margin-top: 1.75rem;
            padding-top: 1.25rem;
            border-top: 1px solid var(--border);
        }
        .form-actions-right { display: flex; gap: .6rem; }

        /* ── Role pill ────────────────────────────────────────────── */
        .role-pill { display: inline-block; padding: .12rem .5rem; border-radius: 99px; font-size: .72rem; font-weight: 600; text-transform: uppercase; letter-spacing: .03em; }

        /* ── Protected notice ─────────────────────────────────────── */
        .notice {
            display: flex;
            align-items: center;
            gap: .5rem;
            padding: .65rem 1rem;
            border-radius: 6px;
            font-size: .82rem;
            background: #fffbeb;
            color: #92400e;
            border: 1px solid #fde68a;
            margin-bottom: 1.25rem;
        }

        /* ── Confirm delete backdrop ──────────────────────────────── */
        .modal-backdrop {
            display: none;
            position: fixed; inset: 0;
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
            width: 100%; max-width: 400px;
            padding: 2rem;
            text-align: center;
            animation: modal-in .2s ease;
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
            <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor" style="color:#1d4ed8;">
                <path d="M2.695 14.763l-1.262 3.154a.5.5 0 00.65.65l3.155-1.262a4 4 0 001.343-.885L17.5 5.5a2.121 2.121 0 00-3-3L3.58 13.42a4 4 0 00-.885 1.343z"/>
            </svg>
            <h2>Edit Role: {{ $role->name }}</h2>
        </div>

        @php $isProtected = strtolower($role->name) === 'admin'; @endphp

        @if($isProtected)
        <div class="notice">
            <svg width="15" height="15" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd"/></svg>
            This is a system role. You can update its color and description, but the name and permission level are locked.
        </div>
        @endif

        {{-- Live Preview --}}
        <div class="preview-wrap">
            <div class="preview-label">Live Preview</div>
            <div class="role-preview">
                <div class="preview-accent" id="preview-accent" style="background:{{ $role->color }};"></div>
                <div class="preview-body">
                    <div class="preview-icon" id="preview-icon" style="background:{{ $role->color }}22; color:{{ $role->color }};">🛡️</div>
                    <div class="preview-name" id="preview-name">{{ $role->name }}</div>
                    <div class="preview-desc"  id="preview-desc">{{ $role->description ?: 'Role description…' }}</div>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('roles.update', $role) }}" novalidate>
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">Role Name <span style="color:var(--danger);">*</span></label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    maxlength="50"
                    value="{{ old('name', $role->name) }}"
                    class="{{ $errors->has('name') ? 'input-error' : '' }}"
                    {{ $isProtected ? 'readonly' : 'required' }}
                    oninput="document.getElementById('preview-name').textContent = this.value || 'Role Name'"
                    style="{{ $isProtected ? 'background:var(--bg); color:var(--text-muted); cursor:not-allowed;' : '' }}"
                />
                @error('name')<span class="field-error">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea
                    id="description"
                    name="description"
                    maxlength="255"
                    placeholder="What does this role do?"
                    oninput="document.getElementById('preview-desc').textContent = this.value || 'Role description…'"
                >{{ old('description', $role->description) }}</textarea>
                @error('description')<span class="field-error">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label for="level">Permission Level <span style="color:var(--danger);">*</span></label>
                <select id="level" name="level" {{ $isProtected ? 'disabled' : '' }}
                    style="{{ $isProtected ? 'background:var(--bg); color:var(--text-muted); cursor:not-allowed;' : '' }}">
                    <option value="user"  {{ old('level', $role->level) === 'user'  ? 'selected' : '' }}>Standard (User)  — view only, edit own profile</option>
                    <option value="hr"    {{ old('level', $role->level) === 'hr'    ? 'selected' : '' }}>HR              — can add, edit &amp; delete users</option>
                    <option value="admin" {{ old('level', $role->level) === 'admin' ? 'selected' : '' }}>Admin           — full access including roles</option>
                </select>
                {{-- Hidden field to preserve level value when select is disabled --}}
                @if($isProtected)
                <input type="hidden" name="level" value="{{ $role->level }}" />
                @endif
                <span class="field-hint">Controls what actions users with this role can perform.</span>
                @error('level')<span class="field-error">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label>Card Color <span style="color:var(--danger);">*</span></label>
                <input type="hidden" id="color" name="color" value="{{ old('color', $role->color) }}" />
                <div class="color-swatches" id="color-swatches"></div>
                @error('color')<span class="field-error">{{ $message }}</span>@enderror
            </div>

            <div class="form-actions">
                {{-- Delete button (left side) — hidden for protected roles --}}
                @if(!$isProtected)
                <button type="button" class="btn btn-danger" onclick="document.getElementById('modal-delete').classList.add('open')">
                    <svg width="13" height="13" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd"/></svg>
                    Delete Role
                </button>
                @else
                <span></span>
                @endif

                <div class="form-actions-right">
                    <a href="{{ route('dashboard', ['tab' => 'roles']) }}" class="btn btn-ghost">Cancel</a>
                    <button type="submit" class="btn btn-primary" id="btn-save-role">
                        <svg width="13" height="13" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                        Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Delete Confirm Modal --}}
@if(!$isProtected)
<div class="modal-backdrop" id="modal-delete">
    <div class="modal">
        <div class="confirm-icon">
            <svg width="22" height="22" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd"/></svg>
        </div>
        <h3 style="font-size:1rem; font-weight:600; margin-bottom:.4rem;">Delete Role</h3>
        <p style="font-size:.86rem; color:var(--text-muted);">
            Are you sure you want to delete <strong style="color:var(--text);">{{ $role->name }}</strong>?<br>
            All users with this role will be reset to <strong style="color:var(--text);">User</strong>.
        </p>
        <form method="POST" action="{{ route('roles.destroy', $role) }}">
            @csrf
            @method('DELETE')
            <div class="confirm-footer">
                <button type="button" class="btn btn-ghost" onclick="document.getElementById('modal-delete').classList.remove('open')">Cancel</button>
                <button type="submit" class="btn btn-danger">Yes, Delete</button>
            </div>
        </form>
    </div>
</div>
@endif

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

    // Close modal on backdrop click
    document.getElementById('modal-delete')?.addEventListener('click', function(e) {
        if (e.target === this) this.classList.remove('open');
    });

    // Close on Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') document.getElementById('modal-delete')?.classList.remove('open');
    });

    renderSwatches();
    updatePreview(selectedColor);
</script>

</body>
</html>
