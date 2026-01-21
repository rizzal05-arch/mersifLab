<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index()
    {
        // Dummy settings data
        $settings = [
            'site_name' => 'Mersif Learning Platform',
            'site_description' => 'Advanced Learning Management System',
            'site_logo' => 'images/logo.png',
            'admin_email' => 'admin@mersif.com',
            'maintenance_mode' => false,
            'registration_enabled' => true,
            'email_notifications' => true
        ];

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update the settings.
     */
    public function update(Request $request)
    {
        // Implementation for updating settings
        return redirect()->route('admin.settings.index')->with('success', 'Settings updated successfully');
    }

    /**
     * Upload logo.
     */
    public function uploadLogo(Request $request)
    {
        // Implementation for logo upload
        return redirect()->route('admin.settings.index')->with('success', 'Logo uploaded successfully');
    }
}
