#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "\n=== PUBLISHING COURSES ===\n";

// Publish all existing courses
$updated = DB::table('classes')
    ->whereIn('id', [1, 2])
    ->update(['is_published' => 1]);

echo "Updated: $updated courses\n";

// Verify
echo "\n=== VERIFICATION ===\n";
$classes = DB::table('classes')->where('is_published', 1)->get();
echo "Published Classes: " . count($classes) . "\n";

foreach ($classes as $class) {
    echo sprintf(
        "✓ %s (Category: %s)\n",
        $class->name,
        $class->category
    );
}

?>
