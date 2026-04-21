<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    // Core system roles that cannot be deleted
    const PROTECTED_ROLES = ['admin'];

    //Show the Create Role page.
     
    public function create()
    {
        if (! Auth::user()->isAdmin()) {
            abort(403, 'Only admins can manage roles.');
        }

        return view('roles.create_role', [
            'user' => Auth::user(),
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    // Store a newly created role.
    public function store(Request $request)
    {
        if (! Auth::user()->isAdmin()) {
            abort(403, 'Only admins can manage roles.');
        }

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:50', 'unique:roles,name'],
            'description' => ['nullable', 'string', 'max:255'],
            'color'       => ['required', 'string', 'max:20'],
            'level'       => ['required', Rule::in(['admin', 'hr', 'user'])],
        ]);

        Role::create($validated);

        return redirect()->route('dashboard', ['tab' => 'roles'])
                         ->with('success', 'Role "' . $validated['name'] . '" created successfully.');
    }

    //Show the Edit Role page.
     
    public function edit(Role $role)
    {
        if (! Auth::user()->isAdmin()) {
            abort(403, 'Only admins can manage roles.');
        }

        return view('roles.edit_role', [
            'user'  => Auth::user(),
            'role'  => $role,
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    // Update an existing role.
    public function update(Request $request, Role $role)
    {
        if (! Auth::user()->isAdmin()) {
            abort(403, 'Only admins can manage roles.');
        }

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:50', Rule::unique('roles', 'name')->ignore($role->id)],
            'description' => ['nullable', 'string', 'max:255'],
            'color'       => ['required', 'string', 'max:20'],
            'level'       => ['required', Rule::in(['admin', 'hr', 'user'])],
        ]);

        // If the role name changed, propagate to all users who had the old name
        if ($role->name !== $validated['name']) {
            User::where('role', $role->name)->update(['role' => $validated['name']]);
        }

        $role->update($validated);

        return redirect()->route('dashboard', ['tab' => 'roles'])
                         ->with('success', 'Role "' . $validated['name'] . '" updated successfully.');
    }

    // Delete a role and reset affected users to 'User'.
    public function destroy(Role $role)
    {
        if (! Auth::user()->isAdmin()) {
            abort(403, 'Only admins can manage roles.');
        }

        if (in_array(strtolower($role->name), self::PROTECTED_ROLES, true)) {
            return redirect()->route('dashboard', ['tab' => 'roles'])
                             ->with('error', 'The "' . $role->name . '" role is a system role and cannot be deleted.');
        }

        $name     = $role->name;
        $affected = User::where('role', $role->name)->count();

        User::where('role', $role->name)->update(['role' => 'User']);
        $role->delete();

        $msg = 'Role "' . $name . '" deleted successfully.';
        if ($affected > 0) {
            $msg .= " {$affected} " . ($affected === 1 ? 'user was' : 'users were') . ' reset to the User role.';
        }

        return redirect()->route('dashboard', ['tab' => 'roles'])
                         ->with('success', $msg);
    }
}
