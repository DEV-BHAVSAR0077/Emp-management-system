<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
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

    // Create a new user page
    Route::get('/users/create', [UserController::class, 'create'])
         ->name('users.create');

    // Edit a user page
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])
         ->name('users.edit');

    // Update a user (own profile for regular users; any user for admin/HR)
    Route::put('/users/{user}', [UserController::class, 'update'])
         ->name('users.update');

    // Delete a user (admin/HR only — enforced in controller)
    Route::delete('/users/{user}', [UserController::class, 'destroy'])
         ->name('users.destroy');

    // ── Role CRUD (admin only — enforced in controller) ───────────────────────
    Route::get('/roles/create', [RoleController::class, 'create'])
         ->name('roles.create');

    Route::post('/roles', [RoleController::class, 'store'])
         ->name('roles.store');

    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])
         ->name('roles.edit');

    Route::put('/roles/{role}', [RoleController::class, 'update'])
         ->name('roles.update');

    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])
         ->name('roles.destroy');
});
