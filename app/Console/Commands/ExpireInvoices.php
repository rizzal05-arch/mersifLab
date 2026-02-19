<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;

class ExpireInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire pending invoices that passed their due date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for overdue invoices...');

        $invoices = Invoice::where('status', 'pending')
            ->whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->get();

        $count = $invoices->count();

        if ($count === 0) {
            $this->info('No overdue invoices found.');
            return 0;
        }

        foreach ($invoices as $invoice) {
            try {
                $invoice->expire();
                $this->line("Expired invoice: {$invoice->invoice_number} (id: {$invoice->id})");
            } catch (\Exception $e) {
                $this->error("Failed to expire invoice {$invoice->id}: {$e->getMessage()}");
            }
        }

        $this->info("Done. {$count} invoices processed.");
        return 0;
    }
}
