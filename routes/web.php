<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// ─── Guest-only routes (accessible only when NOT logged in) ───────────────────
Route::middleware('guest')->group(function () {

    // Login
    Route::get('/login', [AuthController::class, 'showLogin'])
         ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
         ->name('login.post');

    // Register
    Route::get('/register', [AuthController::class, 'showRegister'])
         ->name('register');

    Route::post('/register', [AuthController::class, 'register'])
         ->name('register.post');
});

// ─── Authenticated routes (accessible only when logged in) ────────────────────
Route::middleware('auth')->group(function () {

    // Dashboard (with user list + search)
    Route::get('/dashboard', [UserController::class, 'index'])
         ->name('dashboard');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])
         ->name('logout');

    // ── User CRUD ─────────────────────────────────────────────────────────────

    // Create a new user (admin/HR only — enforced in controller)
    Route::post('/users', [UserController::class, 'store'])
         ->name('users.store');

    // Fetch single user as JSON (for edit modal)
    Route::get('/users/{user}', [UserController::class, 'show'])
         ->name('users.show');

    // Update a user (own profile for regular users; any user for admin/HR)
    Route::put('/users/{user}', [UserController::class, 'update'])
         ->name('users.update');

    // Delete a user (admin/HR only — enforced in controller)
    Route::delete('/users/{user}', [UserController::class, 'destroy'])
         ->name('users.destroy');
});
