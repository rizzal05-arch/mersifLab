<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

use App\Models\User;
use App\Models\ClassModel;
use Illuminate\Support\Facades\Auth;

echo "\n=== SIMULATE STUDENT LOGIN ===\n";

// Get a student
$student = User::where('role', 'student')->first();

if ($student) {
    echo "Student: " . $student->email . "\n";
    
    // Simulate login
    Auth::login($student);
    
    echo "Logged in as: " . auth()->user()->name . "\n";
    
    // Test enrolled courses (same as in HomeController)
    $enrolledCourses = collect();
    if (auth()->check() && auth()->user()->isStudent()) {
        $enrolledCourses = ClassModel::published()
            ->with('teacher')
            ->withCount('modules')
            ->take(3)
            ->get();
    }
    
    echo "Enrolled Courses: " . count($enrolledCourses) . "\n";
    
    foreach ($enrolledCourses as $course) {
        echo sprintf(
            "✓ %s (ID: %d, Teacher: %s)\n",
            $course->name,
            $course->id,
            $course->teacher->name ?? 'NULL'
        );
    }
    
    echo "\n✅ No error! All courses loaded correctly.\n";
} else {
    echo "❌ No student found!\n";
}

?>
