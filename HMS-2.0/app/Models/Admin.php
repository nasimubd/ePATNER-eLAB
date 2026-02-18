<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $table = 'users'; // Using the same users table

    protected $fillable = [
        'name',
        'email',
        'password',
        'business_id',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the business that this admin manages
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Check if admin has a business assigned
     */
    public function hasBusiness(): bool
    {
        return !is_null($this->business_id);
    }

    /**
     * Get admin's initials for avatar
     */
    public function getInitialsAttribute(): string
    {
        $names = explode(' ', $this->name);
        $initials = '';

        foreach ($names as $name) {
            $initials .= strtoupper(substr($name, 0, 1));
        }

        return substr($initials, 0, 2);
    }

    /**
     * Scope to get only admin users
     */
    public function scopeAdmins($query)
    {
        return $query->whereHas('roles', function ($q) {
            $q->where('name', 'admin');
        });
    }

    /**
     * Scope to search admins
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%")
                ->orWhereHas('business', function ($businessQuery) use ($search) {
                    $businessQuery->where('hospital_name', 'LIKE', "%{$search}%");
                });
        });
    }
}
