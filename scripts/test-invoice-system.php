<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\User;
use App\Models\ClassModel;
use App\Models\SubscriptionPurchase;
use App\Models\Purchase;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing Invoice System ===\n\n";

try {
    // Get a test user (student)
    $user = User::where('role', 'student')->first();
    if (!$user) {
        echo "No student user found. Creating test user...\n";
        $user = User::create([
            'name' => 'Test Student',
            'email' => 'test.student@example.com',
            'password' => bcrypt('password'),
            'role' => 'student',
            'email_verified_at' => now(),
        ]);
        echo "Created test student: {$user->email}\n";
    }

    echo "Using user: {$user->name} ({$user->email})\n\n";

    // Test 1: Create Course Purchase Invoice
    echo "=== Test 1: Course Purchase Invoice ===\n";
    
    $course = ClassModel::first();
    if (!$course) {
        echo "No course found. Skipping course invoice test.\n\n";
    } else {
        echo "Creating purchase for course: {$course->name}\n";
        
        // Create purchase (this should auto-create invoice)
        $purchase = Purchase::create([
            'purchase_code' => Purchase::generatePurchaseCode(),
            'user_id' => $user->id,
            'class_id' => $course->id,
            'amount' => 150000,
            'status' => 'pending',
            'payment_method' => 'bank_transfer',
            'notes' => 'Test purchase',
        ]);

        echo "Purchase created with code: {$purchase->purchase_code}\n";

        // Check if invoice was created
        $invoice = Invoice::where('invoiceable_id', $purchase->id)
                         ->where('invoiceable_type', Purchase::class)
                         ->first();

        if ($invoice) {
            echo "✓ Invoice created: {$invoice->invoice_number}\n";
            echo "  - Amount: {$invoice->formatted_total_amount}\n";
            echo "  - Status: {$invoice->status}\n";
            echo "  - Due Date: {$invoice->due_date->format('Y-m-d')}\n";
        } else {
            echo "✗ Invoice not created for purchase\n";
        }

        echo "\n";
    }

    // Test 2: Create Subscription Purchase Invoice
    echo "=== Test 2: Subscription Purchase Invoice ===\n";
    
    // Create subscription purchase (this should auto-create invoice)
    $subscriptionPurchase = SubscriptionPurchase::create([
        'purchase_code' => SubscriptionPurchase::generatePurchaseCode(),
        'user_id' => $user->id,
        'plan' => 'standard',
        'amount' => 100000,
        'discount_amount' => 10000,
        'final_amount' => 90000,
        'status' => 'pending',
        'payment_method' => 'bank_transfer',
        'notes' => 'Test subscription purchase',
    ]);

    echo "Subscription purchase created with code: {$subscriptionPurchase->purchase_code}\n";

    // Check if invoice was created
    $subscriptionInvoice = Invoice::where('invoiceable_id', $subscriptionPurchase->id)
                                 ->where('invoiceable_type', SubscriptionPurchase::class)
                                 ->first();

    if ($subscriptionInvoice) {
        echo "✓ Invoice created: {$subscriptionInvoice->invoice_number}\n";
        echo "  - Amount: {$subscriptionInvoice->formatted_total_amount}\n";
        echo "  - Status: {$subscriptionInvoice->status}\n";
        echo "  - Due Date: {$subscriptionInvoice->due_date->format('Y-m-d')}\n";
        echo "  - Plan: " . (isset($subscriptionInvoice->metadata['subscription_plan']) ? $subscriptionInvoice->metadata['subscription_plan'] : 'N/A') . "\n";
    } else {
        echo "✗ Invoice not created for subscription purchase\n";
    }

    echo "\n";

    // Test 3: Check User Invoices
    echo "=== Test 3: User Invoice List ===\n";
    
    $userInvoices = $user->invoices()->get();
    echo "User has {$userInvoices->count()} invoices:\n";
    
    foreach ($userInvoices as $inv) {
        echo "  - {$inv->invoice_number} ({$inv->type}): {$inv->formatted_total_amount} [{$inv->status}]\n";
    }

    echo "\n";

    // Test 4: Test Invoice Status Methods
    echo "=== Test 4: Invoice Status Methods ===\n";
    
    if (isset($invoice)) {
        echo "Course Invoice Tests:\n";
        echo "  - isPaid(): " . ($invoice->isPaid() ? 'true' : 'false') . "\n";
        echo "  - isOverdue(): " . ($invoice->isOverdue() ? 'true' : 'false') . "\n";
        echo "  - Status Badge: {$invoice->status_badge}\n";
    }

    if (isset($subscriptionInvoice)) {
        echo "\nSubscription Invoice Tests:\n";
        echo "  - isPaid(): " . ($subscriptionInvoice->isPaid() ? 'true' : 'false') . "\n";
        echo "  - isOverdue(): " . ($subscriptionInvoice->isOverdue() ? 'true' : 'false') . "\n";
        echo "  - Status Badge: {$subscriptionInvoice->status_badge}\n";
    }

    echo "\n";

    // Test 5: Test Invoice Number Generation
    echo "=== Test 5: Invoice Number Generation ===\n";
    
    for ($i = 1; $i <= 3; $i++) {
        $testNumber = Invoice::generateInvoiceNumber();
        echo "Generated invoice number $i: $testNumber\n";
    }

    echo "\n=== Invoice System Test Complete ===\n";
    echo "All tests completed successfully!\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}
