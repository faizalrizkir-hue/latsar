<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    public $timestamps = false;

    protected $with = [
        'coordinatorAccount',
    ];

    protected $fillable = [
        'subtopic_title',
        'statement',
        'row_id',
        'coordinator_name',
        'coordinator_username',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function coordinatorAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'coordinator_username', 'username');
    }

    public function getCoordinatorRoleLabelAttribute(): string
    {
        $role = $this->coordinatorAccount?->role;

        return $role !== null
            ? Account::roleLabel($role)
            : 'Pengguna';
    }
}
