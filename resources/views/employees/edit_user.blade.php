<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit User — {{ config('app.name') }}</title>
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
            --admin-color:#b45309;
            --admin-bg:   #fef3c7;
            --hr-color:   #6d28d9;
            --hr-bg:      #f5f3ff;
        }

        html, body {
            height: 100%;
            font-family: 'Inter', system-ui, sans-serif;
            background: var(--bg);
            color: var(--text);
            font-size: 15px;
            line-height: 1.6;
        }

        .navbar {
            background: var(--card-bg);
            border-bottom: 1px solid var(--border);
            padding: .9rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
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

        .main {
            max-width: 600px;
            margin: 2.5rem auto;
            padding: 0 1.5rem;
        }

        .panel {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 2rem;
        }

        .panel h2 {
            font-size: 1.25rem;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }

        /* Forms */
        .form-group { margin-bottom: 1.1rem; }
        .form-group label { display: block; font-size: .82rem; font-weight: 500; margin-bottom: .38rem; }
        .form-group input, .form-group select {
            width: 100%; padding: .52rem .75rem; font-size: .88rem; font-family: inherit;
            border: 1px solid var(--border); border-radius: 6px; outline: none; transition: .18s;
        }
        .form-group input:focus, .form-group select:focus { border-color: #9ca3af; box-shadow: 0 0 0 3px rgba(156,163,175,.15); }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: .8rem; }
        
        .field-error { display: flex; align-items: center; gap: .32rem; font-size: .78rem; color: var(--danger); margin-top: .35rem; }
        .field-hint { font-size: .76rem; color: var(--text-muted); margin-top: .28rem; display: block; }
        .input-error { border-color: #f87171 !important; }

        .btn { padding: .42rem .9rem; font-size: .84rem; font-family: inherit; font-weight: 500; border-radius: 6px; cursor: pointer; border: 1px solid transparent; text-decoration: none; display: inline-flex; justify-content: center; }
        .btn-primary { background: var(--btn-bg); color: var(--btn-text); }
        .btn-primary:hover { background: var(--btn-hover); }
        .btn-ghost { background: transparent; color: var(--text-muted); border-color: var(--border); }
        .btn-ghost:hover { background: var(--bg); color: var(--text); }
        
        .role-pill { padding: .12rem .5rem; border-radius: 99px; font-size: .72rem; font-weight: 600; text-transform: uppercase; }
        .role-pill-admin { background: var(--admin-bg); color: var(--admin-color); }
        .role-pill-hr { background: var(--hr-bg); color: var(--hr-color); }
        .role-pill-user { background: #f3f4f6; color: var(--text-muted); }
        
        .form-actions { display: flex; justify-content: flex-end; gap: .6rem; margin-top: 1.5rem; padding-top: 1.2rem; border-top: 1px solid var(--border); }
    </style>
</head>
<body>

<nav class="navbar">
    <a href="{{ route('dashboard') }}" class="brand">{{ config('app.name', 'MyApp') }}</a>
    <div class="nav-right">
        <span style="font-size: .84rem; color: var(--text-muted); display: flex; align-items: center; gap: .45rem;">
            {{ $user->email }}
            @if($user->isAdmin()) <span class="role-pill role-pill-admin">Admin</span>
            @elseif($user->isHr()) <span class="role-pill role-pill-hr">HR</span>
            @else <span class="role-pill role-pill-user">User</span>
            @endif
        </span>
        <a href="{{ route('dashboard') }}" class="btn btn-ghost" style="padding: .3rem .6rem;">Back to Dashboard</a>
    </div>
</nav>

<div class="main">
    <div class="panel">
        <h2>Edit User: {{ $editUser->name }}</h2>
        <form method="POST" action="{{ route('users.update', $editUser) }}" novalidate>
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" placeholder="John Doe" value="{{ old('name', $editUser->name) }}" class="{{ $errors->has('name') ? 'input-error' : '' }}" required />
                @error('name')<span class="field-error">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="you@example.com" value="{{ old('email', $editUser->email) }}" class="{{ $errors->has('email') ? 'input-error' : '' }}" required />
                @error('email')<span class="field-error">{{ $message }}</span>@enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="password">New Password</label>
                    <input type="password" id="password" name="password" placeholder="Leave blank to keep" class="{{ $errors->has('password') ? 'input-error' : '' }}" />
                    <span class="field-hint">Leave empty to keep current password.</span>
                    @error('password')<span class="field-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Repeat new password" />
                </div>
            </div>

            @if($user->isAdmin())
            <div class="form-group">
                <label for="role">Role</label>
                <select id="role" name="role">
                    <option value="user" {{ old('role', $editUser->role) === 'user' ? 'selected' : '' }}>User</option>
                    <option value="hr"   {{ old('role', $editUser->role) === 'hr'    ? 'selected' : '' }}>HR</option>
                    <option value="admin"{{ old('role', $editUser->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
                @error('role')<span class="field-error">{{ $message }}</span>@enderror
                <span class="field-hint">Changing role grants or revokes management permissions.</span>
            </div>
            @endif

            <div class="form-actions">
                <a href="{{ route('dashboard', ['tab' => 'emp']) }}" class="btn btn-ghost">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>