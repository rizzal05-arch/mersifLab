<?php

namespace App\Console\Commands;

use App\Models\Purchase;
use App\Models\TeacherBalance;
use Illuminate\Console\Command;

class UpdateTeacherBalances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'finance:update-balances {--force : Force update existing balances}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update teacher balances from successful purchases';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting teacher balance update...');

        // Get all successful purchases with course and teacher relationship
        $purchases = Purchase::where('status', 'success')
            ->with(['course' => function ($query) {
                $query->select('id', 'teacher_id', 'name');
            }])
            ->get();

        $this->info("Found {$purchases->count()} successful purchases");

        $updated = 0;
        $errors = 0;

        foreach ($purchases as $purchase) {
            try {
                if (!$purchase->course) {
                    $this->warn("Purchase {$purchase->id} has no course");
                    $errors++;
                    continue;
                }

                $teacher = $purchase->course->teacher;
                if (!$teacher) {
                    $this->warn("Course {$purchase->course->id} has no teacher");
                    $errors++;
                    continue;
                }

                // Get or create teacher balance
                $balance = TeacherBalance::firstOrCreate(
                    ['teacher_id' => $teacher->id],
                    [
                        'balance' => 0,
                        'total_earnings' => 0,
                        'total_withdrawn' => 0,
                        'pending_earnings' => 0,
                    ]
                );

                // Calculate earnings
                $earnings = $purchase->teacher_earning ?? $purchase->amount;

                // Check if this purchase already contributed to balance
                $currentEarnings = $balance->total_earnings;
                $expectedEarnings = Purchase::where('status', 'success')
                    ->whereHas('course', function ($q) use ($teacher) {
                        $q->where('teacher_id', $teacher->id);
                    })
                    ->sum(\DB::raw('COALESCE(teacher_earning, amount)'));

                // Only update if difference exists
                if ($currentEarnings < $expectedEarnings) {
                    $balance->total_earnings = $expectedEarnings;
                    $balance->balance = $expectedEarnings - $balance->total_withdrawn;
                    $balance->last_updated = now();
                    $balance->save();
                    $updated++;

                    $this->line("Updated balance for teacher {$teacher->id} ({$teacher->name})");
                }
            } catch (\Exception $e) {
                $this->error("Error processing purchase {$purchase->id}: {$e->getMessage()}");
                $errors++;
            }
        }

        $this->info("Update completed!");
        $this->line("✓ Updated: $updated");
        $this->line("✗ Errors: $errors");
    }
}
