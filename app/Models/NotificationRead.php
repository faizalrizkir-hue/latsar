<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationRead extends Model
{
    protected $table = 'notification_reads';

    protected $fillable = [
        'notification_id',
        'username',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];
}
