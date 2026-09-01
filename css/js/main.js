/**
 * Scent - Fully Functional Navigation Fix
 * Fixes all dropdown and navigation issues
 */

document.addEventListener('DOMContentLoaded', function() {
    // 1. Initialize all dropdown menus
    const initDropdowns = () => {
        const dropdowns = document.querySelectorAll('.dropdown-toggle');
        
        dropdowns.forEach(toggle => {
            // Initialize Bootstrap dropdown
            const dropdown = new bootstrap.Dropdown(toggle);
            
            // Fix click handling for mobile
            toggle.addEventListener('click', function(e) {
                if (window.innerWidth <= 992) { // Mobile breakpoint
                    e.preventDefault();
                    dropdown.toggle();
                }
            });
        });
    };

    // 2. Fix mobile navbar toggle
    const initMobileMenu = () => {
        const navbarToggler = document.querySelector('.navbar-toggler');
        if (navbarToggler) {
            navbarToggler.addEventListener('click', function() {
                const target = document.querySelector(this.dataset.bsTarget);
                target.classList.toggle('show');
            });
        }
    };

    // 3. Initialize all components
    initDropdowns();
    initMobileMenu();
    
    // Debug check
    console.log('Navigation initialized - Bootstrap:', typeof bootstrap !== 'undefined');
});
// In your product page JavaScript for the "Add to Cart" button
fetch('<?= BASE_URL ?>/cart/add/' + productId, {
    method: 'POST',
    headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
        'X-Requested-With': 'XMLHttpRequest'
    },
    body: 'quantity=' + quantity
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        // Show success message
        console.log("Cart response:", data); // Add this for debugging
        // Update cart count in the header
    }
})
.catch(error => {
    console.error('Error:', error);
});