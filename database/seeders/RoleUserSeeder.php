<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test student users
        User::firstOrCreate(
            ['email' => 'student1@example.com'],
            [
                'name' => 'Ahmad Student',
                'password' => Hash::make('password'),
                'role' => 'student',
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'student2@example.com'],
            [
                'name' => 'Siti Belajar',
                'password' => Hash::make('password'),
                'role' => 'student',
                'email_verified_at' => now(),
            ]
        );

        // Create test teacher users
        User::firstOrCreate(
            ['email' => 'teacher1@example.com'],
            [
                'name' => 'Dr. Budi Pengajar',
                'password' => Hash::make('password'),
                'role' => 'teacher',
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'teacher2@example.com'],
            [
                'name' => 'Ibu Ratna Instruktur',
                'password' => Hash::make('password'),
                'role' => 'teacher',
                'email_verified_at' => now(),
            ]
        );

        // Create admin user
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin Mersif',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Role-based test users created successfully!');
        $this->command->line('');
        $this->command->info('Test Credentials:');
        $this->command->line('Student 1: student1@example.com / password');
        $this->command->line('Student 2: student2@example.com / password');
        $this->command->line('Teacher 1: teacher1@example.com / password');
        $this->command->line('Teacher 2: teacher2@example.com / password');
        $this->command->line('Admin: admin@example.com / password');
    }
}
