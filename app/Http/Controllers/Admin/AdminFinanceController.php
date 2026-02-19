<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\TeacherBalance;
use App\Models\TeacherWithdrawal;
use App\Models\Purchase;
use App\Models\ClassModel;
use App\Models\CommissionSetting;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification as NotificationFacade;

class AdminFinanceController extends Controller
{
    /**
     * Display financial dashboard
     */
    public function dashboard()
    {
        // Calculate platform financial overview
        $totalRevenue = Purchase::where('status', 'success')->sum('amount');
        $totalTeacherPayouts = TeacherWithdrawal::whereIn('status', ['approved', 'processed'])->sum('amount');
        $platformCommission = $totalRevenue - $totalTeacherPayouts;
        $pendingWithdrawals = TeacherWithdrawal::where('status', 'pending')->count();
        $pendingPurchases = Purchase::where('status', 'pending')->count();

        // Teacher statistics
        $teacherStats = User::where('role', 'teacher')
            ->with('teacherBalance')
            ->get()
            ->map(function ($teacher) {
                $balance = $teacher->teacherBalance;
                return [
                    'id' => $teacher->id,
                    'name' => $teacher->name,
                    'email' => $teacher->email,
                    'total_sales' => $this->getTeacherTotalSales($teacher->id),
                    'total_earnings' => $balance ? $balance->total_earnings : 0,
                    'current_balance' => $balance ? $balance->balance : 0,
                    'total_withdrawn' => $balance ? $balance->total_withdrawn : 0,
                    'pending_earnings' => $balance ? $balance->pending_earnings : 0,
                ];
            });

        // Recent transactions
        $recentTransactions = Purchase::with(['user', 'course.teacher'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Recent withdrawal requests
        $recentWithdrawals = TeacherWithdrawal::with('teacher')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.finance.dashboard', compact(
            'totalRevenue',
            'totalTeacherPayouts', 
            'platformCommission',
            'pendingWithdrawals',
            'pendingPurchases',
            'teacherStats',
            'recentTransactions',
            'recentWithdrawals'
        ));
    }

