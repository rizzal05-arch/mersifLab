@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<section class="profile-section py-5">
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3">
                <div class="profile-sidebar">
                    <!-- Profile Avatar -->
                    <div class="profile-avatar-section text-center">
                        <div class="profile-avatar mx-auto">
                            <span class="avatar-letter">{{ strtoupper(substr(Auth::user()->name ?? Auth::user()->email ?? 'S', 0, 1)) }}</span>
                        </div>
                        <h5 class="profile-name mt-3">
                            {{ Auth::user()->name ?? 'Student' }}
                            @php
                                $user = Auth::user();
                                $isSubscriber = $user->is_subscriber;
                                $subscriptionPlan = strtolower($user->subscription_plan ?? 'none');
                            @endphp
                            @if($isSubscriber)
                                @if($subscriptionPlan === 'premium')
                                    <span style="background: linear-gradient(135deg, #FFD700, #FFA500); color: white; margin-left: 8px; padding: 2px 8px; border-radius: 12px; font-size: 0.7em; font-weight: bold; display: inline-block; text-shadow: 0 1px 2px rgba(0,0,0,0.3);" title="Premium Subscriber - Gold Crown">👑</span>
                                @else
                                    <span style="background: linear-gradient(135deg, #C0C0C0, #808080); color: white; margin-left: 8px; padding: 2px 8px; border-radius: 12px; font-size: 0.7em; font-weight: bold; display: inline-block; text-shadow: 0 1px 2px rgba(0,0,0,0.3);" title="Standard Subscriber - Silver Crown">👑</span>
                                @endif
                            @endif
                        </h5>
                        <p class="profile-role text-muted mb-1">{{ Auth::user()->isTeacher() ? 'teacher' : 'student' }}</p>
                        <p class="profile-email">{{ Auth::user()->email ?? 'student@gmail.com' }}</p>
                    </div>
                    
                    <!-- Navigation Menu -->
                    <nav class="profile-nav mt-4">
                        <a href="{{ route('profile') }}" class="profile-nav-item active">
                            <i class="fas fa-user me-2"></i> My Profile
                        </a>
                        <a href="{{ route('my-courses') }}" class="profile-nav-item">
                            <i class="fas fa-book me-2"></i> My Courses
                        </a>
                        <a href="{{ route('purchase-history') }}" class="profile-nav-item">
                            <i class="fas fa-history me-2"></i> Purchase History
                        </a>
                        <a href="{{ route('notification-preferences') }}" class="profile-nav-item">
                            <i class="fas fa-bell me-2"></i> Notification Preferences
                        </a>
                    </nav>
                    
                    <!-- Logout Button -->
                    <form action="{{ route('logout') }}" method="POST" class="mt-4">
                        @csrf
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-sign-out-alt me-2"></i> Logout Account
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-lg-9">
                <div class="profile-content">
                    <div class="profile-header">
                        <h2 class="profile-title">Profile</h2>
                        <p class="profile-subtitle">Add information about yourself</p>
                    </div>
                    
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Full Name<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="full_name" name="full_name" 
                                   placeholder="Your Full Name" value="{{ old('full_name', Auth::user()->full_name ?? '') }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email<span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   placeholder="student@gmail.com" value="{{ old('email', Auth::user()->email ?? '') }}" required readonly>
                            <small class="text-muted">Email cannot be changed</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="telephone" class="form-label">Telephone Number</label>
                            <input type="tel" class="form-control" id="telephone" name="telephone" 
                                   placeholder="08xx-xxxx-xxxx" value="{{ old('telephone', Auth::user()->telephone ?? '') }}">
                        </div>
                        
                        <div class="mb-4">
                            <label for="biography" class="form-label">Biography</label>
                            <textarea class="form-control" id="biography" name="biography" rows="5" 
                                      placeholder="Describe yourself">{{ old('biography', Auth::user()->biography ?? '') }}</textarea>
                            <small class="text-muted">Tell us about yourself</small>
                        </div>
                        
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>Save Changes
                            </button>
                        </div>
                    </form>

                    <div class="mt-4">
                        <hr>
                        <h5 class="mb-3">Subscription Status</h5>
                        @php
                            $user = Auth::user();
                            $isSubscriber = $user->is_subscriber;
                            $subscriptionPlan = strtolower($user->subscription_plan ?? 'none');
                            $isExpired = $user->subscription_expires_at && $user->subscription_expires_at->isPast();
                        @endphp
                        
                        @if($isSubscriber && $subscriptionPlan !== 'none')
                            @if($subscriptionPlan === 'premium')
                                <div class="alert alert-success" style="background: linear-gradient(135deg, #6a1b9a, #8e24aa); color: white; border: none; padding: 16px;">
                                    <div style="display: flex; align-items: center; gap: 16px;">
                                        <span style="font-size: 2.5rem; color: #FFD700; text-shadow: 0 0 10px rgba(255, 215, 0, 0.5);">♛</span>
                                        <div>
                                            <strong>Premium Subscriber</strong>
                                            <div style="font-size: 0.9rem; margin-top: 4px;">
                                                <span>🌟 Access to ALL courses</span>
                                            </div>
                                            <div style="font-size: 0.85rem; margin-top: 2px; opacity: 0.9;">
                                                <span>📅 Expires: {{ $user->subscription_expires_at ? $user->subscription_expires_at->format('F j, Y') : 'Unlimited' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-success" style="background: linear-gradient(135deg, #2e7d32, #43a047); color: white; border: none; padding: 16px;">
                                    <div style="display: flex; align-items: center; gap: 16px;">
                                        <span style="font-size: 2.5rem; color: #C0C0C0; text-shadow: 0 0 10px rgba(192, 192, 192, 0.5);">♛</span>
                                        <div>
                                            <strong>Standard Subscriber</strong>
                                            <div style="font-size: 0.9rem; margin-top: 4px;">
                                                <span>🎓 Access to Standard courses</span>
                                            </div>
                                            <div style="font-size: 0.85rem; margin-top: 2px; opacity: 0.9;">
                                                <span>📅 Expires: {{ $user->subscription_expires_at ? $user->subscription_expires_at->format('F j, Y') : 'Unlimited' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if($isExpired)
                                <div class="alert alert-warning mt-2">
                                    <i class="fas fa-exclamation-triangle"></i> Your subscription has expired. Please renew to continue accessing premium content.
                                </div>
                            @endif
                        @else
                            <div class="alert alert-info">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <i class="fas fa-user" style="font-size: 1.5rem; color: #64748b;"></i>
                                    <div>
                                        <strong>Free User</strong>
                                        <div style="font-size: 0.9rem;">
                                            <i class="fas fa-lock"></i> No subscription access
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p class="text-muted mt-3">Upgrade your subscription to get access to premium courses and features.</p>
                            <form action="{{ url('/subscribe') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-crown me-2"></i>Upgrade Subscription
                                </button>
                            </form>
                        @endif
                    </div>
                    
                    @if(Auth::user()->isStudent() && !Auth::user()->hasPendingTeacherApplication())
                    <div class="mt-4 text-center">
                        <hr>
                        <p class="text-muted mb-3">Want to share your knowledge and help others learn?</p>
                        <a href="{{ route('teacher.application.create') }}" class="btn btn-outline-primary" target="_blank" rel="noopener noreferrer">
                            <i class="fas fa-chalkboard-teacher me-2"></i>Want to become a teacher?
                        </a>
                    </div>
                    @elseif(Auth::user()->hasPendingTeacherApplication())
                    <div class="mt-4 text-center">
                        <hr>
                        <div class="alert alert-info">
                            <i class="fas fa-clock me-2"></i>
                            Your teacher application is under review. We'll notify you once there's an update.
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('teacher.application.preview') }}" class="btn btn-primary me-2">
                                <i class="fas fa-eye me-2"></i>View Application
                            </a>
                            <a href="{{ route('teacher.application.edit') }}" class="btn btn-outline-primary">
                                <i class="fas fa-edit me-2"></i>Edit Application
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
.crown-icon {
    margin-left: 8px;
    font-size: 1.2em;
    display: inline-block;
    vertical-align: middle;
}

.premium-crown {
    color: #FFD700;
    text-shadow: 0 0 8px rgba(255, 215, 0, 0.5);
    animation: shine 2s ease-in-out infinite;
}

.standard-crown {
    color: #C0C0C0;
    text-shadow: 0 0 8px rgba(192, 192, 192, 0.5);
}

@keyframes shine {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.8; }
}

.premium-subscription {
    background: linear-gradient(135deg, #6a1b9a, #8e24aa) !important;
    color: white !important;
    border: none !important;
}

.standard-subscription {
    background: linear-gradient(135deg, #2e7d32, #43a047) !important;
    color: white !important;
    border: none !important;
}

.subscription-display {
    display: flex;
    align-items: center;
    gap: 16px;
}

.crown-emoji {
    font-size: 2.5rem;
    display: block;
    text-align: center;
    min-width: 60px;
}

.premium-emoji {
    filter: drop-shadow(0 0 10px rgba(255, 215, 0, 0.6));
    animation: pulse 2s ease-in-out infinite;
}

.standard-emoji {
    filter: drop-shadow(0 0 10px rgba(192, 192, 192, 0.6));
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.subscription-info {
    flex: 1;
}

.subscription-details {
    display: flex;
    flex-direction: column;
    gap: 4px;
    margin-top: 8px;
    font-size: 0.9rem;
    opacity: 0.9;
}

.subscription-details span {
    display: flex;
    align-items: center;
    gap: 6px;
}
</style>
@endpush