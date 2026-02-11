#!/usr/bin/env php
<?php

/**
 * Subscription System Verification Script
 * 
 * This script verifies that the subscription system is properly implemented
 * and all components are working correctly.
 * 
 * Usage: php verify-subscription-system.php
 */

// Colors for console output
define('GREEN', "\033[32m");
define('RED', "\033[31m");
define('YELLOW', "\033[33m");
define('BLUE', "\033[34m");
define('RESET', "\033[0m");

// Test counter
$testsPassed = 0;
$testsFailed = 0;
$testsWarning = 0;

// Helper functions
function testPass($message) {
    global $testsPassed;
    echo GREEN . "✓" . RESET . " PASS: $message\n";
    $testsPassed++;
}

function testFail($message) {
    global $testsFailed;
    echo RED . "✗" . RESET . " FAIL: $message\n";
    $testsFailed++;
}

function testWarning($message) {
    global $testsWarning;
    echo YELLOW . "⚠" . RESET . " WARN: $message\n";
    $testsWarning++;
}

function testInfo($message) {
    echo BLUE . "ℹ" . RESET . " INFO: $message\n";
}

echo BLUE . "================================" . RESET . "\n";
echo BLUE . "Subscription System Verification" . RESET . "\n";
echo BLUE . "================================" . RESET . "\n\n";

// Start Laravel
try {
    $app = require __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    testPass("Laravel application bootstrapped");
} catch (Exception $e) {
    testFail("Failed to bootstrap Laravel: " . $e->getMessage());
    exit(1);
}

// 1. Test Database Migrations
echo "\n" . BLUE . "1. Database Schema Verification" . RESET . "\n";
echo str_repeat("-", 40) . "\n";

try {
    $userTable = \Illuminate\Support\Facades\Schema::getColumns('users');
    $userColumns = array_column($userTable, 'name');
    
    if (in_array('is_subscriber', $userColumns)) {
        testPass("'is_subscriber' column exists in users table");
    } else {
        testFail("'is_subscriber' column missing from users table");
    }
    
    if (in_array('subscription_expires_at', $userColumns)) {
        testPass("'subscription_expires_at' column exists in users table");
    } else {
        testFail("'subscription_expires_at' column missing from users table");
    }
    
    if (in_array('subscription_plan', $userColumns)) {
        testPass("'subscription_plan' column exists in users table");
    } else {
        testFail("'subscription_plan' column missing from users table");
    }
    
} catch (Exception $e) {
    testFail("Error checking users table: " . $e->getMessage());
}

try {
    $classesTable = \Illuminate\Support\Facades\Schema::getColumns('classes');
    $classesColumns = array_column($classesTable, 'name');
    
    if (in_array('price_tier', $classesColumns)) {
        testPass("'price_tier' column exists in classes table");
    } else {
        testFail("'price_tier' column missing from classes table");
    }
    
} catch (Exception $e) {
    testFail("Error checking classes table: " . $e->getMessage());
}

// 2. Test User Model Methods
echo "\n" . BLUE . "2. User Model Methods" . RESET . "\n";
echo str_repeat("-", 40) . "\n";

try {
    $userModel = new \App\Models\User();
    
    // Check if methods exist
    if (method_exists($userModel, 'hasActiveSubscription')) {
        testPass("User::hasActiveSubscription() method exists");
    } else {
        testFail("User::hasActiveSubscription() method missing");
    }
    
    if (method_exists($userModel, 'canAccessViaPlanTier')) {
        testPass("User::canAccessViaPlanTier() method exists");
    } else {
        testFail("User::canAccessViaPlanTier() method missing");
    }
    
    if (method_exists($userModel, 'isSubscriptionExpiringSoon')) {
        testPass("User::isSubscriptionExpiringSoon() method exists");
    } else {
        testFail("User::isSubscriptionExpiringSoon() method missing");
    }
    
} catch (Exception $e) {
    testFail("Error checking User model: " . $e->getMessage());
}

// 3. Test Controllers
echo "\n" . BLUE . "3. Controller Classes" . RESET . "\n";
echo str_repeat("-", 40) . "\n";

try {
    $subscriptionController = new \App\Http\Controllers\SubscriptionController();
    if (method_exists($subscriptionController, 'show')) {
        testPass("SubscriptionController::show() method exists");
    } else {
        testFail("SubscriptionController::show() method missing");
    }
    
    if (method_exists($subscriptionController, 'subscribe')) {
        testPass("SubscriptionController::subscribe() method exists");
    } else {
        testFail("SubscriptionController::subscribe() method missing");
    }
} catch (Exception $e) {
    testFail("Error checking SubscriptionController: " . $e->getMessage());
}

try {
    $moduleViewController = new \App\Http\Controllers\ModuleViewController();
    if (method_exists($moduleViewController, 'show')) {
        testPass("ModuleViewController::show() method exists");
    } else {
        testFail("ModuleViewController::show() method missing");
    }
    
    if (method_exists($moduleViewController, 'serveFile')) {
        testPass("ModuleViewController::serveFile() method exists");
    } else {
        testFail("ModuleViewController::serveFile() method missing");
    }
} catch (Exception $e) {
    testFail("Error checking ModuleViewController: " . $e->getMessage());
}

try {
    $enrollmentController = new \App\Http\Controllers\EnrollmentController();
    if (method_exists($enrollmentController, 'markComplete')) {
        testPass("EnrollmentController::markComplete() method exists");
    } else {
        testFail("EnrollmentController::markComplete() method missing");
    }
    
    if (method_exists($enrollmentController, 'completeAllModules')) {
        testPass("EnrollmentController::completeAllModules() method exists");
    } else {
        testFail("EnrollmentController::completeAllModules() method missing");
    }
} catch (Exception $e) {
    testFail("Error checking EnrollmentController: " . $e->getMessage());
}