    /**
     * Display teacher financial details
     */
    public function teacherFinance($teacherId)
    {
        $teacher = User::where('role', 'teacher')->findOrFail($teacherId);
        $balance = TeacherBalance::where('teacher_id', $teacherId)->first();
        
        if (!$balance) {
            $balance = TeacherBalance::create([
                'teacher_id' => $teacherId,
                'balance' => 0,
                'total_earnings' => 0,
                'total_withdrawn' => 0,
                'pending_earnings' => 0,
            ]);
        }

        // Get teacher's courses
        $courses = ClassModel::where('teacher_id', $teacherId)
            ->withCount(['purchases' => function ($query) {
                $query->where('status', 'success');
            }])
            ->get();

        // Get transaction history
        $transactions = Purchase::whereHas('course', function ($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })
        ->with(['user', 'course'])
        ->orderBy('created_at', 'desc')
        ->get();

        // Get withdrawal history
        $withdrawals = TeacherWithdrawal::where('teacher_id', $teacherId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get commission settings
        $commissionSettings = CommissionSetting::getForTeacher($teacherId);

        return view('admin.finance.teacher', compact(
            'teacher',
            'balance',
            'courses',
            'transactions',
            'withdrawals',
            'commissionSettings'
        ));
    }

    /**
     * Update commission settings for teacher
     */
    public function updateCommissionSettings(Request $request, $teacherId)
    {
        $validated = $request->validate([
            'commission_type' => 'required|in:fixed,tiered,per_course',
            'platform_percentage' => 'required|numeric|min:0|max:100',
            'teacher_percentage' => 'required|numeric|min:0|max:100',
            'min_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        // Ensure percentages add up to 100
        if (($validated['platform_percentage'] + $validated['teacher_percentage']) != 100) {
            return redirect()->back()
                ->with('error', 'Platform and teacher percentages must add up to 100%')
                ->withInput();
        }

        CommissionSetting::updateOrCreate(
            ['teacher_id' => $teacherId],
            $validated + ['is_active' => true]
        );

        return redirect()->back()
            ->with('success', 'Commission settings updated successfully');
    }

    /**
     * Show withdrawal detail
     */
    public function showWithdrawal($withdrawalId)
    {
        $withdrawal = TeacherWithdrawal::with('teacher')->findOrFail($withdrawalId);
        
        return view('admin.finance.withdrawal-detail', compact('withdrawal'));
    }

    /**
     * Process withdrawal request
     */
    public function processWithdrawal(Request $request, $withdrawalId)
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
            'admin_notes' => 'nullable|string|max:1000',
            'transfer_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'approval_notes' => 'nullable|string|max:500',
        ]);

        $withdrawal = TeacherWithdrawal::findOrFail($withdrawalId);
        $teacherBalance = TeacherBalance::where('teacher_id', $withdrawal->teacher_id)->first();

        if ($validated['status'] === 'approved') {
            if (!$teacherBalance || $teacherBalance->balance < $withdrawal->amount) {
                return redirect()->back()
                    ->with('error', 'Insufficient balance to process this withdrawal');
            }

            // Handle transfer proof file
            $proofPath = null;
            if ($request->hasFile('transfer_proof')) {
                $file = $request->file('transfer_proof');
                $filename = 'withdrawal-' . $withdrawal->id . '-' . time() . '.' . $file->getClientOriginalExtension();
                $proofPath = $file->storeAs('withdrawals/proofs', $filename, 'public');
            }

            // Process withdrawal
            $teacherBalance->processWithdrawal($withdrawal->amount);
            
            // Notify teacher with transfer proof
            $notificationData = [];
            if ($proofPath) {
                $notificationData['transfer_proof'] = $proofPath;
                $notificationData['approval_notes'] = $validated['approval_notes'] ?? null;
            }

            Notification::create([
                'user_id' => $withdrawal->teacher_id,
                'type' => 'withdrawal_approved',
                'title' => 'Penarikan Disetujui & Dana Ditransfer',
                'message' => "Penarikan Rp " . number_format($withdrawal->amount, 0, ',', '.') . " telah disetujui dan dana sudah ditransfer ke rekening Anda.",
                'data' => !empty($notificationData) ? $notificationData : null,
            ]);
        } else {
            // Reject withdrawal
            Notification::create([
                'user_id' => $withdrawal->teacher_id,
                'type' => 'withdrawal_rejected',
                'title' => 'Penarikan Ditolak',
                'message' => "Penarikan Rp " . number_format($withdrawal->amount, 0, ',', '.') . " ditolak. " . $validated['admin_notes'],
            ]);
        }

        $updateData = [
            'status' => $validated['status'],
            'admin_notes' => $validated['admin_notes'],
            'approval_notes' => $validated['approval_notes'] ?? null,
            'processed_at' => now(),
        ];

        if (isset($proofPath)) {
            $updateData['transfer_proof'] = $proofPath;
        }

        $withdrawal->update($updateData);

        return redirect()->back()
            ->with('success', 'Withdrawal processed successfully');
    }

    /**
     * Approve pending earnings
     */
    public function approveEarnings(Request $request, $teacherId)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        $balance = TeacherBalance::where('teacher_id', $teacherId)->first();
        
        if (!$balance || $balance->pending_earnings < $validated['amount']) {
            return redirect()->back()
                ->with('error', 'Insufficient pending earnings to approve');
        }

        $balance->approvePendingEarnings($validated['amount']);

        // Notify teacher
        Notification::create([
            'user_id' => $teacherId,
            'type' => 'earnings_approved',
            'title' => 'Pendapatan Disetujui',
            'message' => "Pendapatan Rp " . number_format($validated['amount'], 0, ',', '.') . " telah disetujui dan ditambahkan ke saldo Anda.",
            'data' => json_encode(['amount' => $validated['amount']])
        ]);

