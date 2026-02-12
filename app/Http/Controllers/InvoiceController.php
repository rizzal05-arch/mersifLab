<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Course;
use App\Models\ClassModel;
use App\Models\SubscriptionPurchase;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class InvoiceController extends Controller
{
    /**
     * Display a listing of user's invoices
     */
    public function index()
    {
        $user = Auth::user();
        $invoices = $user->invoices()->orderBy('created_at', 'desc')->paginate(10);
        
        return view('invoices.index', compact('invoices'));
    }

    /**
     * Display the specified invoice
     */
    public function show($invoiceNumber)
    {
        $user = Auth::user();
        $invoice = Invoice::where('invoice_number', $invoiceNumber)
                          ->where('user_id', $user->id)
                          ->firstOrFail();

        return view('invoices.show', compact('invoice'));
    }

    /**
     * Create invoice for course purchase
     */
    public function createCourseInvoice(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'amount' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
        ]);

        $user = Auth::user();
        $course = ClassModel::findOrFail($request->class_id);
        
        // Check if user already has a pending invoice for this course
        $existingInvoice = Invoice::where('user_id', $user->id)
                                 ->where('invoiceable_id', $course->id)
                                 ->where('invoiceable_type', ClassModel::class)
                                 ->where('status', 'pending')
                                 ->first();

        if ($existingInvoice) {
            return redirect()->route('invoices.show', $existingInvoice->invoice_number)
                           ->with('info', 'Anda sudah memiliki invoice pending untuk course ini.');
        }

        // Create purchase record first
        $purchase = Purchase::create([
            'purchase_code' => Purchase::generatePurchaseCode(),
            'user_id' => $user->id,
            'class_id' => $course->id,
            'amount' => $request->amount,
            'status' => 'pending',
            'payment_method' => 'bank_transfer',
            'notes' => 'Invoice generated',
        ]);

        // Calculate amounts
        $amount = $request->amount;
        $discountAmount = $request->discount_amount ?? 0;
        $taxAmount = 0; // No tax for now
        $totalAmount = $amount - $discountAmount + $taxAmount;

        // Create invoice
        $invoice = Invoice::create([
            'user_id' => $user->id,
            'type' => 'course',
            'invoiceable_id' => $purchase->id,
            'invoiceable_type' => Purchase::class,
            'amount' => $amount,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'total_amount' => $totalAmount,
            'currency' => 'IDR',
            'status' => 'pending',
            'payment_method' => 'bank_transfer',
            'payment_instructions' => $this->getPaymentInstructions(),
            'metadata' => [
                'course_name' => $course->name,
                'course_description' => $course->description,
                'purchase_code' => $purchase->purchase_code,
            ],
        ]);

        return redirect()->route('invoices.show', $invoice->invoice_number)
                       ->with('success', 'Invoice berhasil dibuat. Silakan lakukan pembayaran.');
    }

    /**
     * Create invoice for subscription purchase
     */
    public function createSubscriptionInvoice(Request $request)
    {
        $request->validate([
            'plan' => 'required|in:standard,premium',
            'amount' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
        ]);

        $user = Auth::user();
        
        // Check if user already has a pending subscription invoice
        $existingInvoice = Invoice::where('user_id', $user->id)
                                 ->where('type', 'subscription')
                                 ->where('status', 'pending')
                                 ->first();

        if ($existingInvoice) {
            return redirect()->route('invoices.show', $existingInvoice->invoice_number)
                           ->with('info', 'Anda sudah memiliki invoice subscription yang pending.');
        }

        // Create subscription purchase record first
        $subscriptionPurchase = SubscriptionPurchase::create([
            'purchase_code' => SubscriptionPurchase::generatePurchaseCode(),
            'user_id' => $user->id,
            'plan' => $request->plan,
            'amount' => $request->amount,
            'discount_amount' => $request->discount_amount ?? 0,
            'final_amount' => $request->amount - ($request->discount_amount ?? 0),
            'status' => 'pending',
            'payment_method' => 'bank_transfer',
        ]);

        // Calculate amounts
        $amount = $request->amount;
        $discountAmount = $request->discount_amount ?? 0;
        $taxAmount = 0; // No tax for now
        $totalAmount = $amount - $discountAmount + $taxAmount;

        // Create invoice
        $invoice = Invoice::create([
            'user_id' => $user->id,
            'type' => 'subscription',
            'invoiceable_id' => $subscriptionPurchase->id,
            'invoiceable_type' => SubscriptionPurchase::class,
            'amount' => $amount,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'total_amount' => $totalAmount,
            'currency' => 'IDR',
            'status' => 'pending',
            'payment_method' => 'bank_transfer',
            'payment_instructions' => $this->getPaymentInstructions(),
            'metadata' => [
                'subscription_plan' => $request->plan,
                'plan_features' => $this->getPlanFeatures($request->plan),
                'purchase_code' => $subscriptionPurchase->purchase_code,
            ],
        ]);

        return redirect()->route('invoices.show', $invoice->invoice_number)
                       ->with('success', 'Invoice subscription berhasil dibuat. Silakan lakukan pembayaran.');
    }

    /**
     * Download invoice as PDF (placeholder for now)
     */
    public function download($invoiceNumber)
    {
        $user = Auth::user();
        $invoice = Invoice::where('invoice_number', $invoiceNumber)
                          ->where('user_id', $user->id)
                          ->firstOrFail();

        // For now, return view. In production, you'd generate PDF here
        return view('invoices.pdf', compact('invoice'));
    }

    /**
     * Get payment instructions
     */
    private function getPaymentInstructions()
    {
        return "Silakan transfer ke:\n\n" .
               "Bank: BCA\n" .
               "No. Rekening: 123-456-7890\n" .
               "A/n: PT MersifLab Education\n\n" .
               "Jumlah: Sesuai total invoice\n\n" .
               "Setelah transfer, kirim bukti pembayaran ke admin.";
    }

    /**
     * Get plan features
     */
    private function getPlanFeatures($plan)
    {
        $features = [
            'standard' => [
                'Access all standard courses',
                'Basic certificate',
                'Email support',
                '1 month validity'
            ],
            'premium' => [
                'Access all courses (standard + premium)',
                'Premium certificate',
                'Priority support',
                'Download materials',
                '1 month validity'
            ]
        ];

        return $features[$plan] ?? [];
    }

    /**
     * Admin: Mark invoice as paid
     */
    public function markAsPaid(Request $request, $invoiceNumber)
    {
        $request->validate([
            'payment_method' => 'required|string',
            'payment_provider' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $invoice = Invoice::where('invoice_number', $invoiceNumber)->firstOrFail();
        
        $invoice->markAsPaid(
            $request->payment_method,
            $request->payment_provider
        );

        if ($request->notes) {
            $invoice->update(['notes' => $request->notes]);
        }

        return redirect()->back()->with('success', 'Invoice berhasil ditandai sebagai lunas.');
    }

    /**
     * Admin: Cancel invoice
     */
    public function cancel($invoiceNumber)
    {
        $invoice = Invoice::where('invoice_number', $invoiceNumber)->firstOrFail();
        $invoice->cancel();

        return redirect()->back()->with('success', 'Invoice berhasil dibatalkan.');
    }

    /**
     * Admin: View all invoices
     */
    public function adminIndex()
    {
        $invoices = Invoice::with('user')
                          ->orderBy('created_at', 'desc')
                          ->paginate(20);

        return view('admin.invoices.index', compact('invoices'));
    }

    /**
     * Admin: Show invoice details
     */
    public function adminShow($invoiceNumber)
    {
        $invoice = Invoice::where('invoice_number', $invoiceNumber)
                          ->with('user')
                          ->firstOrFail();

        return view('admin.invoices.show', compact('invoice'));
    }
}
