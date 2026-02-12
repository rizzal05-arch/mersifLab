<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Test the tier logic
$prices = [45000, 50000, 75000, 100000, 150000, 200000];
$tier_mapping = [];

foreach ($prices as $price) {
    $tier = null;
    if ($price >= 50000 && $price <= 100000) {
        $tier = 'standard';
    } elseif ($price > 100000) {
        $tier = 'premium';
    }
    $tier_mapping[$price] = $tier;
}

echo "Tier Mapping Test:\n";
echo "=================\n";
foreach ($tier_mapping as $price => $tier) {
    echo "Rp " . number_format($price) . " => " . ($tier ?? 'null (no tier)') . "\n";
}

// Check existing courses
echo "\n\nExisting Courses:\n";
echo "================\n";
$courses = \App\Models\ClassModel::limit(5)->get();
foreach ($courses as $course) {
    echo $course->name . " | Price: Rp " . number_format($course->price ?? 0) . " | Tier: " . ($course->price_tier ?? 'null') . "\n";
}
?>
