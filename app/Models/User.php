<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    //The attributes that are mass assignable.
     
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    // The attributes that should be hidden for serialization.
    
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Get the attributes that should be cast.
    
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // Role relationship ─────────────────────────────────────────────────
    public function roleInfo(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role', 'name');
    }

    // Permission helpers ────────────────────────────────────────────────
    public function getPermissionLevel(): string
    {
        // Use DB role level if available
        if ($this->relationLoaded('roleInfo') && $this->roleInfo) {
            return $this->roleInfo->level;
        }
        // Lazy-load from DB
        $role = Role::where('name', $this->role)->first();
        if ($role) {
            return $role->level;
        }
        // Final fallback: match old lowercase role strings
        return match (strtolower($this->role)) {
            'admin' => 'admin',
            'hr'    => 'hr',
            default => 'user',
        };
    }

    /** Is this user an admin? */
    public function isAdmin(): bool
    {
        return $this->getPermissionLevel() === 'admin';
    }

    // Is this user an HR?
    public function isHr(): bool
    {
        return $this->getPermissionLevel() === 'hr';
    }

    // Can this user manage other users (add, edit, delete)? Both admin-level and hr-level roles can manage users.
    public function canManageUsers(): bool
    {
        return in_array($this->getPermissionLevel(), ['admin', 'hr'], true);
    }
}
