<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDevice extends Model
{
    protected $fillable = [
        'user_id',
        'ip_address',
        'device_type',
        'device_name',
        'user_agent',
        'is_trusted',
        'is_blocked',
        'last_login_at',
    ];

    protected function casts(): array
    {
        return [
            'is_trusted' => 'boolean',
            'is_blocked' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
