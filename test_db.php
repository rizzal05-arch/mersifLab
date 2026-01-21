#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "\n=== DATABASE CHECK ===\n";

// Check classes table
$classes = DB::table('classes')->get();
echo "Total Classes in DB: " . count($classes) . "\n";

foreach ($classes as $class) {
    echo sprintf(
        "- ID: %d, Name: %s, Published: %d, Category: %s\n",
        $class->id,
        $class->name,
        $class->is_published,
        $class->category ?? 'NULL'
    );
}

echo "\nPublished Classes:\n";
$published = DB::table('classes')->where('is_published', 1)->get();
echo "Count: " . count($published) . "\n";

foreach ($published as $class) {
    echo sprintf(
        "- %s (Category: %s)\n",
        $class->name,
        $class->category
    );
}

echo "\nBy Category:\n";
$byCategory = DB::table('classes')
    ->where('is_published', 1)
    ->groupBy('category')
    ->selectRaw('category, COUNT(*) as count')
    ->get();

foreach ($byCategory as $cat) {
    echo sprintf("- %s: %d courses\n", $cat->category, $cat->count);
}

?>
