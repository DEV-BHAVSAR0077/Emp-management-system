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
        'profile_photo',
        'report_frequency',
        'next_send_at',
        'last_sent_at',
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
            'next_send_at'      => 'datetime',
            'last_sent_at'      => 'datetime',
        ];
    }

    // Role relationship ─────────────────────────────────────────────────
    public function roleInfo(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role', 'name');
    }

    // Profile Photo helper ──────────────────────────────────────────────
    public function getProfilePhotoUrlAttribute()
    {
        return $this->profile_photo 
            ? asset('storage/' . $this->profile_photo) 
            : null;
    }

    // Permission helpers ────────────────────────────────────────────────

    public function hasPermission($permission)
    {
        return $this->roleInfo &&
            $this->roleInfo->permissions->contains('name', $permission);
    }
}