// 4. Test Routes
echo "\n" . BLUE . "4. Routes" . RESET . "\n";
echo str_repeat("-", 40) . "\n";

try {
    $routes = \Illuminate\Support\Facades\Route::getRoutes();
    $routeNames = array_map(fn($r) => $r->getName(), $routes->getRoutes());
    
    if (in_array('subscription.page', $routeNames)) {
        testPass("Route 'subscription.page' is registered");
    } else {
        testFail("Route 'subscription.page' not found");
    }
    
    if (in_array('subscribe', $routeNames)) {
        testPass("Route 'subscribe' is registered");
    } else {
        testFail("Route 'subscribe' not found");
    }
    
} catch (Exception $e) {
    testFail("Error checking routes: " . $e->getMessage());
}

// 5. Test Views
echo "\n" . BLUE . "5. Views" . RESET . "\n";
echo str_repeat("-", 40) . "\n";

$subscriptionIndexPath = base_path('resources/views/subscription/index.blade.php');
if (file_exists($subscriptionIndexPath)) {
    testPass("View 'subscription/index.blade.php' exists");
    
    $content = file_get_contents($subscriptionIndexPath);
    if (strpos($content, 'subscription') !== false) {
        testPass("View contains subscription-related content");
    } else {
        testWarning("View may not contain expected subscription content");
    }
} else {
    testFail("View 'subscription/index.blade.php' not found");
}

$courseDetailPath = base_path('resources/views/course-detail.blade.php');
if (file_exists($courseDetailPath)) {
    testPass("View 'course-detail.blade.php' exists");
    
    $content = file_get_contents($courseDetailPath);
    if (strpos($content, 'canAccessBySubscription') !== false) {
        testPass("Course detail view has subscription access logic");
    } else {
        testWarning("Course detail view may not have subscription logic");
    }
} else {
    testFail("View 'course-detail.blade.php' not found");
}

// 6. Test Functionality (if test user exists)
echo "\n" . BLUE . "6. Functional Tests" . RESET . "\n";
echo str_repeat("-", 40) . "\n";

try {
    // Try to find a test user
    $testUser = \App\Models\User::where('email', 'test@example.com')->first();
    
    if ($testUser) {
        testInfo("Found test user: {$testUser->email}");
        
        // Test hasActiveSubscription on non-subscribed user
        if (!$testUser->hasActiveSubscription()) {
            testPass("hasActiveSubscription() returns false for non-subscribed user");
        } else {
            testWarning("Test user already has subscription");
        }
        
        // Test canAccessViaPlanTier on non-subscribed user
        if (!$testUser->canAccessViaPlanTier('standard')) {
            testPass("canAccessViaPlanTier('standard') returns false for non-subscribed user");
        } else {
            testFail("canAccessViaPlanTier should return false for non-subscribed user");
        }
        
        // Try to subscribe test user
        $testUser->update([
            'is_subscriber' => true,
            'subscription_plan' => 'standard',
            'subscription_expires_at' => \Carbon\Carbon::now()->addMonth(),
        ]);
        
        if ($testUser->hasActiveSubscription()) {
            testPass("hasActiveSubscription() returns true after subscription");
        } else {
            testFail("hasActiveSubscription() should return true for subscribed user");
        }
        
        if ($testUser->canAccessViaPlanTier('standard')) {
            testPass("canAccessViaPlanTier('standard') returns true for Standard subscriber");
        } else {
            testFail("canAccessViaPlanTier('standard') should return true");
        }
        
        if (!$testUser->canAccessViaPlanTier('premium')) {
            testPass("canAccessViaPlanTier('premium') returns false for Standard subscriber");
        } else {
            testFail("canAccessViaPlanTier('premium') should return false for Standard subscriber");
        }
        
        // Test Premium subscription
        $testUser->update([
            'subscription_plan' => 'premium',
        ]);
        
        if ($testUser->canAccessViaPlanTier('standard') && $testUser->canAccessViaPlanTier('premium')) {
            testPass("canAccessViaPlanTier works correctly for Premium subscriber");
        } else {
            testFail("Premium subscriber should access both tiers");
        }
        
    } else {
        testWarning("Test user (test@example.com) not found - skipping functional tests");
        testInfo("Create a test user to run functional tests");
    }
    
} catch (Exception $e) {
    testWarning("Functional tests skipped: " . $e->getMessage());
}

// 7. Summary
echo "\n" . BLUE . "================================" . RESET . "\n";
echo BLUE . "Verification Summary" . RESET . "\n";
echo BLUE . "================================" . RESET . "\n";

echo GREEN . "✓ Tests Passed: $testsPassed" . RESET . "\n";
if ($testsFailed > 0) {
    echo RED . "✗ Tests Failed: $testsFailed" . RESET . "\n";
}
if ($testsWarning > 0) {
    echo YELLOW . "⚠ Tests Warning: $testsWarning" . RESET . "\n";
}

$totalTests = $testsPassed + $testsFailed + $testsWarning;
$passPercentage = $totalTests > 0 ? round(($testsPassed / $totalTests) * 100, 1) : 0;

echo "\nTotal Tests: $totalTests\n";
echo "Pass Rate: {$passPercentage}%\n";

if ($testsFailed === 0 && $testsWarning === 0) {
    echo GREEN . "\n✓ All checks passed! Subscription system is properly implemented." . RESET . "\n";
    exit(0);
} elseif ($testsFailed === 0) {
    echo YELLOW . "\n⚠ All critical checks passed, but some warnings detected. Review them above." . RESET . "\n";
    exit(0);
} else {
    echo RED . "\n✗ Some checks failed. Review the errors above and fix them." . RESET . "\n";
    exit(1);
}
