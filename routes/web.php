<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\ExpenseController;
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
         ->name('users.store')->middleware('permission:create-user');

    // Create a new user page
    Route::get('/users/create', [UserController::class, 'create'])
         ->name('users.create')->middleware('permission:create-user');

    // Edit a user page
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])
         ->name('users.edit')->middleware('permission:edit-user,user');

    // Update a user (own profile for regular users; any user for admin/HR)
    Route::put('/users/{user}', [UserController::class, 'update'])
         ->name('users.update')->middleware('permission:edit-user,user');

    // Delete a user (admin/HR only — enforced in controller)
    Route::delete('/users/{user}', [UserController::class, 'destroy'])
         ->name('users.destroy')->middleware('permission:delete-user');

    // ── Role CRUD (admin only — enforced in controller) ───────────────────────
    Route::get('/roles/create', [RoleController::class, 'create'])
         ->name('roles.create')->middleware('permission:create-role');

    Route::post('/roles', [RoleController::class, 'store'])
         ->name('roles.store')->middleware('permission:create-role');

    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])
         ->name('roles.edit')->middleware('permission:edit-role');

    Route::put('/roles/{role}', [RoleController::class, 'update'])
         ->name('roles.update')->middleware('permission:edit-role');

    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])
         ->name('roles.destroy')->middleware('permission:delete-role');

    // ── Expense CRUD (permission-gated in controller) ─────────────────────
    Route::get('/expenses', [ExpenseController::class, 'index'])
         ->name('expenses.index');

    Route::get('/expenses/create', [ExpenseController::class, 'create'])
         ->name('expenses.create')->middleware('permission:create-expense');

    Route::post('/expenses', [ExpenseController::class, 'store'])
         ->name('expenses.store')->middleware('permission:create-expense');

    Route::get('/expenses/{expense}/edit', [ExpenseController::class, 'edit'])
         ->name('expenses.edit')->middleware('permission:edit-expense,expense');

    Route::put('/expenses/{expense}', [ExpenseController::class, 'update'])
         ->name('expenses.update')->middleware('permission:edit-expense,expense');

    Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])
         ->name('expenses.destroy')->middleware('permission:delete-expense,expense');

    Route::post('/expenses/{expense}/restore', [ExpenseController::class, 'restore'])
         ->name('expenses.restore')->middleware('permission:delete-expense,expense');

    // ── Expense Category API (JSON — used by dynamic JS) ──────────────────
    Route::post('/expense-categories', [ExpenseCategoryController::class, 'storeCategory'])
         ->name('expense-categories.store')->middleware('permission:create-expense');

    Route::delete('/expense-categories/{category}', [ExpenseCategoryController::class, 'destroyCategory'])
         ->name('expense-categories.destroy')->middleware('permission:delete-expense');

    Route::get('/expense-categories/{category}/sub-categories', [ExpenseCategoryController::class, 'subCategories'])
         ->name('expense-categories.subs');

    Route::post('/expense-sub-categories', [ExpenseCategoryController::class, 'storeSubCategory'])
         ->name('expense-sub-categories.store')->middleware('permission:create-expense');

    Route::delete('/expense-sub-categories/{subCategory}', [ExpenseCategoryController::class, 'destroySubCategory'])
         ->name('expense-sub-categories.destroy')->middleware('permission:delete-expense');
});
