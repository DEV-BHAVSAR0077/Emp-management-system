<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - {{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet" />
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #f4f5f7; color: #1a1d23; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .card { background: #fff; padding: 2.5rem 2rem; border-radius: 8px; box-shadow: 0 4px 16px rgba(0,0,0,.06); width: 100%; max-width: 400px; border: 1px solid #dde1e7; }
        .card h1 { font-size: 1.5rem; font-weight: 600; margin-bottom: 1.5rem; text-align: center; }
        .form-group { margin-bottom: 1.25rem; }
        .form-group label { display: block; font-size: .875rem; font-weight: 500; margin-bottom: .5rem; }
        .form-group input[type="text"], .form-group input[type="email"], .form-group input[type="password"] { width: 100%; padding: .6rem .75rem; border: 1px solid #dde1e7; border-radius: 6px; font-family: inherit; }
        .form-group input:focus { outline: none; border-color: #9ca3af; box-shadow: 0 0 0 3px rgba(156,163,175,.15); }
        .btn { width: 100%; padding: .65rem; background: #374151; color: #fff; border: none; border-radius: 6px; font-weight: 500; cursor: pointer; transition: background .15s; font-family: inherit; font-size: 1rem; margin-top: .5rem; }
        .btn:hover { background: #1f2937; }
        .error { color: #b91c1c; font-size: .75rem; margin-top: .4rem; display: block; }
        .links { margin-top: 1.5rem; text-align: center; font-size: .875rem; }
        .links a { color: #374151; text-decoration: none; }
        .links a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="card">
    <h1>Register</h1>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="form-group">
            <label for="name">Name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus>
            @error('name')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required>
            @error('email')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input id="password" type="password" name="password" required>
            @error('password')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation">Confirm Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required>
            @error('password_confirmation')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="btn">Register</button>
    </form>

    <div class="links">
        <a href="{{ route('login') }}">Already registered? Log in</a>
    </div>
</div>

</body>
</html>
