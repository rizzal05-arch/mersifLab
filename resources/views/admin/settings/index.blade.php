@extends('layouts.admin')

@section('title', 'Settings')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="page-title">
    <h1>System Settings</h1>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="margin-bottom: 20px; border-radius: 8px;">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin-bottom: 20px; border-radius: 8px;">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin-bottom: 20px; border-radius: 8px;">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
    <div class="col-lg-8">
        <div class="card-content">
            <div class="card-content-title">
                General Settings
                <div>
                    <button type="submit" form="settingsForm" class="btn btn-primary" style="background: #2F80ED; border: none; padding: 8px 16px; font-size: 13px; border-radius: 6px; color: white;">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </div>

            <form id="settingsForm" action="{{ route('admin.settings.update') }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="site_name" class="form-label" style="font-weight: 600; color: #333333; margin-bottom: 8px; display: block;">Site Name</label>
                            <input type="text" class="form-control" id="site_name" name="site_name" value="{{ $settings['site_name'] }}" style="border: 1px solid #e0e0e0; border-radius: 6px; padding: 10px 12px; font-size: 14px; width: 100%;">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="admin_email" class="form-label" style="font-weight: 600; color: #333333; margin-bottom: 8px; display: block;">Admin Email</label>
                            <input type="email" class="form-control" id="admin_email" name="admin_email" value="{{ $settings['admin_email'] }}" style="border: 1px solid #e0e0e0; border-radius: 6px; padding: 10px 12px; font-size: 14px; width: 100%;">
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="site_description" class="form-label" style="font-weight: 600; color: #333333; margin-bottom: 8px; display: block;">Site Description</label>
                    <textarea class="form-control" id="site_description" name="site_description" rows="3" style="border: 1px solid #e0e0e0; border-radius: 6px; padding: 10px 12px; font-size: 14px; width: 100%; resize: vertical;">{{ $settings['site_description'] }}</textarea>
                </div>

                <div class="row mb-4">
                    <div class="col-md-12">
                        <h5 style="font-weight: 600; color: #333333; margin-bottom: 20px; font-size: 16px;">System Configuration</h5>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="form-check form-switch mb-3">
                            <input type="hidden" name="maintenance_mode" value="0">
                            <input class="form-check-input" type="checkbox" id="maintenance_mode" name="maintenance_mode" value="1" {{ $settings['maintenance_mode'] ? 'checked' : '' }} style="width: 40px; height: 20px;">
                            <label class="form-check-label" for="maintenance_mode" style="margin-left: 10px; color: #333333; font-weight: 500;">Maintenance Mode</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check form-switch mb-3">
                            <input type="hidden" name="registration_enabled" value="0">
                            <input class="form-check-input" type="checkbox" id="registration_enabled" name="registration_enabled" value="1" {{ $settings['registration_enabled'] ? 'checked' : '' }} style="width: 40px; height: 20px;">
                            <label class="form-check-label" for="registration_enabled" style="margin-left: 10px; color: #333333; font-weight: 500;">Enable Registration</label>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card-content">
            <div class="card-content-title">
                Site Logo
            </div>

            <div class="text-center mb-4">
                @php
                    $logoPath = $settings['site_logo'];
                    // Check if logo is in storage
                    if (Storage::disk('public')->exists($logoPath)) {
                        $logoUrl = Storage::url($logoPath);
                    } elseif (file_exists(public_path($logoPath))) {
                        // Fallback to public path
                        $logoUrl = asset($logoPath);
                    } else {
                        // Default placeholder
                        $logoUrl = asset('images/logo.png');
                    }
                @endphp
                <img src="{{ $logoUrl }}" alt="Site Logo" style="max-width: 200px; max-height: 100px; border: 1px solid #e0e0e0; border-radius: 8px; padding: 10px; margin-bottom: 20px;" onerror="this.src='{{ asset('images/logo.png') }}'">
            </div>

            <form action="{{ route('admin.settings.uploadLogo') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="logo" class="form-label" style="font-weight: 600; color: #333333; margin-bottom: 8px; display: block;">Upload New Logo</label>
                    <input type="file" class="form-control" id="logo" name="logo" accept="image/*" style="border: 1px solid #e0e0e0; border-radius: 6px; padding: 10px 12px; font-size: 14px; width: 100%;">
                    <small class="form-text text-muted" style="color: #828282; font-size: 12px; margin-top: 5px; display: block;">Recommended size: 200x100px. Max file size: 2MB.</small>
                </div>
                <button type="submit" class="btn btn-primary w-100" style="background: #2F80ED; border: none; padding: 10px 16px; font-size: 14px; border-radius: 6px; color: white;">
                    <i class="fas fa-upload"></i> Upload Logo
                </button>
            </form>
        </div>

        <div class="card-content mt-4">
            <div class="card-content-title">
                Site Favicon
            </div>

            <div class="text-center mb-4">
                @php
                    $faviconPath = $settings['site_favicon'];
                    // Check if favicon is in storage
                    if (Storage::disk('public')->exists($faviconPath)) {
                        $faviconUrl = Storage::url($faviconPath);
                    } elseif (file_exists(public_path($faviconPath))) {
                        // Fallback to public path
                        $faviconUrl = asset($faviconPath);
                    } else {
                        // Default placeholder
                        $faviconUrl = asset('images/favicon.png');
                    }
                @endphp
                <img src="{{ $faviconUrl }}" alt="Site Favicon" style="max-width: 32px; max-height: 32px; border: 1px solid #e0e0e0; border-radius: 4px; padding: 5px; margin-bottom: 20px;" onerror="this.src='{{ asset('images/favicon.png') }}'">
            </div>

            <form action="{{ route('admin.settings.uploadFavicon') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="favicon" class="form-label" style="font-weight: 600; color: #333333; margin-bottom: 8px; display: block;">Upload New Favicon</label>
                    <input type="file" class="form-control" id="favicon" name="favicon" accept="image/x-icon,image/png,.ico" style="border: 1px solid #e0e0e0; border-radius: 6px; padding: 10px 12px; font-size: 14px; width: 100%;">
                    <small class="form-text text-muted" style="color: #828282; font-size: 12px; margin-top: 5px; display: block;">Recommended size: 32x32px. Max file size: 512KB. Format: ICO or PNG.</small>
                </div>
                <button type="submit" class="btn btn-primary w-100" style="background: #2F80ED; border: none; padding: 10px 16px; font-size: 14px; border-radius: 6px; color: white;">
                    <i class="fas fa-upload"></i> Upload Favicon
                </button>
            </form>
        </div>

        <div class="card-content mt-4">
            <div class="card-content-title">
                System Information
            </div>

            <div style="padding: 20px 0;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #f8f9fa;">
                    <span style="color: #828282; font-size: 13px;">Laravel Version</span>
                    <span style="color: #333333; font-weight: 500; font-size: 13px;">{{ app()->version() }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #f8f9fa;">
                    <span style="color: #828282; font-size: 13px;">PHP Version</span>
                    <span style="color: #333333; font-weight: 500; font-size: 13px;">{{ PHP_VERSION }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #f8f9fa;">
                    <span style="color: #828282; font-size: 13px;">Environment</span>
                    <span style="color: #333333; font-weight: 500; font-size: 13px;">{{ config('app.env') }}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #828282; font-size: 13px;">Database</span>
                    <span style="color: #333333; font-weight: 500; font-size: 13px;">{{ config('database.default') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
