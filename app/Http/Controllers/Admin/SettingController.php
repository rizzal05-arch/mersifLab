<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class SettingController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index()
    {
        // Get settings from database or use defaults
        $settings = [
            'site_name' => Setting::get('site_name', 'Mersif Learning Platform'),
            'site_description' => Setting::get('site_description', 'Advanced Learning Management System'),
            'site_logo' => Setting::get('site_logo', 'images/favicon.png'),
            'site_favicon' => Setting::get('site_favicon', 'images/favicon.png'),
            'admin_email' => Setting::get('admin_email', 'admin@mersif.com'),
            'maintenance_mode' => Setting::get('maintenance_mode', '0') === '1',
            'registration_enabled' => Setting::get('registration_enabled', '1') === '1',
        ];

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update the settings.
     */
    public function update(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string|max:255',
            'site_description' => 'nullable|string|max:500',
            'admin_email' => 'required|email|max:255',
        ]);

        // Update settings
        Setting::set('site_name', $request->site_name);
        Setting::set('site_description', $request->site_description ?? '');
        Setting::set('admin_email', $request->admin_email);
        Setting::set('maintenance_mode', $request->input('maintenance_mode', '0'));
        Setting::set('registration_enabled', $request->input('registration_enabled', '0'));

        return redirect()->route('admin.settings.index')->with('success', 'Settings updated successfully');
    }

    /**
     * Upload logo.
     */
    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        try {
            // Delete old logo if exists
            $oldLogo = Setting::get('site_logo');
            if ($oldLogo && $oldLogo !== 'images/favicon.png' && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }

            // Upload new logo
            $logoPath = $request->file('logo')->store('images', 'public');
            
            // Update setting
            Setting::set('site_logo', $logoPath);

        return redirect()->route('admin.settings.index')->with('success', 'Logo uploaded successfully');
        } catch (\Exception $e) {
            return redirect()->route('admin.settings.index')->with('error', 'Failed to upload logo: ' . $e->getMessage());
        }
    }

    /**
     * Upload favicon.
     */
    public function uploadFavicon(Request $request)
    {
        $request->validate([
            'favicon' => 'required|mimes:ico,png,jpg,jpeg|max:512',
        ]);

        try {
            // Delete old favicon if exists
            $oldFavicon = Setting::get('site_favicon');
            if ($oldFavicon && $oldFavicon !== 'images/favicon.png' && Storage::disk('public')->exists($oldFavicon)) {
                Storage::disk('public')->delete($oldFavicon);
            }

            // Upload new favicon to storage/app/public/images
            $faviconPath = $request->file('favicon')->store('images', 'public');
            
            // Update setting with the storage path
            Setting::set('site_favicon', $faviconPath);

            return redirect()->route('admin.settings.index')->with('success', 'Favicon uploaded successfully');
        } catch (\Exception $e) {
            return redirect()->route('admin.settings.index')->with('error', 'Failed to upload favicon: ' . $e->getMessage());
        }
    }
}
