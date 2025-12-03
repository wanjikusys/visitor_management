<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of roles
     */
    public function index()
    {
        $roles = Role::withCount(['users', 'permissions'])->get();
        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role
     */
    public function create()
    {
        $permissions = Permission::all()->groupBy('module');
        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created role
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name|max:255|regex:/^[a-z_]+$/',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ], [
            'name.regex' => 'Role name must be lowercase letters and underscores only (e.g., security_officer)'
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'description' => $validated['description'] ?? null,
        ]);

        if (!empty($validated['permissions'])) {
            $role->permissions()->attach($validated['permissions']);
        }

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role created successfully!');
    }

    /**
     * Show the form for editing the specified role
     */
    public function edit(Role $role)
    {
        $permissions = Permission::all()->groupBy('module');
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update the specified role
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|regex:/^[a-z_]+$/|unique:roles,name,' . $role->id,
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ], [
            'name.regex' => 'Role name must be lowercase letters and underscores only'
        ]);

        $role->update([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'description' => $validated['description'] ?? null,
        ]);

        $role->permissions()->sync($validated['permissions'] ?? []);

        // Clear permission cache for all users with this role
        $this->clearUserPermissionCache($role);

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role updated successfully!');
    }

    /**
     * Remove the specified role
     */
    public function destroy(Role $role)
    {
        if ($role->name === 'admin') {
            return back()->with('error', 'Cannot delete the admin role!');
        }

        if ($role->users()->count() > 0) {
            return back()->with('error', 'Cannot delete role with assigned users! Please reassign users first.');
        }

        $role->permissions()->detach();
        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role deleted successfully!');
    }

    /**
     * Show assign users to role page
     */
    public function assignUsers(Role $role)
    {
        $assignedUsers = $role->users;
        $availableUsers = User::whereDoesntHave('roles', function($q) use ($role) {
            $q->where('role_id', $role->id);
        })->get();

        return view('admin.roles.assign-users', compact('role', 'assignedUsers', 'availableUsers'));
    }

    /**
     * Attach user to role
     */
    public function attachUser(Request $request, Role $role)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $user = User::findOrFail($validated['user_id']);

        if ($role->users()->where('user_id', $user->id)->exists()) {
            return back()->with('error', 'User already has this role!');
        }

        $role->users()->attach($user->id);

        // Clear cache for the affected user
        $user->flushPermissionsCache();

        return back()->with('success', $user->name . ' has been assigned the ' . $role->display_name . ' role!');
    }

    /**
     * Detach user from role
     */
    public function detachUser(Role $role, User $user)
    {
        if ($role->name === 'admin' && $role->users()->count() === 1) {
            return back()->with('error', 'Cannot remove the last admin user!');
        }

        $role->users()->detach($user->id);

        // Clear cache for the affected user
        $user->flushPermissionsCache();

        return back()->with('success', $user->name . ' has been removed from the ' . $role->display_name . ' role!');
    }

    /**
     * Update permissions for a role (AJAX or direct)
     */
    public function updatePermissions(Request $request, Role $role)
    {
        $validated = $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        $role->permissions()->sync($validated['permissions'] ?? []);

        // Clear cache for ALL users with this role
        $this->clearUserPermissionCache($role);

        return back()->with('success', 'Permissions updated successfully!');
    }

    // Helper: Clear permission cache for all users in a role
    private function clearUserPermissionCache(Role $role)
    {
        foreach ($role->users as $user) {
            $user->flushPermissionsCache();
        }
    }
}