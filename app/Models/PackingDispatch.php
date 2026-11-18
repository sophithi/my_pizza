<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackingDispatch extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'sent_at' => 'datetime',
        'completed_at' => 'datetime',
        'snapshot' => 'array',
    ];
}
