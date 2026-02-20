<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'user_id',
        'type',
        'invoiceable_id',
        'invoiceable_type',
        'amount',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'currency',
        'status',
        'payment_method',
        'payment_provider',
        'due_date',
        'paid_at',
        'notes',
        'payment_instructions',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'due_date' => 'datetime',
        'paid_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Generate invoice number when creating
        static::creating(function ($invoice) {
            if (!$invoice->invoice_number) {
                $invoice->invoice_number = self::generateInvoiceNumber();
            }
            
            // Set due date (24 hours from now) for purchase invoices if not set
            // Only apply for purchase types (course/subscription)
            if (!$invoice->due_date && in_array($invoice->type, ['course', 'subscription'])) {
                $invoice->due_date = now()->addHours(24);
            }
        });

        // Send invoice email and create admin notification when created
        static::created(function ($invoice) {
            // Load invoiceItems relationship for email
            $invoice->load('invoiceItems');
            $invoice->sendInvoiceEmail();
            
            // Create notification for admin when invoice is created (user sudah klik "Bayar Sekarang")
            // Hanya untuk course purchases, bukan subscription
            if ($invoice->type === 'course' && \Illuminate\Support\Facades\Schema::hasTable('notifications')) {
                // Get all admin users
                $adminUsers = \App\Models\User::where('role', 'admin')->get();
                
                // Get purchase(s) information
                $purchaseInfo = [];
                
                // Check if this is multiple purchases (from metadata)
                if (isset($invoice->metadata['purchase_ids']) && is_array($invoice->metadata['purchase_ids']) && count($invoice->metadata['purchase_ids']) > 1) {
                    // Multiple purchases
                    $purchases = \App\Models\Purchase::whereIn('id', $invoice->metadata['purchase_ids'])
                        ->with(['user', 'course'])
                        ->get();
                    
                    if ($purchases->isNotEmpty()) {
                        $courseNames = $purchases->pluck('course.name')->filter()->implode(', ');
                        $studentName = $purchases->first()->user->name ?? 'Unknown';
                        $purchaseInfo = [
                            'message' => "Siswa {$studentName} telah meminta invoice untuk {$purchases->count()} course: {$courseNames}",
                            'purchase_ids' => $invoice->metadata['purchase_ids'],
                        ];
                    }
                } else {
                    // Single purchase - try from metadata first, then invoiceable
                    $purchaseId = null;
                    if (isset($invoice->metadata['purchase_ids']) && is_array($invoice->metadata['purchase_ids']) && count($invoice->metadata['purchase_ids']) === 1) {
                        $purchaseId = $invoice->metadata['purchase_ids'][0];
                    } elseif ($invoice->invoiceable_id) {
                        $purchaseId = $invoice->invoiceable_id;
                    }
                    
                    if ($purchaseId) {
                        $purchase = \App\Models\Purchase::with(['user', 'course'])->find($purchaseId);
                        if ($purchase) {
                            $purchaseInfo = [
                                'message' => "Siswa {$purchase->user->name} telah meminta invoice untuk course: {$purchase->course->name}",
                                'purchase_ids' => [$purchaseId],
                            ];
                        }
                    }
                }
                
                if (!empty($purchaseInfo)) {
                    foreach ($adminUsers as $admin) {
                        \App\Models\Notification::create([
                            'user_id' => $admin->id,
                            'type' => 'new_purchase',
                            'title' => 'Permintaan Pembayaran Course Baru',
                            'message' => $purchaseInfo['message'],
                            'notifiable_type' => \App\Models\Purchase::class,
                            'notifiable_id' => $purchaseInfo['purchase_ids'][0] ?? $invoice->id,
                            'is_read' => false,
                        ]);
                    }
                }
            }
        });

        // Auto-expire pending invoices past due date
        static::retrieved(function ($invoice) {
            // Check if invoice is pending and past due date
            // IMPORTANT: Do NOT auto-expire if invoice has been paid (has paid_at or status is 'paid')
            if ($invoice->status === 'pending' && $invoice->due_date && $invoice->due_date->isPast() && !$invoice->paid_at) {
                try {
                    $invoice->expire();
                    
                    // Also expire related purchases if they are still pending
                    if (isset($invoice->metadata['purchase_ids']) && is_array($invoice->metadata['purchase_ids'])) {
                        \App\Models\Purchase::whereIn('id', $invoice->metadata['purchase_ids'])
                            ->where('status', 'pending')
                            ->update(['status' => 'expired']);
                    } elseif ($invoice->invoiceable && $invoice->invoiceable->status === 'pending') {
                        $invoice->invoiceable->update(['status' => 'expired']);
                    }
                } catch (\Exception $e) {
                    // Log error but don't break the application
                    \Log::error('Failed to auto-expire invoice ' . $invoice->invoice_number . ': ' . $e->getMessage());
                }
            }
        });
    }

    /**
     * Generate unique invoice number
     */
    public static function generateInvoiceNumber()
    {
        do {
            $prefix = 'INV';
            $date = now()->format('Ymd');
            $random = str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
            $invoiceNumber = "{$prefix}{$date}{$random}";
        } while (self::where('invoice_number', $invoiceNumber)->exists());
        
        return $invoiceNumber;
    }

    /**
     * Get the user that owns the invoice
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent invoiceable model (Course or SubscriptionPurchase)
     */
    public function invoiceable()
    {
        return $this->morphTo();
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'paid' => 'success',
            'pending' => 'warning',
            'expired' => 'danger',
            'cancelled' => 'secondary',
            default => 'secondary',
        };
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute()
    {
        return 'Rp' . number_format($this->amount, 0, ',', '.');
    }

    /**
     * Get formatted tax amount
     */
    public function getFormattedTaxAmountAttribute()
    {
        return 'Rp' . number_format($this->tax_amount, 0, ',', '.');
    }

    /**
     * Get formatted discount amount
     */
    public function getFormattedDiscountAmountAttribute()
    {
        return 'Rp' . number_format($this->discount_amount, 0, ',', '.');
    }

    /**
     * Get formatted total amount
     */
    public function getFormattedTotalAmountAttribute()
    {
        return 'Rp' . number_format($this->total_amount, 0, ',', '.');
    }

    /**
     * Get payment deadline (formatted)
     */
    public function getPaymentDeadlineAttribute()
    {
        if (!$this->due_date) {
            return null;
        }

        return [
            'datetime' => $this->due_date->format('Y-m-d H:i:s'),
            'readable' => $this->due_date->format('d M Y H:i'),
            'remaining' => $this->due_date->isPast() ? 'Expired' : $this->due_date->diffForHumans(),
        ];
    }

    /**
     * Check if invoice is overdue
     */
    public function isOverdue(): bool
    {
        return $this->status === 'pending' && $this->due_date && $this->due_date->isPast();
    }

    /**
     * Check if invoice is paid
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * Mark invoice as paid
     */
    public function markAsPaid(string $paymentMethod = null, string $paymentProvider = null)
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
            'payment_method' => $paymentMethod ?? $this->payment_method,
            'payment_provider' => $paymentProvider ?? $this->payment_provider,
        ]);

        // Update related purchase/subscription if needed
        if ($this->type === 'course') {
            // Check if this invoice has multiple purchases
            if (isset($this->metadata['purchase_ids']) && is_array($this->metadata['purchase_ids']) && count($this->metadata['purchase_ids']) > 1) {
                // Unlock all purchases associated with this invoice
                $purchaseIds = $this->metadata['purchase_ids'];
                $purchases = Purchase::whereIn('id', $purchaseIds)->get();
                
                foreach ($purchases as $purchase) {
                    if (method_exists($purchase, 'unlockCourse')) {
                        $purchase->unlockCourse();
                    }
                }
            } else {
                // Single purchase - unlock the invoiceable purchase
                if ($this->invoiceable && method_exists($this->invoiceable, 'unlockCourse')) {
                    $this->invoiceable->unlockCourse();
                }
            }
        } elseif ($this->type === 'subscription' && $this->invoiceable && method_exists($this->invoiceable, 'activateSubscription')) {
            $this->invoiceable->activateSubscription();
        }
    }

    /**
     * Cancel invoice
     */
    public function cancel()
    {
        $this->update([
            'status' => 'cancelled',
            'notes' => ($this->notes ?? '') . ' - Cancelled on ' . now()->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Expire invoice (mark as expired)
     */
    public function expire()
    {
        $this->update([
            'status' => 'expired',
            'notes' => ($this->notes ?? '') . ' - Expired on ' . now()->format('Y-m-d H:i:s'),
        ]);

        // If invoice links to a purchase/subscription, mark them expired as well (if not already paid)
        if ($this->invoiceable && property_exists($this->invoiceable, 'status') && $this->invoiceable->status !== 'success') {
            try {
                $this->invoiceable->update(['status' => 'expired']);
            } catch (\Exception $e) {
                // ignore update failures
            }
        }
    }

    /**
     * Send invoice email
     */
    public function sendInvoiceEmail()
    {
        try {
            $this->user->notify(new \App\Notifications\InvoiceNotification($this));
            
            // Log activity
            if (method_exists($this->user, 'logActivity')) {
                $this->user->logActivity('invoice_sent', 'Invoice ' . $this->invoice_number . ' sent via email');
            }
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to send invoice email', [
                'invoice_id' => $this->id,
                'invoice_number' => $this->invoice_number,
                'user_id' => $this->user_id,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    /**
     * Get invoice title based on type
     */
    public function getTitleAttribute()
    {
        if ($this->type === 'course') {
            return 'Invoice Pembelian Course';
        } elseif ($this->type === 'subscription') {
            return 'Invoice Pembelian Subscription';
        }
        
        return 'Invoice';
    }

    /**
     * Get item description
     */
    public function getItemDescriptionAttribute()
    {
        if ($this->type === 'course') {
            // Try to get course name from metadata first
            if (isset($this->metadata['course_name'])) {
                return $this->metadata['course_name'];
            }
            // Fallback to invoiceable relationship
            if ($this->invoiceable) {
                return 'Course: ' . ($this->invoiceable->course->name ?? $this->invoiceable->course->title ?? 'Course Tidak Diketahui');
            }
        } elseif ($this->type === 'subscription') {
            // Try to get plan from metadata first
            if (isset($this->metadata['subscription_plan'])) {
                return 'Subscription: ' . ucfirst($this->metadata['subscription_plan']);
            }
            // Fallback to invoiceable relationship
            if ($this->invoiceable) {
                return 'Subscription: ' . ucfirst($this->invoiceable->plan ?? 'Unknown Plan');
            }
        }
        
        return 'Item';
    }

    /**
     * Scope: Pending invoices
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Paid invoices
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope: Overdue invoices
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', 'pending')
                     ->where('due_date', '<', now());
    }

    /**
     * Get invoice items
     */
    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * Scope: For specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Create invoice items from cart data
     */
    public function createInvoiceItems($items)
    {
        foreach ($items as $item) {
            $this->invoiceItems()->create([
                'purchase_id' => $item['purchase_id'] ?? null,
                'course_id' => $item['course_id'] ?? null,
                'item_name' => $item['name'] ?? $item['title'] ?? 'Course Item',
                'item_description' => $item['description'] ?? null,
                'amount' => $item['amount'] ?? $item['price'] ?? 0,
                'tax_amount' => $item['tax_amount'] ?? 0,
                'discount_amount' => $item['discount_amount'] ?? 0,
                'total_amount' => $item['total_amount'] ?? $item['amount'] ?? $item['price'] ?? 0,
                'currency' => $item['currency'] ?? 'IDR',
                'metadata' => $item['metadata'] ?? [
                    'purchase_code' => $item['purchase_code'] ?? null,
                    'course_id' => $item['course_id'] ?? null,
                ],
            ]);
        }
    }
}
