#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "\n=== CHECKING USERS ===\n";

$users = User::all();
echo "Total Users: " . count($users) . "\n";

foreach ($users as $user) {
    echo sprintf(
        "- Email: %s, Name: %s, Role: %s\n",
        $user->email,
        $user->name,
        $user->role
    );
}

// Create test student if not exists
$student = User::firstOrCreate(
    ['email' => 'student@test.com'],
    [
        'name' => 'Test Student',
        'password' => bcrypt('password123'),
        'role' => 'student'
    ]
);

echo "\n✓ Student Account Ready:\n";
echo "  Email: student@test.com\n";
echo "  Password: password123\n";

?>
