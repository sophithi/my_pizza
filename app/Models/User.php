<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
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
        'profile',
        'profile_image',
        'is_active',
        'last_login_at',
        'last_logout_at',
        'last_login_ip',
        'last_login_user_agent',
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
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'last_logout_at' => 'datetime',
        ];
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is manager
     */
    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    /**
     * Check if user is staff (office)
     */
    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    /**
     * Check if user is staff (inventory)
     */
    public function isStaffInventory(): bool
    {
        return $this->role === 'staff_inventory';
    }

    /**
     * Check if user is any staff type
     */
    public function isAnyStaff(): bool
    {
        return in_array($this->role, ['staff', 'staff_inventory']);
    }

    /**
     * Get role label
     */
    public function getRoleLabel(): string
    {
        return match($this->role) {
            'admin' => 'Administrator',
            'manager' => 'Manager',
            'staff' => 'Staff (Office)',
            'staff_inventory' => 'Staff (Inventory)',
            default => 'Unknown',
        };
    }

    /**
     * Get user's activity logs
     */
    public function activities()
    {
        return $this->hasMany(UserActivity::class);
    }

    /**
     * Get user's orders (orders created by this user)
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get user's login activities
     */
    public function loginActivities()
    {
        return $this->activities()->where('activity_type', 'login')->latest();
    }

    /**
     * Get user's logout activities
     */
    public function logoutActivities()
    {
        return $this->activities()->where('activity_type', 'logout')->latest();
    }
}
