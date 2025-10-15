<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::updateOrCreate(
            ['email' => 'admin@visitor.com'],
            [
                'name' => 'System Administrator',
                'role' => 'admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );

        // Security user
        User::updateOrCreate(
            ['email' => 'security@visitor.com'],
            [
                'name' => 'Security Officer',
                'role' => 'security',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );

        // Receptionist user
        User::updateOrCreate(
            ['email' => 'reception@visitor.com'],
            [
                'name' => 'Receptionist',
                'role' => 'receptionist',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );

        echo "✓ Admin users created successfully!\n";
        echo "Admin: admin@visitor.com / password\n";
        echo "Security: security@visitor.com / password\n";
        echo "Reception: reception@visitor.com / password\n";
    }
}
