/**
 * Admin Pagination Enhancement Script
 * MersifLab - Enhanced pagination with keyboard navigation and accessibility
 */

document.addEventListener('DOMContentLoaded', function() {
    // Enhance pagination with keyboard navigation
    const paginationLinks = document.querySelectorAll('.pagination .page-link');
    
    paginationLinks.forEach(link => {
        // Add keyboard event listeners
        link.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.click();
            }
        });
        
        // Add ARIA labels for better accessibility
        if (!this.getAttribute('aria-label')) {
            const text = this.textContent.trim();
            if (text.includes('Previous') || text.includes('«')) {
                this.setAttribute('aria-label', 'Previous page');
            } else if (text.includes('Next') || text.includes('»')) {
                this.setAttribute('aria-label', 'Next page');
            } else if (!isNaN(text)) {
                this.setAttribute('aria-label', `Go to page ${text}`);
            }
        }
    });
    
    // Add page info announcement for screen readers
    const pageInfo = document.querySelector('.pagination-info');
    if (pageInfo) {
        pageInfo.setAttribute('role', 'status');
        pageInfo.setAttribute('aria-live', 'polite');
    }
    
    // Enhance mobile touch experience
    if ('ontouchstart' in window) {
        paginationLinks.forEach(link => {
            link.addEventListener('touchstart', function() {
                this.style.transform = 'scale(0.95)';
            });
            
            link.addEventListener('touchend', function() {
                this.style.transform = '';
            });
        });
    }
    
    // Auto-scroll to top when page changes
    const currentPage = document.querySelector('.pagination .page-item.active .page-link');
    if (currentPage && window.scrollY > 200) {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }
    
    // Add loading state for pagination clicks
    paginationLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            if (this.href && !this.classList.contains('active') && !this.parentElement.classList.contains('disabled')) {
                // Add loading state
                const originalContent = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                this.style.pointerEvents = 'none';
                
                // Restore if navigation takes too long (fallback)
                setTimeout(() => {
                    this.innerHTML = originalContent;
                    this.style.pointerEvents = '';
                }, 3000);
            }
        });
    });
});

// Utility function to update pagination info
function updatePaginationInfo(currentPage, totalPages, totalItems) {
    const pageInfo = document.querySelector('.pagination-info');
    if (pageInfo) {
        const start = (currentPage - 1) * 20 + 1; // Assuming 20 items per page
        const end = Math.min(currentPage * 20, totalItems);
        pageInfo.textContent = `Showing ${start} to ${end} of ${totalItems} results`;
        pageInfo.setAttribute('aria-live', 'polite');
    }
}