        return redirect()->back()
            ->with('success', 'Earnings approved successfully');
    }

    /**
     * Approve purchase and update teacher earnings
     */
    public function approvePurchase($purchaseId)
    {
        $purchase = Purchase::with(['course', 'user'])->findOrFail($purchaseId);
        
        if ($purchase->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Purchase is already processed');
        }
        
        DB::beginTransaction();
        try {
            // Get commission settings for this teacher
            $commissionSettings = CommissionSetting::getForTeacher($purchase->course->teacher_id);
            
            // Calculate commission based on course type
            if ($purchase->course->commission_type === 'premium') {
                $platformPercentage = 10;
                $teacherPercentage = 90;
            } else {
                $platformPercentage = $commissionSettings->platform_percentage;
                $teacherPercentage = $commissionSettings->teacher_percentage;
            }
            
            $platformCommission = ($purchase->amount * $platformPercentage) / 100;
            $teacherEarning = ($purchase->amount * $teacherPercentage) / 100;
            
            // Update purchase status
            $purchase->update([
                'status' => 'success',
                'approved_at' => now(),
                'platform_commission' => $platformCommission,
                'teacher_earning' => $teacherEarning,
            ]);
            
            // Add to teacher's pending earnings (admin needs to approve)
            $teacherBalance = TeacherBalance::firstOrCreate(
                ['teacher_id' => $purchase->course->teacher_id],
                [
                    'balance' => 0,
                    'total_earnings' => 0,
                    'total_withdrawn' => 0,
                    'pending_earnings' => 0,
                ]
            );
            
            $teacherBalance->addPendingEarnings($teacherEarning);
            
            // Notify teacher
            Notification::create([
                'user_id' => $purchase->course->teacher_id,
                'type' => 'purchase_approved',
                'title' => 'Pembelian Disetujui',
                'message' => "Pembelian course '{$purchase->course->name}' oleh {$purchase->user->name} sebesar Rp " . number_format($teacherEarning, 0, ',', '.') . " telah disetujui. Menunggu persetujuan admin untuk ditambahkan ke saldo.",
                'data' => json_encode(['purchase_id' => $purchase->id, 'amount' => $teacherEarning])
            ]);
            
            // Notify student
            Notification::create([
                'user_id' => $purchase->user_id,
                'type' => 'purchase_approved',
                'title' => 'Pembelian Disetujui',
                'message' => "Pembelian course '{$purchase->course->name}' Anda telah disetujui. Course sekarang tersedia di dashboard Anda.",
                'data' => json_encode(['purchase_id' => $purchase->id])
            ]);
            
            DB::commit();
            
            return redirect()->back()
                ->with('success', 'Purchase approved successfully. Teacher earning: Rp ' . number_format($teacherEarning, 0, ',', '.'));
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Failed to approve purchase: ' . $e->getMessage());
        }
    }

    /**
     * Get teacher total sales
     */
    private function getTeacherTotalSales($teacherId)
    {
        return Purchase::whereHas('course', function ($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })->where('status', 'success')->sum('amount');
    }

    /**
     * Export financial report
     */
    public function exportReport(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'required|in:transactions,withdrawals,summary',
        ]);

        switch ($validated['type']) {
            case 'transactions':
                $data = Purchase::whereBetween('created_at', [
                    $validated['start_date'],
                    $validated['end_date']
                ])
                ->with(['user', 'course.teacher'])
                ->get()
                ->map(function ($purchase) {
                    return [
                        'Date' => $purchase->created_at->format('Y-m-d H:i:s'),
                        'Purchase Code' => $purchase->purchase_code,
                        'Student' => $purchase->user->name,
                        'Teacher' => $purchase->course->teacher->name,
                        'Course' => $purchase->course->name,
                        'Amount' => $purchase->amount,
                        'Status' => $purchase->status,
                    ];
                });
                break;

            case 'withdrawals':
                $data = TeacherWithdrawal::whereBetween('created_at', [
                    $validated['start_date'],
                    $validated['end_date']
                ])
                ->with('teacher')
                ->get()
                ->map(function ($withdrawal) {
                    return [
                        'Date' => $withdrawal->created_at->format('Y-m-d H:i:s'),
                        'Withdrawal Code' => $withdrawal->withdrawal_code,
                        'Teacher' => $withdrawal->teacher->name,
                        'Amount' => $withdrawal->amount,
                        'Bank' => $withdrawal->bank_name,
                        'Status' => $withdrawal->status,
                        'Processed At' => $withdrawal->processed_at?->format('Y-m-d H:i:s'),
                    ];
                });
                break;

            case 'summary':
                $data = User::where('role', 'teacher')
                    ->with('teacherBalance')
                    ->get()
                    ->map(function ($teacher) {
                        $balance = $teacher->teacherBalance;
                        return [
                            'Teacher' => $teacher->name,
                            'Email' => $teacher->email,
                            'Total Sales' => $this->getTeacherTotalSales($teacher->id),
                            'Total Earnings' => $balance ? $balance->total_earnings : 0,
                            'Current Balance' => $balance ? $balance->balance : 0,
                            'Total Withdrawn' => $balance ? $balance->total_withdrawn : 0,
                            'Pending Earnings' => $balance ? $balance->pending_earnings : 0,
                        ];
                    });
                break;
        }

        $filename = 'financial_report_' . $validated['type'] . '_' . $validated['start_date'] . '_to_' . $validated['end_date'] . '.csv';
        
        // Convert to CSV and download
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');
            
            if ($data->isNotEmpty()) {
                fputcsv($file, array_keys($data->first()));
                
                foreach ($data as $row) {
                    fputcsv($file, $row);
                }
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
