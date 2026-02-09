@extends('layouts.admin')

@section('title', 'Activity Management - Admin Dashboard')

@section('content')
<div class="page-title" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
    <div>
        <h1>Activity Management</h1>
    </div>
    <div style="max-width: 350px; width: 100%; margin-top: 0;">
        <input type="text" id="activitySearch" placeholder="Search activities..." style="width: 100%; padding: 10px 15px; border: none; background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); border-radius: 20px; font-size: 13px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); transition: all 0.3s ease; outline: none;" onfocus="this.style.background='white'; this.style.boxShadow='0 2px 8px rgba(0, 0, 0, 0.1)';" onblur="this.style.background='rgba(255, 255, 255, 0.8)'; this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.05)';">
    </div>
</div>

<div class="card-content" style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
    <!-- Tab Navigation -->
    <div class="activity-tabs" style="display: flex; gap: 10px; margin-bottom: 24px; border-bottom: 2px solid #f0f0f0; position: relative;">
        <button class="activity-tab active" data-tab="student" style="flex: 1; padding: 12px 20px; background: none; border: none; font-size: 14px; font-weight: 600; color: #828282; cursor: pointer; position: relative; transition: all 0.3s;">
            <i class="fas fa-users me-2"></i>Student User
        </button>
        <button class="activity-tab" data-tab="teacher" style="flex: 1; padding: 12px 20px; background: none; border: none; font-size: 14px; font-weight: 600; color: #828282; cursor: pointer; position: relative; transition: all 0.3s;">
            <i class="fas fa-chalkboard-user me-2"></i>Teacher User
        </button>
        <button class="activity-tab" data-tab="admin" style="flex: 1; padding: 12px 20px; background: none; border: none; font-size: 14px; font-weight: 600; color: #828282; cursor: pointer; position: relative; transition: all 0.3s;">
            <i class="fas fa-user-shield me-2"></i>Admin User
        </button>
        <div class="tab-indicator" style="position: absolute; bottom: -2px; left: 0; height: 2px; background: #2F80ED; transition: all 0.3s ease; width: 33.33%;"></div>
    </div>

    <!-- Tab Content Container -->
    <div class="tab-content-wrapper" style="position: relative; overflow: hidden;">
        <!-- Student Tab Content -->
        <div class="tab-content active" data-content="student" style="display: block;">
            <div class="list-group list-group-flush">
                @forelse($studentActivities ?? [] as $activity)
                    <div class="list-group-item" style="border: none; border-bottom: 1px solid #f0f0f0; padding: 16px 0;">
                        <div class="d-flex align-items-start gap-3">
                            <div style="width: 40px; height: 40px; background: #e8f5e9; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fas fa-circle" style="font-size: 8px; color: #27AE60;"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div style="font-size: 13px; color: #333333; margin-bottom: 6px; line-height: 1.5;">
                                    <strong style="color: #27AE60; font-weight: 600;">{{ $activity->user->name ?? 'System' }}</strong> 
                                    <span>{{ $activity->description }}</span>
                                </div>
                                <div style="font-size: 12px; color: #828282; display: flex; align-items: center; gap: 8px;">
                                    <i class="far fa-clock"></i>
                                    <span>{{ $activity->created_at->format('d M Y, H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="list-group-item text-center" style="border: none; padding: 60px; color: #828282;">
                        <i class="fas fa-inbox" style="font-size: 48px; color: #e0e0e0; margin-bottom: 15px;"></i>
                        <p style="font-size: 14px; margin: 0;">No student activity</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Teacher Tab Content -->
        <div class="tab-content" data-content="teacher" style="display: none;">
            <div class="list-group list-group-flush">
                @forelse($teacherActivities ?? [] as $activity)
                    <div class="list-group-item" style="border: none; border-bottom: 1px solid #f0f0f0; padding: 16px 0;">
                        <div class="d-flex align-items-start gap-3">
                            <div style="width: 40px; height: 40px; background: #e3f2fd; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fas fa-circle" style="font-size: 8px; color: #1976d2;"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div style="font-size: 14px; color: #333333; margin-bottom: 6px; line-height: 1.5;">
                                    <strong style="color: #1976d2;">{{ $activity->user->name ?? 'System' }}</strong> 
                                    <span>{{ $activity->description }}</span>
                                </div>
                                <div style="font-size: 12px; color: #828282; display: flex; align-items: center; gap: 8px;">
                                    <i class="far fa-clock"></i>
                                    <span>{{ $activity->created_at->format('d M Y, H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="list-group-item text-center" style="border: none; padding: 60px; color: #828282;">
                        <i class="fas fa-inbox" style="font-size: 48px; color: #e0e0e0; margin-bottom: 15px;"></i>
                        <p style="font-size: 14px; margin: 0;">Tidak ada aktivitas teacher</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Admin Student Tab Content -->
        <div class="tab-content" data-content="admin" style="display: none;">
            <div class="list-group list-group-flush">
                @forelse($adminStudentActivities ?? [] as $activity)
                    <div class="list-group-item" style="border: none; border-bottom: 1px solid #f0f0f0; padding: 16px 0;">
                        <div class="d-flex align-items-start gap-3">
                            <div style="width: 40px; height: 40px; background: #fff3e0; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fas fa-circle" style="font-size: 8px; color: #f57c00;"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div style="font-size: 14px; color: #333333; margin-bottom: 6px; line-height: 1.5;">
                                    <strong style="color: #f57c00;">{{ $activity->user->name ?? 'System' }}</strong> 
                                    <span>{{ $activity->description }}</span>
                                </div>
                                <div style="font-size: 12px; color: #828282; display: flex; align-items: center; gap: 8px;">
                                    <i class="far fa-clock"></i>
                                    <span>{{ $activity->created_at->format('d M Y, H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="list-group-item text-center" style="border: none; padding: 60px; color: #828282;">
                        <i class="fas fa-inbox" style="font-size: 48px; color: #e0e0e0; margin-bottom: 15px;"></i>
                        <p style="font-size: 14px; margin: 0;">No admin activity</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<style>
    .activity-tab.active {
        color: #2F80ED !important;
    }

    .activity-tab:hover {
        color: #2F80ED !important;
        background: #f8f9fa !important;
        border-radius: 8px 8px 0 0;
    }

    .tab-content {
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateX(10px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    /* Swipe support for mobile */
    .tab-content-wrapper {
        touch-action: pan-y;
    }

    /* Responsive search bar */
    @media (max-width: 768px) {
        .page-title {
            flex-direction: column !important;
            gap: 15px;
        }
        
        .page-title > div:last-child {
            max-width: 100% !important;
            width: 100% !important;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('.activity-tab');
        const contents = document.querySelectorAll('.tab-content');
        const indicator = document.querySelector('.tab-indicator');
        const searchInput = document.getElementById('activitySearch');
        let currentTab = 0;
        let startX = 0;
        let currentX = 0;
        let isDragging = false;

        // Tab click handler
        tabs.forEach((tab, index) => {
            tab.addEventListener('click', function() {
                const tabName = this.getAttribute('data-tab');
                switchTab(index, tabName);
            });
        });

        function switchTab(index, tabName) {
            // Update tabs
            tabs.forEach((t, i) => {
                if (i === index) {
                    t.classList.add('active');
                } else {
                    t.classList.remove('active');
                }
            });

            // Update contents
            contents.forEach(content => {
                if (content.getAttribute('data-content') === tabName) {
                    content.style.display = 'block';
                    content.classList.add('active');
                } else {
                    content.style.display = 'none';
                    content.classList.remove('active');
                }
            });

            // Update indicator position
            const tabWidth = 100 / tabs.length;
            indicator.style.left = `${index * tabWidth}%`;
            indicator.style.width = `${tabWidth}%`;

            currentTab = index;
        }

        // Swipe functionality for mobile
        const wrapper = document.querySelector('.tab-content-wrapper');
        
        wrapper.addEventListener('touchstart', function(e) {
            startX = e.touches[0].clientX;
            isDragging = true;
        });

        wrapper.addEventListener('touchmove', function(e) {
            if (!isDragging) return;
            currentX = e.touches[0].clientX;
        });

        wrapper.addEventListener('touchend', function(e) {
            if (!isDragging) return;
            isDragging = false;

            const diffX = startX - currentX;
            const threshold = 50; // Minimum swipe distance

            if (Math.abs(diffX) > threshold) {
                if (diffX > 0 && currentTab < tabs.length - 1) {
                    // Swipe left - next tab
                    const nextTab = tabs[currentTab + 1];
                    const tabName = nextTab.getAttribute('data-tab');
                    switchTab(currentTab + 1, tabName);
                } else if (diffX < 0 && currentTab > 0) {
                    // Swipe right - previous tab
                    const prevTab = tabs[currentTab - 1];
                    const tabName = prevTab.getAttribute('data-tab');
                    switchTab(currentTab - 1, tabName);
                }
            }
        });

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft' && currentTab > 0) {
                const prevTab = tabs[currentTab - 1];
                const tabName = prevTab.getAttribute('data-tab');
                switchTab(currentTab - 1, tabName);
            } else if (e.key === 'ArrowRight' && currentTab < tabs.length - 1) {
                const nextTab = tabs[currentTab + 1];
                const tabName = nextTab.getAttribute('data-tab');
                switchTab(currentTab + 1, tabName);
            }
        });

        // Search functionality
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase().trim();
                const activeContent = document.querySelector('.tab-content.active');
                
                if (!activeContent) return;
                
                const activityItems = activeContent.querySelectorAll('.list-group-item');
                
                activityItems.forEach(item => {
                    const text = item.textContent.toLowerCase();
                    const activityText = item.querySelector('strong')?.textContent || '';
                    const descriptionText = item.textContent || '';
                    
                    if (searchTerm === '' || text.includes(searchTerm)) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                });
                
                // Check if all items are hidden, show empty state if needed
                const visibleItems = Array.from(activityItems).filter(item => item.style.display !== 'none');
                const emptyState = activeContent.querySelector('.list-group-item.text-center');
                
                if (visibleItems.length === 0 && searchTerm !== '') {
                    // Show no results message
                    if (!emptyState || !emptyState.classList.contains('search-no-results')) {
                        const noResults = document.createElement('div');
                        noResults.className = 'list-group-item text-center search-no-results';
                        noResults.style.cssText = 'border: none; padding: 60px; color: #828282;';
                        noResults.innerHTML = `
                            <i class="fas fa-search" style="font-size: 48px; color: #e0e0e0; margin-bottom: 15px;"></i>
                            <p style="font-size: 14px; margin: 0;">No results for "${searchTerm}"</p>
                        `;
                        activeContent.querySelector('.list-group').appendChild(noResults);
                    }
                } else {
                    // Remove no results message if exists
                    const noResults = activeContent.querySelector('.search-no-results');
                    if (noResults) {
                        noResults.remove();
                    }
                }
            });
            
            // Clear search when switching tabs
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    searchInput.value = '';
                    const allItems = document.querySelectorAll('.list-group-item');
                    allItems.forEach(item => {
                        item.style.display = '';
                    });
                    const noResults = document.querySelectorAll('.search-no-results');
                    noResults.forEach(item => item.remove());
                });
            });
        }
    });
</script>
@endsection
