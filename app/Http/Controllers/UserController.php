<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Models\Category;
use App\Models\Expense;
use App\Models\Role;
use App\Models\User;
use App\Models\AgencyVendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Display the paginated user list.
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
        $rolesMap = $roles->keyBy('name');

        return view('employees.index', [
            'user'     => Auth::user(),
            'users'    => $users,
            'search'   => $search,
            'rolesMap' => $rolesMap,
        ]);
    }

    // Store a newly created user.
    public function store(UpdateUserRequest $request)
    {
        $defaultRoleId = Role::where('name', 'User')->value('id');
        $data = [
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role_id'  => $defaultRoleId,
        ];

        // Admin / User with edit-role may set role on creation
        if (Auth::user()->hasPermission('edit-role') && $request->filled('role_id')) {
            $data['role_id'] = $request->role_id;
        }

        User::create($data);

        return redirect()->route('users.index')
                         ->with('success', 'User created successfully.');
    }

    //Show the form for creating a new user.
     
    public function create()
    {
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

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Only users with edit-role permission can change a user's role
        if ($authUser->hasPermission('edit-role') && $request->filled('role_id')) {
            // Prevent admin from demoting themselves
            if ($user->id === $authUser->id) {
                $requestedRole = Role::find($request->role_id);
                if (!$requestedRole || strtolower($requestedRole->name) !== 'admin') {
                    return redirect()->route('users.index')
                                     ->with('error', 'You cannot remove admin access from your own account.');
                }
            }
            $data['role_id'] = $request->role_id;
        }

        $user->update($data);

        return redirect()->route('users.index')
                         ->with('success', 'User updated successfully.');
    }

    // Delete a user.
    public function destroy(User $user)
    {
        $authUser = Auth::user();

        if ($user->id === $authUser->id) {
            return redirect()->route('users.index')
                             ->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return back()->with('success', 'User deleted successfully.');
    }
}
