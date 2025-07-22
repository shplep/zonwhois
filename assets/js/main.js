document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const navList = document.querySelector('.nav-list');
    
    if (mobileMenuToggle && navList) {
        mobileMenuToggle.addEventListener('click', function() {
            navList.classList.toggle('active');
            mobileMenuToggle.classList.toggle('active');
        });
    }
    
    // Search form handling
    const searchForm = document.querySelector('.search-form');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const searchInput = document.querySelector('.search-input');
            const domain = searchInput.value.trim();
            
            if (domain) {
                // Validate domain format
                if (isValidDomain(domain)) {
                    window.location.href = '/domain/' + encodeURIComponent(domain);
                } else {
                    showAlert('Please enter a valid domain name (e.g., example.com)', 'error');
                }
            } else {
                showAlert('Please enter a domain name', 'error');
            }
        });
    }
    
    // Domain validation
    function isValidDomain(domain) {
        // Remove protocol if present
        domain = domain.replace(/^https?:\/\//, '');
        
        // Basic domain validation
        const domainRegex = /^[a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?(\.[a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?)*$/;
        return domainRegex.test(domain);
    }
    
    // Alert system
    function showAlert(message, type = 'info') {
        // Remove existing alerts
        const existingAlert = document.querySelector('.alert');
        if (existingAlert) {
            existingAlert.remove();
        }
        
        // Create new alert
        const alert = document.createElement('div');
        alert.className = `alert alert-${type}`;
        alert.textContent = message;
        
        // Insert alert
        const searchContainer = document.querySelector('.search-container');
        if (searchContainer) {
            searchContainer.appendChild(alert);
            
            // Auto-remove after 5 seconds
            setTimeout(() => {
                alert.remove();
            }, 5000);
        }
    }
    
    // Lazy loading for domain lists
    const domainLinks = document.querySelectorAll('.domain-link');
    domainLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Add loading state
            this.style.opacity = '0.6';
            this.style.pointerEvents = 'none';
        });
    });
    
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Auto-focus search input on homepage
    const searchInput = document.querySelector('.search-input');
    if (searchInput && window.location.pathname === '/') {
        searchInput.focus();
    }
    
    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + K to focus search
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            const searchInput = document.querySelector('.search-input');
            if (searchInput) {
                searchInput.focus();
            }
        }
        
        // Escape to close mobile menu
        if (e.key === 'Escape') {
            const navList = document.querySelector('.nav-list');
            if (navList && navList.classList.contains('active')) {
                navList.classList.remove('active');
                const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
                if (mobileMenuToggle) {
                    mobileMenuToggle.classList.remove('active');
                }
            }
        }
    });
    
    // Add CSS for mobile menu
    const style = document.createElement('style');
    style.textContent = `
        @media (max-width: 768px) {
            .nav-list {
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: white;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                flex-direction: column;
                padding: 1rem;
                display: none;
            }
            
            .nav-list.active {
                display: flex;
            }
            
            .mobile-menu-toggle.active span:nth-child(1) {
                transform: rotate(-45deg) translate(-5px, 6px);
            }
            
            .mobile-menu-toggle.active span:nth-child(2) {
                opacity: 0;
            }
            
            .mobile-menu-toggle.active span:nth-child(3) {
                transform: rotate(45deg) translate(-5px, -6px);
            }
        }
    `;
    document.head.appendChild(style);
}); 