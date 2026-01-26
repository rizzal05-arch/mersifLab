<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AdminManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $admins = User::where('role', 'admin')
            ->with('createdBy')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($admin) {
                return [
                    'id' => $admin->id,
                    'name' => $admin->name,
                    'email' => $admin->email,
                    'username' => $admin->name, // Using name as username for now
                    'password' => '••••••••', // Masked password
                    'role' => $admin->getAdminRoleLabel(),
                    'created_by' => $admin->createdBy ? $admin->createdBy->name : 'System',
                    'created_at' => $admin->created_at->format('Y-m-d'),
                    'last_login' => $admin->last_login_at ? $admin->last_login_at->diffForHumans() : 'Never',
                    'last_login_raw' => $admin->last_login_at ? $admin->last_login_at->format('Y-m-d H:i:s') : null,
                    'is_active' => $admin->isActive(),
                    'is_online' => $this->isUserOnline($admin),
                ];
            });

        return view('admin.admins.index', compact('admins'));
    }

    /**
     * Check if user is online (based on active session and last login)
     */
    private function isUserOnline($user): bool
    {
        // First check if user has recent login (within 15 minutes)
        if (!$user->last_login_at) {
            return false;
        }
        
        // Check if last login was within last 15 minutes
        $lastLoginMinutesAgo = $user->last_login_at->diffInMinutes(now());
        if ($lastLoginMinutesAgo > 15) {
            return false;
        }
        
        // Additional check: verify if user has active session
        // This is a more reliable way to determine if user is actually online
        try {
            // Check if there's an active session for this user
            $activeSession = \DB::table('sessions')
                ->where('user_id', $user->id)
                ->where('last_activity', '>', now()->subMinutes(15)->timestamp)
                ->exists();
            
            return $activeSession;
        } catch (\Exception $e) {
            // Fallback to last login check if session table doesn't exist or has issues
            return $lastLoginMinutesAgo <= 15;
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.admins.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', 'in:admin'],
        ]);

        $admin = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin',
            'created_by' => auth()->id(),
            'is_active' => true,
        ]);

        // Log activity
        auth()->user()->logActivity('admin_created', "Created new admin: {$admin->name} ({$admin->email})");

        return redirect()->route('admin.admins.index')->with('success', 'Admin created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $admin = User::where('role', 'admin')
            ->with(['createdBy', 'activityLogs' => function ($query) {
                $query->latest()->limit(50);
            }])
            ->findOrFail($id);

        // Format activity logs
        $activities = $admin->activityLogs->map(function ($log) {
            return [
                'action' => $log->action,
                'description' => $log->description,
                'time_ago' => $log->created_at->diffForHumans(),
                'created_at' => $log->created_at->format('Y-m-d H:i:s'),
            ];
        });

        // Calculate statistics
        $statistics = [
            'users_created' => $admin->createdUsers->count(),
            'total_activities' => $admin->activityLogs->count(),
            'days_active' => number_format($admin->created_at->diffInDays(now()), 0),
        ];

        return view('admin.admins.show', compact('admin', 'activities', 'statistics'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $admin = User::where('role', 'admin')->findOrFail($id);
        return view('admin.admins.edit', compact('admin'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $admin = User::where('role', 'admin')->findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $admin->id],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
            
            // Send password reset notification
            // TODO: Implement email notification
        }

        $admin->update($updateData);

        // Log activity
        auth()->user()->logActivity('admin_updated', "Updated admin: {$admin->name} ({$admin->email})");

        return redirect()->route('admin.admins.index')->with('success', 'Admin updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $admin = User::where('role', 'admin')->findOrFail($id);
        
        // Prevent deletion of self
        if ($admin->id === auth()->id()) {
            return redirect()->route('admin.admins.index')->with('error', 'You cannot delete your own account');
        }

        // Prevent deletion of the first admin (super admin)
        $adminCount = User::where('role', 'admin')->count();
        if ($adminCount === 1) {
            return redirect()->route('admin.admins.index')->with('error', 'Cannot delete the last admin account');
        }

        $adminName = $admin->name;
        $admin->delete();

        // Log activity
        auth()->user()->logActivity('admin_deleted', "Deleted admin: {$adminName}");

        return redirect()->route('admin.admins.index')->with('success', 'Admin deleted successfully');
    }

    /**
     * Toggle admin active status
     */
    public function toggleStatus(string $id)
    {
        $admin = User::where('role', 'admin')->findOrFail($id);
        
        // Prevent deactivation of self
        if ($admin->id === auth()->id()) {
            return redirect()->route('admin.admins.index')->with('error', 'You cannot change your own account status');
        }

        // Prevent deactivation of the first admin (super admin)
        if ($admin->isSuperAdmin()) {
            return redirect()->route('admin.admins.index')->with('error', 'Cannot change status of super admin account');
        }

        $admin->toggleActiveStatus();
        $status = $admin->isActive() ? 'activated' : 'deactivated';

        // Log activity
        auth()->user()->logActivity('admin_status_changed', "Admin {$admin->name} ({$admin->email}) was {$status}");

        return redirect()->route('admin.admins.index')->with('success', "Admin {$status} successfully");
    }
}
