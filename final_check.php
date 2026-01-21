#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

use App\Models\User;
use App\Models\ClassModel;

echo "\n=== FINAL CHECK ===\n";

// Check courses
$published = ClassModel::where('is_published', 1)->with('teacher')->withCount('modules')->get();
echo "Published Courses: " . count($published) . "\n";
foreach ($published as $course) {
    echo sprintf(
        "✓ %s (Category: %s, Teacher: %s, Modules: %d)\n",
        $course->name,
        $course->category,
        $course->teacher->name ?? 'Unknown',
        $course->modules_count
    );
}

// Check students
$students = User::where('role', 'student')->get();
echo "\nStudent Accounts: " . count($students) . "\n";
foreach ($students as $student) {
    echo "✓ " . $student->email . " (" . $student->name . ")\n";
}

echo "\n✅ ALL SET! Ready to test.\n";
echo "Login with: student@example.com / password\n";

?>
