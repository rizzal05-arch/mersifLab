<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_code',
        'user_id',
        'class_id',
        'amount',
        'status',
        'payment_method',
        'payment_provider',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        // Create notification for admin when new purchase is made
        static::created(function ($purchase) {
            if (Schema::hasTable('notifications')) {
                // Get all admin users
                $adminUsers = User::where('role', 'admin')->get();
                
                foreach ($adminUsers as $admin) {
                    Notification::create([
                        'user_id' => $admin->id,
                        'type' => 'new_purchase',
                        'title' => 'Pembelian Course Baru',
                        'message' => "Siswa {$purchase->user->name} telah membeli course {$purchase->course->name}",
                        'notifiable_type' => Purchase::class,
                        'notifiable_id' => $purchase->id,
                        'is_read' => false,
                    ]);
                }
            }

            // JANGAN auto-create invoice di sini
            // Invoice hanya akan dibuat saat user klik "Bayar Sekarang" di halaman checkout
            // Ini memastikan invoice hanya dikirim jika user benar-benar ingin membayar
            // Jika user kembali tanpa klik "Bayar Sekarang", purchase tetap pending tanpa invoice
            $skipAutoInvoice = session('skip_auto_invoice', true); // Default true untuk mencegah auto-create
            
            // Hanya create invoice jika status 'success' (langsung dibayar tanpa checkout) 
            // atau jika explicitly di-set untuk create invoice (bukan dari checkout flow)
            if ($purchase->status === 'success' && !$skipAutoInvoice) {
                // Load course relationship to get proper course name
                $purchase->load('course');
                
                Invoice::create([
                    'user_id' => $purchase->user_id,
                    'type' => 'course',
                    'invoiceable_id' => $purchase->id,
                    'invoiceable_type' => Purchase::class,
                    'amount' => $purchase->amount,
                    'tax_amount' => 0,
                    'discount_amount' => 0,
                    'total_amount' => $purchase->amount,
                    'currency' => 'IDR',
                    'status' => 'paid', // Auto-paid karena purchase sudah success
                    'payment_method' => $purchase->payment_method ?? 'bank_transfer',
                    'payment_provider' => $purchase->payment_provider ?? 'manual',
                    'paid_at' => now(),
                    'metadata' => [
                        'course_name' => $purchase->course->name ?? 'Course Tidak Diketahui',
                        'course_description' => $purchase->course->description ?? '',
                        'purchase_code' => $purchase->purchase_code,
                    ],
                ]);
            }
        });
    }

    /**
     * Generate unique purchase code
     */
    public static function generatePurchaseCode()
    {
        do {
            $code = 'ML-' . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
        } while (self::where('purchase_code', $code)->exists());
        
        return $code;
    }

    /**
     * Get the user that owns the purchase
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the course that was purchased
     */
    public function course()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
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
     * Unlock course for student (mark as paid)
     */
    public function unlockCourse()
    {
        $this->update([
            'status' => 'success',
            'paid_at' => now(),
            'payment_provider' => $this->payment_provider ?? 'whatsapp',
            'notes' => ($this->notes ?? '') . ' - Unlocked by admin via WhatsApp confirmation',
        ]);

        // Enroll student to course if not already enrolled
        $this->enrollStudent();

        // Create notification for student
        if (Schema::hasTable('notifications')) {
            Notification::create([
                'user_id' => $this->user_id,
                'type' => 'course_unlocked',
                'title' => 'Course Aktif!',
                'message' => "Course {$this->course->name} sudah aktif dan dapat Anda akses. Selamat belajar!",
                'notifiable_type' => Purchase::class,
                'notifiable_id' => $this->id,
                'is_read' => false,
            ]);
        }
    }

    /**
     * Enroll student to course
     */
    public function enrollStudent()
    {
        // Check if student is already enrolled
        $alreadyEnrolled = DB::table('class_student')
            ->where('class_id', $this->class_id)
            ->where('user_id', $this->user_id)
            ->exists();

        if (!$alreadyEnrolled) {
            // Enroll student
            DB::table('class_student')->insert([
                'class_id' => $this->class_id,
                'user_id' => $this->user_id,
                'progress' => 0,
                'enrolled_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
