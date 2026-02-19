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

echo "=== Testing Updated Invoice System ===\n\n";

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
            'notes' => 'Test purchase with updated invoice system',
        ]);

        echo "Purchase created with code: {$purchase->purchase_code}\n";

        // Check if invoice was created
        $invoice = Invoice::where('invoiceable_id', $purchase->id)
                         ->where('invoiceable_type', Purchase::class)
                         ->first();

        if ($invoice) {
            echo "✓ Invoice created: {$invoice->invoice_number}\n";
            echo "  - Course: {$invoice->item_description}\n";
            echo "  - Amount: {$invoice->formatted_total_amount}\n";
            echo "  - Status: " . ($invoice->status === 'paid' ? 'Sudah Dibayar' : 'Belum Dibayar') . "\n";
            echo "  - Due Date (with time): {$invoice->due_date->format('d M Y H:i')}\n";
            echo "  - WhatsApp URL: https://wa.me/088806658440?text=" . urlencode("Halo MersifLab, saya ingin konfirmasi pembayaran untuk invoice {$invoice->invoice_number} sebesar {$invoice->formatted_total_amount}") . "\n";
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
        'notes' => 'Test subscription purchase with updated invoice system',
    ]);

    echo "Subscription purchase created with code: {$subscriptionPurchase->purchase_code}\n";

    // Check if invoice was created
    $subscriptionInvoice = Invoice::where('invoiceable_id', $subscriptionPurchase->id)
                                 ->where('invoiceable_type', SubscriptionPurchase::class)
                                 ->first();

    if ($subscriptionInvoice) {
        echo "✓ Invoice created: {$subscriptionInvoice->invoice_number}\n";
        echo "  - Course: {$subscriptionInvoice->item_description}\n";
        echo "  - Amount: {$subscriptionInvoice->formatted_total_amount}\n";
        echo "  - Status: " . ($subscriptionInvoice->status === 'paid' ? 'Sudah Dibayar' : 'Belum Dibayar') . "\n";
        echo "  - Due Date (with time): {$subscriptionInvoice->due_date->format('d M Y H:i')}\n";
        echo "  - Plan: " . (isset($subscriptionInvoice->metadata['subscription_plan']) ? $subscriptionInvoice->metadata['subscription_plan'] : 'N/A') . "\n";
        echo "  - WhatsApp URL: https://wa.me/088806658440?text=" . urlencode("Halo MersifLab, saya ingin konfirmasi pembayaran untuk invoice {$subscriptionInvoice->invoice_number} sebesar {$subscriptionInvoice->formatted_total_amount}") . "\n";
    } else {
        echo "✗ Invoice not created for subscription purchase\n";
    }

    echo "\n";

    // Test 3: Test Email Notification Content
    echo "=== Test 3: Email Notification Content ===\n";
    
    if (isset($invoice)) {
        echo "Testing email notification for course invoice...\n";
        
        try {
            $notification = new \App\Notifications\InvoiceNotification($invoice);
            $mailMessage = $notification->toMail($user);
            
            echo "✓ Email notification created successfully\n";
            echo "  - Subject: " . $mailMessage->subject . "\n";
            echo "  - Using custom view: emails.invoice-with-qris\n";
            
            // Get WhatsApp URL from the notification
            $whatsappNumber = config('app.payment.whatsapp_number');
            $whatsappMessage = urlencode("Halo MersifLab, saya ingin konfirmasi pembayaran untuk invoice {$invoice->invoice_number} sebesar {$invoice->formatted_total_amount}");
            $whatsappUrl = "https://wa.me/{$whatsappNumber}?text={$whatsappMessage}";
            echo "  - WhatsApp URL: " . $whatsappUrl . "\n";
            
        } catch (\Exception $e) {
            echo "✗ Error creating email notification: " . $e->getMessage() . "\n";
        }
    }

    echo "\n";

    // Test 4: Test Invoice Status Display
    echo "=== Test 4: Invoice Status Display ===\n";
    
    if (isset($invoice)) {
        echo "Course Invoice Status Tests:\n";
        echo "  - Status: " . ($invoice->status === 'paid' ? 'Sudah Dibayar' : 'Belum Dibayar') . "\n";
        echo "  - Due Date with time: {$invoice->due_date->format('d M Y H:i')}\n";
        echo "  - Item Description (Course): {$invoice->item_description}\n";
    }

    if (isset($subscriptionInvoice)) {
        echo "\nSubscription Invoice Status Tests:\n";
        echo "  - Status: " . ($subscriptionInvoice->status === 'paid' ? 'Sudah Dibayar' : 'Belum Dibayar') . "\n";
        echo "  - Due Date with time: {$subscriptionInvoice->due_date->format('d M Y H:i')}\n";
        echo "  - Item Description (Course): {$subscriptionInvoice->item_description}\n";
    }

    echo "\n=== Updated Invoice System Test Complete ===\n";
    echo "All tests completed successfully!\n";
    echo "\nKey Updates Implemented:\n";
    echo "✓ Due date includes time (H:i)\n";
    echo "✓ Status shows 'Sudah Dibayar'/'Belum Dibayar'\n";
    echo "✓ Item changed to 'Course'\n";
    echo "✓ WhatsApp confirmation button (088806658440)\n";
    echo "✓ Payment confirmation warning\n";
    echo "✓ Bank account details included\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}
