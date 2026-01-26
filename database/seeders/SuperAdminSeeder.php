<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\AdminPermission;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if super admin already exists
        $existingSuperAdmin = User::where('role', 'admin')->oldest()->first();
        
        if ($existingSuperAdmin) {
            $this->command->info('Super admin already exists: ' . $existingSuperAdmin->email);
            return;
        }

        // Create super admin
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@mersiflab.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'is_active' => true,
            'is_banned' => false,
        ]);

        // Grant all permissions to super admin
        $permissions = AdminPermission::getAvailablePermissions();
        $grantedBy = $superAdmin->id; // Self-granted

        foreach ($permissions as $permission => $label) {
            AdminPermission::create([
                'user_id' => $superAdmin->id,
                'permission' => $permission,
                'granted' => true,
                'granted_by' => $grantedBy,
            ]);
        }

        // Log the creation
        $superAdmin->logActivity('admin_created', "Super admin account created: {$superAdmin->name} ({$superAdmin->email})");

        $this->command->info('Super admin created successfully!');
        $this->command->info('Email: admin@mersiflab.com');
        $this->command->info('Password: admin123');
        $this->command->warn('Please change the password after first login.');
    }
}
