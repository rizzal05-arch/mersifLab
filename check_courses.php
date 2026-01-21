<?php
require 'vendor/autoload.php';
require 'bootstrap/app.php';

use App\Models\ClassModel;
use App\Models\User;

// Check existing data
echo "=== Checking Database ===\n";
echo "Total Classes: " . ClassModel::count() . "\n";
echo "Total Teachers: " . User::where('role', 'teacher')->count() . "\n";

$classes = ClassModel::all();
foreach ($classes as $class) {
    echo "- ID: $class->id, Name: $class->name, Published: $class->is_published, Category: $class->category\n";
}

// If no data, create test data
if (ClassModel::count() === 0) {
    echo "\n=== Creating Test Data ===\n";
    
    // Create teacher
    $teacher = User::updateOrCreate(
        ['email' => 'teacher@test.com'],
        [
            'name' => 'Test Teacher',
            'password' => bcrypt('password'),
            'role' => 'teacher'
        ]
    );
    echo "Teacher created: $teacher->name (ID: $teacher->id)\n";
    
    // Create classes for different categories
    $categories = ['ai', 'development', 'marketing', 'design', 'photography'];
    $courseNames = [
        'ai' => 'AI & Machine Learning Basics',
        'development' => 'Web Development Fundamentals',
        'marketing' => 'Digital Marketing Strategies',
        'design' => 'UI/UX Design Masterclass',
        'photography' => 'Professional Photography Course'
    ];
    
    foreach ($categories as $category) {
        $class = ClassModel::create([
            'teacher_id' => $teacher->id,
            'name' => $courseNames[$category],
            'description' => "Learn $category in this comprehensive course",
            'category' => $category,
            'is_published' => true,
            'order' => 0
        ]);
        echo "✓ Created: $class->name (Category: $category, Published: " . ($class->is_published ? 'Yes' : 'No') . ")\n";
    }
}

echo "\n=== Final Check ===\n";
echo "Total Published Classes: " . ClassModel::where('is_published', true)->count() . "\n";
?>
