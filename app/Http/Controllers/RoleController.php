<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    // Core system roles that cannot be deleted
    const PROTECTED_ROLES = ['admin'];

    //Show the Create Role page.
     
    // public function create()
    // {
    //     if (! Auth::user()->isAdmin()) {
    //         abort(403, 'Only admins can manage roles.');
    //     }

    //     return view('roles.create_role', [
    //         'user' => Auth::user(),
    //         'roles' => Role::orderBy('name')->get(),
    //     ]);
    // }

    // Store a newly created role.
    public function store(Request $request)
    {
        Gate::authorize('create-role');

        $request->validate([
            'name' => 'required|unique:roles,name',
            'description' => 'nullable|max:255',
            'color' => 'required'
        ]);

        $role = Role::create([
            'name' => $request->name,
            'description' => $request->description,
            'color' => $request->color,
        ]);

        $role->permissions()->attach($request->permissions ?? []);

        return redirect()->route('dashboard', ['tab' => 'roles'])
            ->with('success', 'Role Created Successfully');
    }

    //Show the Edit Role page.
     
    public function edit(Role $role)
    {
        Gate::authorize('edit-role');

        if (in_array(strtolower($role->name), self::PROTECTED_ROLES, true)) {
            return redirect()->route('dashboard', ['tab' => 'roles'])
                             ->with('error', 'The "' . $role->name . '" role is a system role and cannot be edited.');
        }

        $permissions = Permission::all();
        $roles = Role::orderBy('name')->get();
        $user = Auth::user();

        return view('roles.edit_role', compact('role', 'permissions', 'roles', 'user'));
    }

    // Update an existing role.
    public function update(Request $request, Role $role)
    {
        Gate::authorize('edit-role');

        if (in_array(strtolower($role->name), self::PROTECTED_ROLES, true)) {
            return redirect()->route('dashboard', ['tab' => 'roles'])
                             ->with('error', 'The "' . $role->name . '" role is a system role and cannot be edited.');
        }

        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
            'description' => 'nullable|max:255',
            'color' => 'required',
        ]);

        $oldName = $role->name;

        $role->update([
            'name' => $request->name,
            'description' => $request->description,
            'color' => $request->color,
        ]);

        if ($oldName !== $request->name) {
            User::where('role', $oldName)->update([
                'role' => $request->name
            ]);
        }

        $role->permissions()->sync($request->permissions ?? []);

        return redirect()->route('dashboard', ['tab' => 'roles'])
            ->with('success', 'Role Updated Successfully');
    }

    // Delete a role and reset affected users to 'User'.
    public function destroy(Role $role)
    {
        Gate::authorize('delete-role');

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

    public function create()
    {
        Gate::authorize('create-role');
        $permissions = Permission::all();
        $roles = Role::orderBy('name')->get();
        $user = Auth::user();

        return view('roles.create_role', compact(
            'permissions',
            'roles',
            'user'
        ));
    }
}
