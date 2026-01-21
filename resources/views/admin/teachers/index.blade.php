@extends('layouts.admin')

@section('title', 'Teachers Management')

@section('content')
<div class="page-title">
    <h1>Teachers Management</h1>
</div>

<div class="card-content">
    <div class="card-content-title">
        All Teachers
        <div>
            <a href="{{ route('admin.teachers.create') }}" class="btn btn-primary" style="background: #2F80ED; border: none; padding: 8px 16px; font-size: 13px; border-radius: 6px; color: white; text-decoration: none;">
                <i class="fas fa-plus"></i> Add New Teacher
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
                    <th style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Joined Date</th>
                    <th style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Total Courses</th>
                    <th style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Status</th>
                    <th style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($teachers as $teacher)
                    <tr style="border-bottom: 1px solid #f8f9fa;">
                        <td style="padding: 16px 8px; vertical-align: middle; color: #333333; font-weight: 500;">{{ $loop->iteration }}</td>
                        <td style="padding: 16px 8px; vertical-align: middle;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="width: 40px; height: 40px; background: #e3f2fd; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-chalkboard-user" style="color: #1976d2; font-size: 16px;"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 600; color: #333333; margin-bottom: 2px;">{{ $teacher['name'] }}</div>
                                    <small style="color: #828282; font-size: 11px;">ID: {{ $teacher['id'] }}</small>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 16px 8px; vertical-align: middle; color: #828282;">{{ $teacher['email'] }}</td>
                        <td style="padding: 16px 8px; vertical-align: middle; color: #828282;">{{ $teacher['joined_date'] }}</td>
                        <td style="padding: 16px 8px; vertical-align: middle; color: #333333; font-weight: 500;">{{ $teacher['total_courses'] }}</td>
                        <td style="padding: 16px 8px; vertical-align: middle;">
                            <span class="badge" style="padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 500; @if($teacher['status'] == 'Active') background: #d4edda; color: #155724; @else background: #f8d7da; color: #721c24; @endif">
                                {{ $teacher['status'] }}
                            </span>
                        </td>
                        <td style="padding: 16px 8px; vertical-align: middle;">
                            <div style="display: flex; gap: 8px;">
                                <a href="{{ route('admin.teachers.show', $teacher['id']) }}" class="btn btn-sm" style="background: #e3f2fd; color: #1976d2; border: none; padding: 4px 8px; font-size: 11px; border-radius: 4px; text-decoration: none;">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                @if($teacher['status'] == 'Active')
                                    <form action="{{ route('admin.teachers.toggleBan', $teacher['id']) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm" style="background: #fff3e0; color: #f57c00; border: none; padding: 4px 8px; font-size: 11px; border-radius: 4px;" onclick="return confirm('Are you sure you want to ban this teacher?')">
                                            <i class="fas fa-ban"></i> Ban
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.teachers.toggleBan', $teacher['id']) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm" style="background: #e8f5e8; color: #2e7d32; border: none; padding: 4px 8px; font-size: 11px; border-radius: 4px;" onclick="return confirm('Are you sure you want to unban this teacher?')">
                                            <i class="fas fa-check"></i> Unban
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
                                <i class="fas fa-chalkboard-user" style="font-size: 48px; color: #e0e0e0;"></i>
                                <span style="font-size: 14px;">No teachers found</span>
                                <a href="{{ route('admin.teachers.create') }}" class="btn btn-primary" style="background: #2F80ED; border: none; padding: 8px 16px; font-size: 13px; border-radius: 6px; color: white; text-decoration: none;">
                                    <i class="fas fa-plus"></i> Add First Teacher
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
