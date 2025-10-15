<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // Keep for backward compatibility
        'department',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Legacy role check (backward compatible)
     */
    public function isAdmin()
    {
        // Check both old 'role' column and new roles relationship
        return $this->role === 'admin' || $this->hasRole('admin');
    }

    /**
     * Many-to-Many: User belongs to many Roles
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    /**
     * Check if user has a specific role
     * 
     * @param string|Role|array $role
     * @return bool
     */
    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->roles->contains('name', $role);
        }

        if ($role instanceof Role) {
            return $this->roles->contains('id', $role->id);
        }

        if (is_array($role)) {
            foreach ($role as $r) {
                if ($this->hasRole($r)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check if user has a specific permission
     * 
     * @param string $permission
     * @return bool
     */
    public function hasPermission($permission)
    {
        // Load roles with permissions if not already loaded
        if (!$this->relationLoaded('roles')) {
            $this->load('roles.permissions');
        }

        foreach ($this->roles as $role) {
            if ($role->permissions->contains('name', $permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user has any of the given permissions
     * 
     * @param array $permissions
     * @return bool
     */
    public function hasAnyPermission($permissions)
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user has all of the given permissions
     * 
     * @param array $permissions
     * @return bool
     */
    public function hasAllPermissions($permissions)
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Assign a role to user
     * 
     * @param string|Role $role
     * @return void
     */
    public function assignRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->firstOrFail();
        }

        if (!$this->hasRole($role)) {
            $this->roles()->attach($role->id);
        }
    }

    /**
     * Remove a role from user
     * 
     * @param string|Role $role
     * @return void
     */
    public function removeRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->firstOrFail();
        }

        $this->roles()->detach($role->id);
    }

    /**
     * Sync roles (replace all existing roles)
     * 
     * @param array $roles
     * @return void
     */
    public function syncRoles($roles)
    {
        $roleIds = [];
        
        foreach ($roles as $role) {
            if (is_string($role)) {
                $roleModel = Role::where('name', $role)->first();
                if ($roleModel) {
                    $roleIds[] = $roleModel->id;
                }
            } elseif ($role instanceof Role) {
                $roleIds[] = $role->id;
            } elseif (is_numeric($role)) {
                $roleIds[] = $role;
            }
        }

        $this->roles()->sync($roleIds);
    }

    /**
     * Get primary role display name
     * 
     * @return string
     */
    public function getPrimaryRoleAttribute()
    {
        if ($this->roles && $this->roles->count() > 0) {
            return $this->roles->first()->display_name;
        }

        // Fallback to old role column
        if ($this->role) {
            return ucfirst($this->role);
        }

        return 'Member';
    }

    /**
     * Visitor visits hosted by this user
     */
    public function hostedVisits()
    {
        return $this->hasMany(VisitorVisit::class, 'host_id');
    }

    /**
     * Check if user is active
     * 
     * @return bool
     */
    public function isActive()
    {
        return $this->is_active;
    }

    /**
     * Scope: Only active users
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Users with specific role
     */
    public function scopeWithRole($query, $role)
    {
        return $query->whereHas('roles', function($q) use ($role) {
            $q->where('name', $role);
        });
    }

    /**
     * Scope: Users with specific permission
     */
    public function scopeWithPermission($query, $permission)
    {
        return $query->whereHas('roles.permissions', function($q) use ($permission) {
            $q->where('name', $permission);
        });
    }
}
