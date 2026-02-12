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
            
            // Set due date (24 hours from now) if not set
            if (!$invoice->due_date) {
                $invoice->due_date = now()->addHours(24);
            }
        });

        // Send invoice email when created
        static::created(function ($invoice) {
            $invoice->sendInvoiceEmail();
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
        if ($this->invoiceable) {
            if ($this->type === 'course' && method_exists($this->invoiceable, 'unlockCourse')) {
                $this->invoiceable->unlockCourse();
            } elseif ($this->type === 'subscription' && method_exists($this->invoiceable, 'activateSubscription')) {
                $this->invoiceable->activateSubscription();
            }
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
     * Scope: For specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
