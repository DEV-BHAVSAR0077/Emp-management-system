<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    // Core system roles that cannot be deleted
    const PROTECTED_ROLES = ['admin'];

    // Display the roles list.
    public function index()
    {
        $roles = Role::orderBy('name')->get();
        return view('roles.index', [
            'user'  => Auth::user(),
            'roles' => $roles,
        ]);
    }

    // Store a newly created role.
    public function store(Request $request)
    {
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

        return redirect()->route('roles.index')
            ->with('success', 'Role Created Successfully');
    }

    //Show the Edit Role page.
     
    public function edit(Role $role)
    {
        if (in_array(strtolower($role->name), self::PROTECTED_ROLES, true)) {
            return redirect()->route('roles.index')
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
        if (in_array(strtolower($role->name), self::PROTECTED_ROLES, true)) {
            return redirect()->route('roles.index')
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

        $role->permissions()->sync($request->permissions ?? []);

        return redirect()->route('roles.index')
            ->with('success', 'Role Updated Successfully');
    }

    // Delete a role and reset affected users to 'User'.
    public function destroy(Role $role)
    {
        if (in_array(strtolower($role->name), self::PROTECTED_ROLES, true)) {
            return redirect()->route('roles.index')
                             ->with('error', 'The "' . $role->name . '" role is a system role and cannot be deleted.');
        }

        $name     = $role->name;
        $affected = User::where('role_id', $role->id)->count();

        $defaultRoleId = Role::where('name', 'User')->value('id');
        User::where('role_id', $role->id)->update(['role_id' => $defaultRoleId]);
        $role->delete();

        $msg = 'Role "' . $name . '" deleted successfully.';
        if ($affected > 0) {
            $msg .= " {$affected} " . ($affected === 1 ? 'user was' : 'users were') . ' reset to the User role.';
        }

        return redirect()->route('roles.index')
                         ->with('success', $msg);
    }

    public function create()
    {
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
