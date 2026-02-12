<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class SubscriptionPurchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_code',
        'user_id',
        'plan',
        'amount',
        'discount_amount',
        'final_amount',
        'status',
        'payment_method',
        'payment_provider',
        'paid_at',
        'expires_at',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        // Create notification for admin when new subscription purchase is made
        static::created(function ($purchase) {
            if (Schema::hasTable('notifications')) {
                // Get all admin users
                $adminUsers = User::where('role', 'admin')->get();
                
                foreach ($adminUsers as $admin) {
                    Notification::create([
                        'user_id' => $admin->id,
                        'type' => 'new_subscription_purchase',
                        'title' => 'Pembelian Subscription Baru',
                        'message' => "Siswa {$purchase->user->name} telah membeli paket {$purchase->plan}",
                        'notifiable_type' => SubscriptionPurchase::class,
                        'notifiable_id' => $purchase->id,
                        'is_read' => false,
                    ]);
                }
            }
        });
    }

    /**
     * Generate unique purchase code for subscription
     */
    public static function generatePurchaseCode()
    {
        do {
            $code = 'SUB-' . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
        } while (self::where('purchase_code', $code)->exists());
        
        return $code;
    }

    /**
     * Get the user that owns the subscription purchase
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'success' => 'success',
            'pending' => 'warning',
            'expired' => 'danger',
            'cancelled' => 'secondary',
            default => 'secondary',
        };
    }

    /**
     * Activate subscription (mark as paid and update user)
     */
    public function activateSubscription()
    {
        $this->update([
            'status' => 'success',
            'paid_at' => now(),
            'payment_provider' => $this->payment_provider ?? 'whatsapp',
            'expires_at' => now()->addMonth(),
            'notes' => ($this->notes ?? '') . ' - Activated by admin via WhatsApp confirmation',
        ]);

        // Update user subscription
        $this->user->update([
            'is_subscriber' => true,
            'subscription_plan' => $this->plan,
            'subscription_expires_at' => $this->expires_at,
        ]);

        // Create notification for student
        if (Schema::hasTable('notifications')) {
            Notification::create([
                'user_id' => $this->user_id,
                'type' => 'subscription_activated',
                'title' => 'Subscription Aktif!',
                'message' => "Paket {$this->plan} Anda sudah aktif! Anda sekarang dapat mengakses semua course sesuai paket Anda. Selamat belajar!",
                'notifiable_type' => SubscriptionPurchase::class,
                'notifiable_id' => $this->id,
                'is_read' => false,
            ]);
        }

        // Log activity if method exists
        if (method_exists($this->user, 'logActivity')) {
            $this->user->logActivity('subscription_activated', 'Subscription activated for ' . ucfirst($this->plan) . ' plan - expires: ' . $this->expires_at->format('Y-m-d'));
        }
    }

    /**
     * Get formatted plan name
     */
    public function getFormattedPlanAttribute()
    {
        return ucfirst($this->plan);
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute()
    {
        return 'Rp' . number_format($this->amount, 0, ',', '.');
    }

    /**
     * Get formatted final amount
     */
    public function getFormattedFinalAmountAttribute()
    {
        return 'Rp' . number_format($this->final_amount, 0, ',', '.');
    }
}
