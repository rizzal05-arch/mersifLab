@extends('layouts.admin')

@section('title', 'Settings')

@section('content')
<div class="page-title">
    <h1>System Settings</h1>
</div>

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
                            <input class="form-check-input" type="checkbox" id="maintenance_mode" name="maintenance_mode" {{ $settings['maintenance_mode'] ? 'checked' : '' }} style="width: 40px; height: 20px;">
                            <label class="form-check-label" for="maintenance_mode" style="margin-left: 10px; color: #333333; font-weight: 500;">Maintenance Mode</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="registration_enabled" name="registration_enabled" {{ $settings['registration_enabled'] ? 'checked' : '' }} style="width: 40px; height: 20px;">
                            <label class="form-check-label" for="registration_enabled" style="margin-left: 10px; color: #333333; font-weight: 500;">Enable Registration</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="email_notifications" name="email_notifications" {{ $settings['email_notifications'] ? 'checked' : '' }} style="width: 40px; height: 20px;">
                            <label class="form-check-label" for="email_notifications" style="margin-left: 10px; color: #333333; font-weight: 500;">Email Notifications</label>
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
                <img src="{{ asset($settings['site_logo']) }}" alt="Site Logo" style="max-width: 200px; max-height: 100px; border: 1px solid #e0e0e0; border-radius: 8px; padding: 10px; margin-bottom: 20px;">
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
