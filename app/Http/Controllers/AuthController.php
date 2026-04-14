<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController
{

    public function showLogin()
    {
        // If already login, redirect to dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        //login first
        return view('auth.login');
    }

    /**
     * Handle login form submission.
     * Uses LoginRequest for validation.
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $remember    = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            // Regenerate session to prevent session fixation attacks
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'))
                             ->with('success', 'Welcome back, ' . Auth::user()->name . '!');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'The provided credentials do not match our records.']);
    }

    /**
     * Show the registration form.
     */
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.register');
    }

    /**
     * Handle registration form submission.
     * Uses RegisterRequest for validation.
     */
    public function register(RegisterRequest $request)
    {
        // Create user — password is auto-hashed via User model cast
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Log the newly registered user in
        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->route('dashboard')
                         ->with('success', 'Account created successfully! Welcome, ' . $user->name . '!');
    }

    /**
     * Show the dashboard (protected page after login).
     */
    public function dashboard()
    {
        return view('auth.dashboard', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        //destroy the session
        $request->session()->invalidate();
        //regenerate csrf token
        $request->session()->regenerateToken();

        return redirect()->route('login')
                         ->with('success', 'You have been logged out successfully.');
    }
}
