@extends('layouts.app')

@section('title', 'Learning Progress')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="mb-4">Learning Progress</h1>
            
            @if($viewedModules->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Module Title</th>
                                <th>Type</th>
                                <th>Views</th>
                                <th>Last Viewed</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($viewedModules as $module)
                                <tr>
                                    <td>{{ $module->title }}</td>
                                    <td><span class="badge bg-info">{{ $module->type }}</span></td>
                                    <td>{{ $module->view_count }}</td>
                                    <td>{{ $module->updated_at->diffForHumans() }}</td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> You haven't viewed any modules yet. Start learning to track your progress!
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
