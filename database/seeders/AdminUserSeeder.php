<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'adm.ali@gmail.com'],
            [
                'name' => 'Adam Ali',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
    }
}
