<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CheckUserEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:check {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check user email verification status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $users = User::where('email', $email)->get(['id', 'name', 'email', 'email_verified_at', 'password']);

        if ($users->isEmpty()) {
            $this->error("Tidak ada user dengan email '$email'");
            return 1;
        }

        $this->info("Found " . $users->count() . " user(s) dengan email '$email':\n");
        
        foreach ($users as $user) {
            $this->info("─────────────────────────────────────");
            $this->info("ID: {$user->id}");
            $this->info("Name: {$user->name}");
            $this->info("Email: {$user->email}");
            $this->info("Email Verified At: " . ($user->email_verified_at ? $user->email_verified_at : '❌ NOT VERIFIED'));
            $this->info("Password Hash: " . substr($user->password, 0, 20) . "...");
        }
        
        return 0;
    }
}
