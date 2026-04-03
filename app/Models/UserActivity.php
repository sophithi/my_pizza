<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
    protected $fillable = [
        'user_id',
        'activity_type',
        'ip_address',
        'user_agent',
        'activity_at',
    ];

    protected $casts = [
        'activity_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user associated with this activity.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get login activities.
     */
    public function scopeLogins($query)
    {
        return $query->where('activity_type', 'login');
    }

    /**
     * Scope to get logout activities.
     */
    public function scopeLogouts($query)
    {
        return $query->where('activity_type', 'logout');
    }

    /**
     * Scope to get recent activities.
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('activity_at', '>=', now()->subDays($days));
    }
}
