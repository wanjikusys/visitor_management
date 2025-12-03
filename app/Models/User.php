<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',           // Legacy column - kept for backward compatibility
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

    // ──────────────────────────────────────────────────────────────
    // RELATIONSHIPS
    // ──────────────────────────────────────────────────────────────

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    public function hostedVisits()
    {
        return $this->hasMany(VisitorVisit::class, 'host_id');
    }

    // ──────────────────────────────────────────────────────────────
    // CACHED PERMISSIONS (Fast & Real-time)
    // ──────────────────────────────────────────────────────────────

    /**
     * Get all permission names this user has via roles (cached forever)
     */
    public function getPermissionsAttribute()
    {
        return Cache::rememberForever("user_permissions_{$this->id}", function () {
            return $this->roles()
                ->with('permissions')
                ->get()
                ->pluck('permissions')
                ->flatten()
                ->pluck('name')
                ->unique()
                ->values();
        });
    }

    /**
     * Flush permission cache (call this whenever roles/permissions change)
     */
    public function flushPermissionsCache(): void
    {
        Cache::forget("user_permissions_{$this->id}");
    }

    // ──────────────────────────────────────────────────────────────
    // ROLE & PERMISSION CHECKS (Used in your sidebar!)
    // ──────────────────────────────────────────────────────────────

    public function hasRole($role): bool
    {
        if (is_string($role)) {
            // Also check legacy 'role' column for old data
            if ($this->role === $role) return true;

            return $this->roles->contains('name', $role);
        }

        if ($role instanceof Role) {
            return $this->roles->contains('id', $role->id);
        }

        if (is_array($role)) {
            foreach ($role as $r) {
                if ($this->hasRole($r)) return true;
            }
        }

        return false;
    }

    public function hasPermission(string $permission): bool
    {
        return $this->permissions->contains($permission);
    }

    public function hasAnyPermission(array $permissions): bool
    {
        return $this->permissions->intersect($permissions)->isNotEmpty();
    }

    public function hasAllPermissions(array $permissions): bool
    {
        return $this->permissions->intersect($permissions)->count() === count($permissions);
    }

    // Backward compatibility
    public function isAdmin(): bool
    {
        return $this->role === 'admin' || $this->hasRole('admin');
    }

    // ──────────────────────────────────────────────────────────────
    // ROLE MANAGEMENT
    // ──────────────────────────────────────────────────────────────

    public function assignRole($role): void
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->firstOrFail();
        }

        if (!$this->hasRole($role)) {
            $this->roles()->attach($role->id);
            $this->flushPermissionsCache();
        }
    }

    public function removeRole($role): void
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->firstOrFail();
        }

        $this->roles()->detach($role->id);
        $this->flushPermissionsCache();
    }

    public function syncRoles(array $roles): void
    {
        $roleIds = [];

        foreach ($roles as $role) {
            if (is_string($role)) {
                $model = Role::where('name', $role)->first();
                if ($model) $roleIds[] = $model->id;
            } elseif ($role instanceof Role) {
                $roleIds[] = $role->id;
            } elseif (is_numeric($role)) {
                $roleIds[] = $role;
            }
        }

        $this->roles()->sync($roleIds);
        $this->flushPermissionsCache();
    }

    // ──────────────────────────────────────────────────────────────
    // DISPLAY HELPERS
    // ──────────────────────────────────────────────────────────────

    public function getPrimaryRoleAttribute(): string
    {
        if ($this->roles->count() > 0) {
            return $this->roles->first()->display_name;
        }

        if ($this->role) {
            return ucfirst($this->role);
        }

        return 'Member';
    }

    // ──────────────────────────────────────────────────────────────
    // SCOPES
    // ──────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeWithRole($query, $role)
    {
        return $query->whereHas('roles', fn($q) => $q->where('name', $role));
    }

    public function scopeWithPermission($query, $permission)
    {
        return $query->whereHas('roles.permissions', fn($q) => $q->where('name', $permission));
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }
}