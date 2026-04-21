<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        return view('auth.dashboard', [
            'user'     => Auth::user(),
            'users'    => $users,
            'search'   => $search,
            'roles'    => $roles,
            'rolesMap' => $rolesMap,
        ]);
    }

    // Store a newly created user.
    public function store(UpdateUserRequest $request)
    {
        if (! Auth::user()->canManageUsers()) {
            abort(403, 'You are not authorized to create users.');
        }

        $data = [
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'User', // default
        ];

        // Admin may set role on creation
        if (Auth::user()->isAdmin() && $request->filled('role')) {
            $data['role'] = $request->role;
        }

        User::create($data);

        return redirect()->route('dashboard', ['tab' => 'emp'])
                         ->with('success', 'User created successfully.');
    }

    //Show the form for creating a new user.
     
    public function create()
    {
        if (! Auth::user()->canManageUsers()) {
            abort(403, 'You are not authorized to create users.');
        }

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
        if (! $authUser->canManageUsers() && $authUser->id !== $user->id) {
            abort(403, 'You are not authorized to edit other users.');
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
        if (! $authUser->canManageUsers() && $authUser->id !== $user->id) {
            abort(403, 'You are not authorized to edit other users.');
        }

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Only admin can change role
        if ($authUser->isAdmin() && $request->filled('role')) {
            // Prevent admin from demoting themselves
            if ($user->id === $authUser->id) {
                $newLevel = Role::where('name', $request->role)->value('level');
                if ($newLevel !== 'admin') {
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

        if (! $authUser->canManageUsers()) {
            abort(403, 'You are not authorized to delete users.');
        }

        if ($user->id === $authUser->id) {
            return redirect()->route('dashboard')
                             ->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('dashboard')
                         ->with('success', 'User deleted successfully.');
    }
}
