<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create-admin {--name= : The admin name} {--email= : The admin email} {--password= : The admin password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->option('name') ?? $this->ask('Admin name');
        $email = $this->option('email') ?? $this->ask('Admin email');
        $password = $this->option('password') ?? $this->secret('Admin password');

        if (User::where('email', $email)->exists()) {
            $this->error("User with email {$email} already exists");
            return Command::FAILURE;
        }

        User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'admin',
        ]);

        $this->info("Admin user {$email} created successfully!");
        return Command::SUCCESS;
    }
}
