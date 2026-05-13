<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    //Display the dashboard with paginated user list.
     
    public function index(Request $request)
    {

        $search  = $request->input('search', '');
        $perPage = 8;

        $users = User::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        $roles    = Role::orderBy('name')->get();
        $rolesMap = $roles->keyBy('name'); // keyed by role name for easy badge lookup

        // ── Expense data for the expenses tab ─────────────────────────────────
        $expenseSearch = $request->input('expense_search', '');
        $expenseStatus = $request->input('expense_status', 'active');

        $expenses = Expense::with(['category', 'subCategory', 'user'])
            ->when($expenseStatus === 'trashed', function ($query) {
                $query->onlyTrashed();
            })
            ->when($expenseSearch, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('expense_date', 'desc')
            ->paginate(10, ['*'], 'expense_page');

        $expenseCategories = ExpenseCategory::with('subCategories')
            ->orderBy('name')
            ->get();

        return view('auth.dashboard', [
            'user'              => Auth::user(),
            'users'             => $users,
            'search'            => $search,
            'roles'             => $roles,
            'rolesMap'          => $rolesMap,
            'expenses'          => $expenses,
            'expenseSearch'     => $expenseSearch,
            'expenseStatus'     => $expenseStatus,
            'expenseCategories' => $expenseCategories,
        ]);
    }

    // Store a newly created user.
    public function store(UpdateUserRequest $request)
    {
        Gate::authorize('create-user');

        $data = [
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'User', // default
        ];

        // Admin / User with edit-role may set role on creation
        if (Auth::user()->hasPermission('edit-role') && $request->filled('role')) {
            $data['role'] = $request->role;
        }

        User::create($data);

        return redirect()->route('dashboard', ['tab' => 'emp'])
                         ->with('success', 'User created successfully.');
    }

    //Show the form for creating a new user.
     
    public function create()
    {
        Gate::authorize('create-user');

        $roles = Role::orderBy('name')->get();

        return view('employees.create_user', [
            'user'     => Auth::user(),
            'roles'    => $roles,
            'rolesMap' => $roles->keyBy('name'),
        ]);
    }

    //Show the form for editing the specified user.
     
     
    public function edit(User $user)
    {
        $authUser = Auth::user();

        // Regular users may only edit themselves
        if ($authUser->id !== $user->id) {
            Gate::authorize('edit-user');
        }

        $roles = Role::orderBy('name')->get();

        return view('employees.edit_user', [
            'user'     => $authUser,
            'editUser' => $user,
            'roles'    => $roles,
            'rolesMap' => $roles->keyBy('name'),
        ]);
    }


    // Update an existing user.
    public function update(UpdateUserRequest $request, User $user)
    {
        $authUser = Auth::user();

        // Regular users may only edit themselves
        if ($authUser->id !== $user->id) {
            Gate::authorize('edit-user');
        }

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Only users with edit-role permission can change a user's role
        if ($authUser->hasPermission('edit-role') && $request->filled('role')) {
            // Prevent admin from demoting themselves
            if ($user->id === $authUser->id) {
                if (strtolower($request->role) !== 'admin') {
                    return redirect()->route('dashboard')
                                     ->with('error', 'You cannot remove admin access from your own account.');
                }
            }
            $data['role'] = $request->role;
        }

        $user->update($data);

        return redirect()->route('dashboard', ['tab' => 'emp'])
                         ->with('success', 'User updated successfully.');
    }

    // Delete a user.
    public function destroy(User $user)
    {
        $authUser = Auth::user();

        Gate::authorize('delete-user');

        if ($user->id === $authUser->id) {
            return redirect()->route('dashboard')
                             ->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return back()->with('success', 'User deleted successfully.');
    }
}
