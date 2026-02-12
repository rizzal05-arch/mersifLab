<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class VerifyUserEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:verify {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify user email by email address';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User dengan email '$email' tidak ditemukan.");
            return 1;
        }

        if ($user->email_verified_at) {
            $this->info("Email user '{$user->email}' sudah diverifikasi pada {$user->email_verified_at}");
            return 0;
        }

        // Verify the email
        $user->update([
            'email_verified_at' => now(),
            'email_verification_token' => null,
            'email_verification_sent_at' => null,
        ]);

        $this->info("Email user '{$user->email}' berhasil diverifikasi!");
        return 0;
    }
}
