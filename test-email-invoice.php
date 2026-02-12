<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing Email Invoice Sending ===\n\n";

try {
    // Get a test user
    $user = \App\Models\User::first();
    if (!$user) {
        echo "❌ No user found in database\n";
        exit(1);
    }
    
    echo "Using user: {$user->name} ({$user->email})\n\n";
    
    // Get a course
    $course = \App\Models\ClassModel::first();
    if (!$course) {
        echo "❌ No course found in database\n";
        exit(1);
    }
    
    echo "Creating purchase for course: {$course->name}\n";
    
    // Create purchase (this should auto-create invoice and send email)
    $purchase = \App\Models\Purchase::create([
        'purchase_code' => \App\Models\Purchase::generatePurchaseCode(),
        'user_id' => $user->id,
        'class_id' => $course->id,
        'status' => 'pending',
        'amount' => 150000,
        'discount_amount' => 0,
        'total_amount' => 150000
    ]);
    
    echo "✓ Purchase created: {$purchase->purchase_code}\n";
    
    // Check if invoice was created
    $invoice = \App\Models\Invoice::where('invoiceable_id', $purchase->id)
                                  ->where('invoiceable_type', \App\Models\Purchase::class)
                                  ->first();
    
    if ($invoice) {
        echo "✓ Invoice created: {$invoice->invoice_number}\n";
        echo "  - Amount: {$invoice->formatted_total_amount}\n";
        echo "  - Status: {$invoice->status}\n";
        echo "  - Due Date: {$invoice->due_date->format('Y-m-d H:i')}\n";
        
        // Test sending email manually
        echo "\n=== Testing Manual Email Send ===\n";
        $emailSent = $invoice->sendInvoiceEmail();
        
        if ($emailSent) {
            echo "✓ Email sent successfully to {$user->email}\n";
        } else {
            echo "❌ Failed to send email\n";
        }
        
    } else {
        echo "❌ No invoice created for purchase\n";
    }
    
    // Check logs
    echo "\n=== Checking Laravel Logs ===\n";
    $logFile = __DIR__ . '/storage/logs/laravel.log';
    if (file_exists($logFile)) {
        $logs = file_get_contents($logFile);
        if (strpos($logs, 'Failed to send invoice email') !== false) {
            echo "❌ Found email sending errors in logs\n";
            // Extract relevant log lines
            $lines = explode("\n", $logs);
            foreach ($lines as $line) {
                if (strpos($line, 'Failed to send invoice email') !== false || 
                    strpos($line, 'invoice') !== false) {
                    echo "  - $line\n";
                }
            }
        } else {
            echo "✓ No email errors found in logs\n";
        }
    } else {
        echo "ℹ️  No log file found\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== Test Complete ===\n";
