<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ── Role helpers ──────────────────────────────────────────────────────

    /** Is this user an admin? */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /** Is this user an HR? */
    public function isHr(): bool
    {
        return $this->role === 'hr';
    }

    /**
     * Can this user manage other users?
     * Admin and HR can edit/delete any user and add new ones.
     */
    public function canManageUsers(): bool
    {
        return in_array($this->role, ['admin', 'hr'], true);
    }
}
