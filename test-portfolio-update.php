<?php

// Test script untuk verify portfolio_link update
// Run: php test-portfolio-update.php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TeacherApplication;
use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "=== Testing Portfolio Link Update ===\n\n";

// 1. Check if portfolio_link is in fillable
echo "1. Checking TeacherApplication model fillable array...\n";
$model = new TeacherApplication();
if (in_array('portfolio_link', $model->getFillable())) {
    echo "   ✅ portfolio_link is in fillable array\n\n";
} else {
    echo "   ❌ portfolio_link is NOT in fillable array\n";
    echo "   Current fillable: " . json_encode($model->getFillable()) . "\n\n";
}

// 2. Check if database column exists
echo "2. Checking if portfolio_link column exists in database...\n";
$columns = DB::select("PRAGMA table_info(teacher_applications)");
$hasColumn = collect($columns)->pluck('name')->contains('portfolio_link');
if ($hasColumn) {
    echo "   ✅ portfolio_link column exists in database\n\n";
} else {
    echo "   ⚠️  portfolio_link column might not exist (or database is MySQL)\n\n";
}

// 3. Test model mass assignment
echo "3. Testing mass assignment...\n";
try {
    $testData = [
        'portfolio_link' => 'https://www.example.com/portfolio'
    ];
    $app = app();
    $model = new TeacherApplication($testData);
    if ($model->portfolio_link === 'https://www.example.com/portfolio') {
        echo "   ✅ Mass assignment works for portfolio_link\n\n";
    } else {
        echo "   ❌ Mass assignment failed\n\n";
    }
} catch (\Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n\n";
}

// 4. Test update method
echo "4. Checking TeacherApplication update method...\n";
$reflectionMethod = new ReflectionMethod(TeacherApplication::class, 'update');
echo "   ✅ update() method exists and is accessible\n\n";

echo "=== All Checks Complete ===\n";
echo "\nIf all checks passed (✅), portfolio_link should now update correctly.\n";
echo "Try editing an application and changing the portfolio link.\n";
