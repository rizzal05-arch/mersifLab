<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ClassModel;

class TestCourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create teacher
        $teacher = User::updateOrCreate(
            ['email' => 'teacher@test.com'],
            [
                'name' => 'Test Teacher',
                'password' => bcrypt('password123'),
                'role' => 'teacher'
            ]
        );

        // Create test courses for each category
        $categories = [
            'ai' => 'AI & Machine Learning Basics',
            'development' => 'Web Development Fundamentals',
            'marketing' => 'Digital Marketing Strategies',
            'design' => 'UI/UX Design Masterclass',
            'photography' => 'Professional Photography Course'
        ];

        foreach ($categories as $category => $name) {
            ClassModel::create([
                'teacher_id' => $teacher->id,
                'name' => $name,
                'description' => "Comprehensive course on $category with practical exercises and real-world projects.",
                'category' => $category,
                'is_published' => true,
                'order' => 0
            ]);
        }

        $this->command->info('Test courses created successfully!');
    }
}
