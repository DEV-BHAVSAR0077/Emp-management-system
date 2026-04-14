<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="@yield('meta_description', 'Secure login and registration portal.')">
    <title>@yield('title', 'Auth') — {{ config('app.name') }}</title>

    {{-- Google Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet" />

    <style>
        /* ── Reset & Base ────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:           #f4f5f7;
            --card-bg:      #ffffff;
            --border:       #dde1e7;
            --text:         #1a1d23;
            --text-muted:   #6b7280;
            --label:        #374151;
            --input-bg:     #f9fafb;
            --input-border: #d1d5db;
            --input-focus:  #4b5563;
            --btn-bg:       #374151;
            --btn-hover:    #1f2937;
            --btn-text:     #ffffff;
            --link:         #4b5563;
            --link-hover:   #1f2937;
            --error:        #b91c1c;
            --success:      #15803d;
            --success-bg:   #f0fdf4;
            --error-bg:     #fef2f2;
            --radius:       8px;
            --shadow:       0 1px 4px rgba(0,0,0,.08), 0 4px 16px rgba(0,0,0,.06);
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

        /* ── Layout ──────────────────────────────────── */
        .auth-wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        /* ── Brand header ────────────────────────────── */
        .auth-brand {
            text-align: center;
            margin-bottom: 1.75rem;
        }

        .auth-brand .brand-name {
            font-size: 1.4rem;
            font-weight: 600;
            letter-spacing: -.3px;
            color: var(--text);
        }

        .auth-brand .brand-sub {
            font-size: .82rem;
            color: var(--text-muted);
            margin-top: 2px;
        }

        /* ── Card ────────────────────────────────────── */
        .auth-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 2.25rem 2.5rem;
            width: 100%;
            max-width: 440px;
        }

        .auth-card h1 {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: .3rem;
        }

        .auth-card .card-subtitle {
            font-size: .83rem;
            color: var(--text-muted);
            margin-bottom: 1.75rem;
        }

        /* ── Alerts ──────────────────────────────────── */
        .alert {
            padding: .75rem 1rem;
            border-radius: var(--radius);
            font-size: .85rem;
            margin-bottom: 1.2rem;
            border: 1px solid transparent;
        }

        .alert-success {
            background: var(--success-bg);
            color: var(--success);
            border-color: #bbf7d0;
        }

        .alert-error {
            background: var(--error-bg);
            color: var(--error);
            border-color: #fecaca;
        }

        /* ── Form elements ───────────────────────────── */
        .form-group {
            margin-bottom: 1.1rem;
        }

        .form-group label {
            display: block;
            font-size: .83rem;
            font-weight: 500;
            color: var(--label);
            margin-bottom: .35rem;
        }

        .form-group input {
            display: block;
            width: 100%;
            padding: .6rem .85rem;
            font-size: .92rem;
            font-family: inherit;
            background: var(--input-bg);
            border: 1px solid var(--input-border);
            border-radius: 6px;
            color: var(--text);
            transition: border-color .18s, box-shadow .18s;
            outline: none;
        }

        .form-group input:focus {
            border-color: var(--input-focus);
            box-shadow: 0 0 0 3px rgba(75,85,99,.12);
        }

        .form-group input.input-error {
            border-color: var(--error);
        }

        .field-error {
            font-size: .78rem;
            color: var(--error);
            margin-top: .3rem;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* ── Checkbox row (remember me) ───────────────── */
        .check-row {
            display: flex;
            align-items: center;
            gap: .5rem;
            margin-bottom: 1.4rem;
        }

        .check-row input[type="checkbox"] {
            width: 15px;
            height: 15px;
            accent-color: var(--btn-bg);
            cursor: pointer;
        }

        .check-row label {
            font-size: .84rem;
            color: var(--text-muted);
            cursor: pointer;
        }

        /* ── Submit button ───────────────────────────── */
        .btn-submit {
            display: block;
            width: 100%;
            padding: .7rem;
            font-size: .92rem;
            font-weight: 500;
            font-family: inherit;
            background: var(--btn-bg);
            color: var(--btn-text);
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background .18s;
            letter-spacing: .2px;
        }

        .btn-submit:hover { background: var(--btn-hover); }
        .btn-submit:active { transform: scale(.99); }

        /* ── Footer link ─────────────────────────────── */
        .auth-footer {
            text-align: center;
            margin-top: 1.4rem;
            font-size: .84rem;
            color: var(--text-muted);
        }

        .auth-footer a {
            color: var(--link);
            font-weight: 500;
            text-decoration: none;
            border-bottom: 1px solid transparent;
            transition: border-color .15s, color .15s;
        }

        .auth-footer a:hover {
            color: var(--link-hover);
            border-bottom-color: var(--link-hover);
        }

        /* ── Divider ─────────────────────────────────── */
        .divider {
            border: none;
            border-top: 1px solid var(--border);
            margin: 1.4rem 0;
        }

        /* ── Password hint ───────────────────────────── */
        .hint {
            font-size: .75rem;
            color: var(--text-muted);
            margin-top: .3rem;
        }

        /* ── Responsive ──────────────────────────────── */
        @media (max-width: 480px) {
            .auth-card { padding: 1.75rem 1.5rem; }
        }
    </style>
</head>
<body>

<div class="auth-wrapper">

    {{-- Brand / logo area --}}
    <div class="auth-brand">
        <div class="brand-name">{{ config('app.name', 'MyApp') }}</div>
        <div class="brand-sub">Secure Portal</div>
    </div>

    {{-- Page-level flash messages (success) --}}
    @if (session('success'))
        <div class="alert alert-success" style="max-width:440px; width:100%;" role="alert">
            {{ session('success') }}
        </div>
    @endif

    {{-- Page content --}}
    @yield('content')

</div>

</body>
</html>
