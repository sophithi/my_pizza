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
     * Check if user is manager (Boss has identical permissions to Manager)
     */
    public function isManager(): bool
    {
        return in_array($this->role, ['manager', 'boss']);
    }

    /**
     * Check if user is boss
     */
    public function isBoss(): bool
    {
        return $this->role === 'boss';
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
     * Check if user is an auditor (view-only access to stock, invoices, expenses, payments, reports)
     */
    public function isAuditor(): bool
    {
        return $this->role === 'auditor';
    }

    /**
     * Get role label
     */
    public function getRoleLabel(): string
    {
        return match($this->role) {
            'admin' => 'Administrator',
            'manager' => 'Manager',
            'boss' => 'Boss',
            'staff' => 'Staff (Office)',
            'staff_inventory' => 'Staff (Inventory)',
            'auditor' => 'Auditor',
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
     * Get customers where this user is the assigned salesperson.
     */
    public function customers()
    {
        return $this->hasMany(Customer::class, 'salesperson_id');
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
