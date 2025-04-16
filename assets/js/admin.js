document.addEventListener('DOMContentLoaded', function() {
    console.log('admin.js loaded');

    const menuToggle = document.querySelector('.menu-toggle');
    const sidebar = document.querySelector('.sidebar');
    const navLinks = document.querySelectorAll('.sidebar-nav a');

    // Debug: Check if elements exist
    if (!menuToggle) {
        console.error('Menu toggle button not found');
        return;
    }
    if (!sidebar) {
        console.error('Sidebar not found');
        return;
    }
    console.log('Elements found:', menuToggle, sidebar);

    // Click to toggle sidebar
    menuToggle.addEventListener('click', function(e) {
        e.preventDefault();
        sidebar.classList.toggle('active');
        console.log('Menu clicked, sidebar active:', sidebar.classList.contains('active'));
    });

    // Hide sidebar after link click (mobile)
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                sidebar.classList.remove('active');
                console.log('Nav link clicked, sidebar hidden');
            }
        });
    });

    // Close sidebar on outside click (mobile)
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 768 && 
            !sidebar.contains(e.target) && 
            !menuToggle.contains(e.target)) {
            sidebar.classList.remove('active');
            console.log('Clicked outside, sidebar hidden');
        }
    });

    // Swipe to open/close sidebar
    let touchStartX = 0;
    let touchEndX = 0;

    document.addEventListener('touchstart', function(e) {
        if (window.innerWidth <= 768) {
            touchStartX = e.touches[0].clientX;
            console.log('Touch start:', touchStartX);
        }
    });

    document.addEventListener('touchmove', function(e) {
        if (window.innerWidth <= 768) {
            touchEndX = e.touches[0].clientX;
        }
    });

    document.addEventListener('touchend', function(e) {
        if (window.innerWidth <= 768) {
            const swipeDistance = touchEndX - touchStartX;
            console.log('Swipe distance:', swipeDistance);
            if (swipeDistance > 75 && touchStartX < 30) { // Swipe right from left edge
                sidebar.classList.add('active');
                console.log('Swipe right, sidebar opened');
            } else if (swipeDistance < -75 && sidebar.classList.contains('active')) { // Swipe left to close
                sidebar.classList.remove('active');
                console.log('Swipe left, sidebar closed');
            }
            touchStartX = 0;
            touchEndX = 0;
        }
    });
});