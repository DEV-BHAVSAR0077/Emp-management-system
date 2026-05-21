<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AVController;
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

    // Dashboard
    Route::get('/dashboard', [UserController::class, 'dashboard'])
         ->name('dashboard');

    // Users List
    Route::get('/users', [UserController::class, 'index'])
         ->name('users.index')->middleware('permission:view-user');

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
    Route::get('/roles', [RoleController::class, 'index'])
         ->name('roles.index')->middleware('permission:view-role');

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
         ->name('expenses.index')->middleware('permission:view-expense');

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
         ->name('expenses.restore')->middleware('permission:delete-expense,expense')->withTrashed();

    // ── Category Module ───────────────────────────────────────────────────
    Route::get('/categories', [CategoryController::class, 'index'])
         ->name('categories.index')->middleware('permission:view-category');

    Route::get('/categories/create', [CategoryController::class, 'create'])
         ->name('categories.create')->middleware('permission:create-category');
         
    Route::post('/categories', [CategoryController::class, 'store'])
         ->name('categories.store')->middleware('permission:create-category');
    
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])
         ->name('categories.edit')->middleware('permission:edit-category');
         
    Route::put('/categories/{category}', [CategoryController::class, 'update'])
         ->name('categories.update')->middleware('permission:edit-category');
         
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])
         ->name('categories.destroy')->middleware('permission:delete-category');
         
    Route::delete('/sub-categories/{subCategory}', [CategoryController::class, 'destroySubCategory'])
         ->name('sub-categories.destroy')->middleware('permission:delete-category');
         
    Route::get('/categories/{category}/sub-categories', [CategoryController::class, 'getSubCategories'])
         ->name('categories.subs');

    // ── Agency & Vendor Module ────────────────────────────────────────────
    Route::get('/agency-vendors/{agencyVendor}/payments', [AVController::class, 'getPayments'])->name('agency_vendors.payments');
    Route::resource('agency-vendors', AVController::class)
         ->only(['index', 'create', 'store', 'edit', 'update', 'destroy'])
         ->names('agency_vendors');

    // ── Payment Module ────────────────────────────────────────────────────
    Route::resource('payments', PaymentController::class)
         ->only(['index', 'create', 'store', 'edit', 'update', 'destroy'])
         ->names('payments');

});
