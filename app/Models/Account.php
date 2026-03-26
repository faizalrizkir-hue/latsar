<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $table = 'accounts';

    protected $fillable = [
        'username',
        'password_hash',
        'display_name',
        'profile_photo',
        'role',
        'active',
        'last_login_ip',
        'last_login_device',
    ];

    protected $hidden = [
        'password_hash',
    ];

    public static function roleLabel(?string $role): string
    {
        $roleKey = strtolower(trim((string) $role));

        return match (true) {
            in_array($roleKey, ['administrator', 'admin', 'superadmin'], true) => 'Administrator',
            $roleKey === 'koordinator' => 'Koordinator',
            $roleKey === 'qa' => 'QA BPKP',
            $roleKey === '' => 'Pengguna',
            default => 'Anggota Tim',
        };
    }

    public function getRoleLabelAttribute(): string
    {
        return self::roleLabel($this->role);
    }
}
