<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Course;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // Create regular user
        User::create([
            'name' => 'Test User',
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
        ]);

        // Create sample courses
        Course::create([
            'title' => 'Introduction to Laravel',
            'description' => 'Learn the basics of Laravel framework',
        ]);

        Course::create([
            'title' => 'Advanced PHP',
            'description' => 'Deep dive into PHP programming',
        ]);

        Course::create([
            'title' => 'Web Development Fundamentals',
            'description' => 'Master HTML, CSS, and JavaScript',
        ]);
    }
}
