<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Purchase;
use App\Models\Invoice;

class CleanupStalePurchases extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'purchases:cleanup-stale';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up stale pending purchases older than 24 hours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Cleaning up stale pending purchases...');
        
        // Find pending purchases older than 24 hours
        $stalePurchases = Purchase::where('status', 'pending')
            ->where('created_at', '<', now()->subHours(24))
            ->get();
        
        $count = $stalePurchases->count();
        
        if ($count === 0) {
            $this->info('No stale pending purchases found.');
            return 0;
        }
        
        $this->info("Found {$count} stale pending purchases.");
        
        // Check if any of these have invoices
        $purchasesWithInvoices = 0;
        foreach ($stalePurchases as $purchase) {
            $hasInvoice = Invoice::where('invoiceable_id', $purchase->id)
                ->where('invoiceable_type', Purchase::class)
                ->exists();
            
            if ($hasInvoice) {
                $purchasesWithInvoices++;
                $this->warn("Purchase #{$purchase->id} has an invoice, skipping deletion.");
            } else {
                // Delete the stale purchase without invoice
                $purchase->delete();
                $this->line("Deleted stale purchase #{$purchase->id}");
            }
        }
        
        $deletedCount = $count - $purchasesWithInvoices;
        $this->info("Cleanup completed. Deleted {$deletedCount} stale purchases.");
        
        return 0;
    }
}
