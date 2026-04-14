<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display the dashboard with paginated user list.
     */
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $perPage = 2;

        $users = User::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return view('auth.dashboard', [
            'user'   => Auth::user(),
            'users'  => $users,
            'search' => $search,
        ]);
    }

    /**
     * Store a newly created user.
     * Only admin/HR may create users from the dashboard.
     */
    public function store(UpdateUserRequest $request)
    {
        if (! Auth::user()->canManageUsers()) {
            abort(403, 'You are not authorized to create users.');
        }

        $data = [
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ];

        // Admin may set role on creation
        if (Auth::user()->isAdmin() && $request->filled('role')) {
            $data['role'] = $request->role;
        }

        User::create($data);

        return redirect()->route('dashboard')
                         ->with('success', 'User created successfully.');
    }

    /**
     * Return a single user as JSON (for the edit modal).
     */
    public function show(User $user)
    {
        return response()->json([
            'id'    => $user->id,
            'name'  => $user->name,
            'email' => $user->email,
            'role'  => $user->role,
        ]);
    }

    /**
     * Update an existing user.
     *
     * Permission rules:
     *  - Admin/HR  → can update any user's name, email, password.
     *  - Admin only → can also change role.
     *  - Regular user → can only update their OWN profile (name, email, password).
     *  - Regular user editing someone else → 403.
     */
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
            // Prevent the last admin from demoting themselves
            if ($user->id === $authUser->id && $request->role !== 'admin') {
                return redirect()->route('dashboard')
                                 ->with('error', 'You cannot demote your own admin account.');
            }
            $data['role'] = $request->role;
        }

        $user->update($data);

        return redirect()->route('dashboard')
                         ->with('success', 'User updated successfully.');
    }

    /**
     * Delete a user.
     * Only admin/HR may delete; self-delete is prevented.
     */
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
