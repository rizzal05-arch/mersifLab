@extends('layouts.admin')

@section('title', 'Admin Management')

@section('content')
<div class="page-title">
    <h1>Admin Management</h1>
</div>

<div class="card-content">
    <div class="card-content-title">
        All Administrators
        <div>
            <a href="{{ route('admin.admins.create') }}" class="btn btn-primary" style="background: #2F80ED; border: none; padding: 8px 16px; font-size: 13px; border-radius: 6px; color: white; text-decoration: none;">
                <i class="fas fa-plus"></i> Create New Admin
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-sm" style="font-size: 13px; border-collapse: separate; border-spacing: 0;">
            <thead>
                <tr>
                    <th style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">#</th>
                    <th style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Name</th>
                    <th style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Email</th>
                    <th style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Role</th>
                    <th style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Created At</th>
                    <th style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Last Login</th>
                    <th style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($admins as $admin)
                    <tr style="border-bottom: 1px solid #f8f9fa;">
                        <td style="padding: 16px 8px; vertical-align: middle; color: #333333; font-weight: 500;">{{ $loop->iteration }}</td>
                        <td style="padding: 16px 8px; vertical-align: middle;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="width: 40px; height: 40px; background: #fce4ec; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-user-shield" style="color: #c2185b; font-size: 16px;"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 600; color: #333333; margin-bottom: 2px;">{{ $admin['name'] }}</div>
                                    <small style="color: #828282; font-size: 11px;">ID: {{ $admin['id'] }}</small>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 16px 8px; vertical-align: middle; color: #828282;">{{ $admin['email'] }}</td>
                        <td style="padding: 16px 8px; vertical-align: middle;">
                            <span class="badge" style="padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 500; @if($admin['role'] == 'Super Admin') background: #f3e5f5; color: #4a148c; @else background: #e3f2fd; color: #1565c0; @endif">
                                {{ $admin['role'] }}
                            </span>
                        </td>
                        <td style="padding: 16px 8px; vertical-align: middle; color: #828282;">{{ $admin['created_at'] }}</td>
                        <td style="padding: 16px 8px; vertical-align: middle; color: #828282;">{{ $admin['last_login'] }}</td>
                        <td style="padding: 16px 8px; vertical-align: middle;">
                            <div style="display: flex; gap: 8px;">
                                <a href="{{ route('admin.admins.show', $admin['id']) }}" class="btn btn-sm" style="background: #e3f2fd; color: #1976d2; border: none; padding: 4px 8px; font-size: 11px; border-radius: 4px; text-decoration: none;">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="{{ route('admin.admins.edit', $admin['id']) }}" class="btn btn-sm" style="background: #fff3e0; color: #f57c00; border: none; padding: 4px 8px; font-size: 11px; border-radius: 4px; text-decoration: none;">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                @if(auth()->user()->email !== $admin['email'])
                                    <form action="{{ route('admin.admins.destroy', $admin['id']) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm" style="background: #ffebee; color: #d32f2f; border: none; padding: 4px 8px; font-size: 11px; border-radius: 4px;" onclick="return confirm('Are you sure you want to delete this admin?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center" style="padding: 40px; color: #828282;">
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 10px;">
                                <i class="fas fa-user-shield" style="font-size: 48px; color: #e0e0e0;"></i>
                                <span style="font-size: 14px;">No admins found</span>
                                <a href="{{ route('admin.admins.create') }}" class="btn btn-primary" style="background: #2F80ED; border: none; padding: 8px 16px; font-size: 13px; border-radius: 6px; color: white; text-decoration: none;">
                                    <i class="fas fa-plus"></i> Create First Admin
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
