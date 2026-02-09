<?php

namespace App\Console\Commands;

use App\Http\Controllers\CertificateController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateMissingCertificates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'certificates:generate-missing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate certificates for all completed courses that dont have certificates yet';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for completed courses without certificates...');
        
        // Find users who have completed courses but don't have certificates
        $completedEnrollments = DB::table('class_student')
            ->where('progress', 100)
            ->whereNotExists(function($query) {
                $query->select(DB::raw(1))
                    ->from('certificates')
                    ->whereRaw('certificates.user_id = class_student.user_id')
                    ->whereRaw('certificates.course_id = class_student.class_id')
                    ->where('certificates.status', 'active');
            })
            ->get();

        if ($completedEnrollments->count() === 0) {
            $this->info('No missing certificates found. All completed courses have certificates!');
            return 0;
        }

        $this->info("Found {$completedEnrollments->count()} completed courses without certificates.");
        
        $certificateController = new CertificateController();
        $generated = 0;
        $failed = 0;

        $progressBar = $this->output->createProgressBar($completedEnrollments->count());
        $progressBar->start();

        foreach ($completedEnrollments as $enrollment) {
            $certificate = $certificateController->generateCertificate($enrollment->user_id, $enrollment->class_id);
            
            if ($certificate) {
                $generated++;
                $this->line("✓ Generated: {$certificate->certificate_code} for User {$enrollment->user_id}, Course {$enrollment->class_id}");
            } else {
                $failed++;
                $this->line("✗ Failed to generate certificate for User {$enrollment->user_id}, Course {$enrollment->class_id}");
            }
            
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();
        
        $this->info("Certificate generation completed:");
        $this->info("✓ Generated: {$generated} certificates");
        if ($failed > 0) {
            $this->error("✗ Failed: {$failed} certificates");
        }
        
        return 0;
    }
}
