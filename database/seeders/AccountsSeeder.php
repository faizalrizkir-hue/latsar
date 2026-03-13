<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('accounts')->updateOrInsert(
            ['username' => 'admin'],
            [
                'password_hash' => Hash::make('admin123'), // admin123
                'display_name' => 'Admin SIKAP',
                'role' => 'administrator',
                'active' => true,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }
}
