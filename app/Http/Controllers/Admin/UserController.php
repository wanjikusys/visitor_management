<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles');

        // Search filter
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Role filter
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'nullable|string',
            'department' => 'nullable|string|max:255',
            'roles' => 'array',
            'roles.*' => 'exists:roles,id',
            'is_active' => 'boolean',
        ]);

        // Create user with email verified automatically when admin creates
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'] ?? 'user',
            'department' => $validated['department'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
            'email_verified_at' => now(), // AUTO VERIFY EMAIL
        ]);

        // Assign roles if provided
        if (isset($validated['roles'])) {
            $user->roles()->attach($validated['roles']);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully! Email automatically verified.');
    }

    public function show(User $user)
    {
        $user->load('roles.permissions');
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $userRoles = $user->roles->pluck('id')->toArray();
        return view('admin.users.edit', compact('user', 'roles', 'userRoles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'nullable|string',
            'department' => 'nullable|string|max:255',
            'roles' => 'array',
            'roles.*' => 'exists:roles,id',
            'is_active' => 'boolean',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'] ?? 'user',
            'department' => $validated['department'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ];

        // Update password if provided
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        // If email was not verified before, verify it now
        if (!$user->email_verified_at) {
            $updateData['email_verified_at'] = now();
        }

        $user->update($updateData);

        // Sync roles
        if (isset($validated['roles'])) {
            $user->roles()->sync($validated['roles']);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully!');
    }

    public function destroy(User $user)
    {
        // Prevent deleting yourself
        if (auth()->id() === $user->id) {
            return back()->with('error', 'You cannot delete yourself!');
        }

        // Prevent deleting the last admin
        if ($user->hasRole('admin') && User::whereHas('roles', function($q) {
            $q->where('name', 'admin');
        })->count() <= 1) {
            return back()->with('error', 'Cannot delete the last admin user!');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully!');
    }
}
