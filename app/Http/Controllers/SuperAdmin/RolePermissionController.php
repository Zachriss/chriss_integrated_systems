<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use App\Models\AuditTrail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RolePermissionController extends Controller
{
    public function roles()
    {
        $roles = Role::with('permissions')->latest()->get();
        return view('super-admin.roles.index', compact('roles'));
    }

    public function createRole()
    {
        $permissions = Permission::all()->groupBy('module');
        return view('super-admin.roles.create', compact('permissions'));
    }

    public function storeRole(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
        ]);

        if (!empty($validated['permissions'])) {
            $role->permissions()->sync($validated['permissions']);
        }

        AuditTrail::create([
            'actor_id' => auth()->id(),
            'actor_type' => User::class,
            'action' => 'create',
            'module' => 'Roles & Permissions',
            'description' => "Created role: {$role->name}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('super-admin.roles.index')->with('success', 'Role created successfully.');
    }

    public function editRole(Role $role)
    {
        $role->load('permissions');
        $permissions = Permission::all()->groupBy('module');
        return view('super-admin.roles.edit', compact('role', 'permissions'));
    }

    public function updateRole(Request $request, Role $role)
    {
        if ($role->slug === 'super-admin') {
            return back()->with('error', 'Cannot modify the Super Admin role.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
        ]);

        if ($request->has('permissions')) {
            $role->permissions()->sync($validated['permissions'] ?? []);
        }

        AuditTrail::create([
            'actor_id' => auth()->id(),
            'actor_type' => User::class,
            'action' => 'update',
            'module' => 'Roles & Permissions',
            'description' => "Updated role: {$role->name}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('super-admin.roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroyRole(Request $request, Role $role)
    {
        if ($role->slug === 'super-admin') {
            return back()->with('error', 'Cannot delete the Super Admin role.');
        }

        AuditTrail::create([
            'actor_id' => auth()->id(),
            'actor_type' => User::class,
            'action' => 'delete',
            'module' => 'Roles & Permissions',
            'description' => "Deleted role: {$role->name}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $role->permissions()->detach();
        $role->delete();

        return redirect()->route('super-admin.roles.index')->with('success', 'Role deleted successfully.');
    }

    public function permissions()
    {
        $permissions = Permission::all()->groupBy('module');
        $roles = Role::with('permissions')->get();
        return view('super-admin.roles.permissions', compact('permissions', 'roles'));
    }

    public function assignUserRole(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id',
        ]);

        $user = User::findOrFail($validated['user_id']);
        $user->roles()->syncWithoutDetaching([$validated['role_id']]);

        $role = Role::find($validated['role_id']);

        AuditTrail::create([
            'actor_id' => auth()->id(),
            'actor_type' => User::class,
            'action' => 'update',
            'module' => 'Roles & Permissions',
            'description' => "Assigned role '{$role->name}' to user: {$user->email}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'Role assigned to user successfully.');
    }
}