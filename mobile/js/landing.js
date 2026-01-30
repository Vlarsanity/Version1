// ================================
// DOM ELEMENTS
// ================================
const sidebar = document.getElementById('mobileSidebar');
const overlay = document.getElementById('sidebarOverlay');
const toggleBtn = document.querySelector('.sidebar-toggle');
const closeBtn = document.getElementById('sidebarClose');
const themeToggleBtn = document.getElementById('themeToggle');
const bottomNavLinks = document.querySelectorAll('.bottom-nav a');
const sidebarNavLinks = document.querySelectorAll('.sidebar-nav a:not(.danger)');
const pageContents = document.querySelectorAll('.page-content');

// Carousel elements
const carouselTrack = document.getElementById('carouselTrack');
const carouselSlides = document.querySelectorAll('.carousel-slide');
const indicators = document.querySelectorAll('.indicator');

// Package filtering
const tabButtons = document.querySelectorAll('.package-tabs .tab-btn');
const packageCards = document.querySelectorAll('.package-card');

// Favorite buttons
const favoriteButtons = document.querySelectorAll('.package-favorite');



// ================================
// CAROUSEL FUNCTIONALITY
// ================================
let currentSlide = 0;
let autoplayInterval;
const autoplayDelay = 4000; // 4 seconds

function showSlide(index) {
    // Remove active class from all slides and indicators
    carouselSlides.forEach(slide => slide.classList.remove('active'));
    indicators.forEach(indicator => indicator.classList.remove('active'));

    // Add active class to current slide and indicator
    carouselSlides[index].classList.add('active');
    indicators[index].classList.add('active');

    // Move the track
    const offset = -index * 100;
    carouselTrack.style.transform = `translateX(${offset}%)`;
}

function nextSlide() {
    currentSlide = (currentSlide + 1) % carouselSlides.length;
    showSlide(currentSlide);
}

function prevSlide() {
    currentSlide = (currentSlide - 1 + carouselSlides.length) % carouselSlides.length;
    showSlide(currentSlide);
}

function startAutoplay() {
    autoplayInterval = setInterval(nextSlide, autoplayDelay);
}

function stopAutoplay() {
    clearInterval(autoplayInterval);
}

// Initialize carousel
showSlide(0);
startAutoplay();

// Indicator click events
indicators.forEach((indicator, index) => {
    indicator.addEventListener('click', () => {
        currentSlide = index;
        showSlide(currentSlide);
        stopAutoplay();
        startAutoplay(); // Restart autoplay after manual change
    });
});

// Touch swipe for carousel
let touchStartX = 0;
let touchEndX = 0;

const carouselContainer = document.querySelector('.carousel-container');

carouselContainer.addEventListener('touchstart', (e) => {
    touchStartX = e.changedTouches[0].screenX;
    stopAutoplay();
});

carouselContainer.addEventListener('touchend', (e) => {
    touchEndX = e.changedTouches[0].screenX;
    handleSwipe();
    startAutoplay(); // Restart autoplay after swipe
});

function handleSwipe() {
    const swipeThreshold = 50;
    const diff = touchStartX - touchEndX;

    if (Math.abs(diff) > swipeThreshold) {
        if (diff > 0) {
            // Swipe left - next slide
            nextSlide();
        } else {
            // Swipe right - previous slide
            prevSlide();
        }
    }
}

// Pause autoplay when page is not visible
document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
        stopAutoplay();
    } else {
        startAutoplay();
    }
});

// ================================
// SIDEBAR FUNCTIONALITY
// ================================
function openSidebar() {
    sidebar.classList.add('open');
    overlay.classList.add('active');
    toggleBtn.classList.add('active');
    document.body.style.overflow = 'hidden'; // Prevent body scroll
}

function closeSidebar() {
    sidebar.classList.remove('open');
    overlay.classList.remove('active');
    toggleBtn.classList.remove('active');
    document.body.style.overflow = ''; // Restore body scroll
}

// Toggle sidebar
toggleBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    if (sidebar.classList.contains('open')) {
        closeSidebar();
    } else {
        openSidebar();
    }
});

// Close sidebar with close button
closeBtn.addEventListener('click', closeSidebar);

// Close sidebar when clicking overlay
overlay.addEventListener('click', closeSidebar);

// Prevent sidebar click from closing
sidebar.addEventListener('click', (e) => {
    e.stopPropagation();
});

// Close sidebar on escape key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && sidebar.classList.contains('open')) {
        closeSidebar();
    }
});

// ================================
// NAVIGATION FUNCTIONALITY
// ================================
function switchPage(pageName) {
    // Hide all pages
    pageContents.forEach(page => page.classList.remove('active'));

    // Show selected page
    const selectedPage = document.getElementById(`${pageName}Page`);
    if (selectedPage) {
        selectedPage.classList.add('active');
    }

    // Update bottom nav active state
    bottomNavLinks.forEach(link => {
        link.classList.remove('active');
        if (link.dataset.page === pageName) {
            link.classList.add('active');
        }
    });

    // Update sidebar nav active state
    sidebarNavLinks.forEach(link => {
        link.classList.remove('active');
        if (link.dataset.page === pageName) {
            link.classList.add('active');
        }
    });

    // Close sidebar after navigation
    closeSidebar();

    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Bottom navigation click events
bottomNavLinks.forEach(link => {
    link.addEventListener('click', (e) => {
        e.preventDefault();
        const pageName = link.dataset.page;
        switchPage(pageName);
    });
});

// Sidebar navigation click events
sidebarNavLinks.forEach(link => {
    link.addEventListener('click', (e) => {
        e.preventDefault();
        const pageName = link.dataset.page;
        switchPage(pageName);
    });
});

// Helper function for "Browse Packages" button
function switchToHome() {
    switchPage('home');
}

// Logout functionality
const logoutLink = document.querySelector('.sidebar-nav a.danger');
if (logoutLink) {
    logoutLink.addEventListener('click', (e) => {
        e.preventDefault();
        const confirmed = confirm('Are you sure you want to logout?');
        if (confirmed) {
            // Add logout logic here
            alert('Logged out successfully!');
            closeSidebar();
        }
    });
}

// ================================
// PACKAGE FILTERING
// ================================
tabButtons.forEach(btn => {
    btn.addEventListener('click', () => {
        // Remove active class from all tabs
        tabButtons.forEach(b => b.classList.remove('active'));
        
        // Activate clicked tab
        btn.classList.add('active');
        
        const tab = btn.dataset.tab;
        
        // Filter packages with fade animation
        packageCards.forEach(card => {
            if (tab === 'all') {
                card.style.display = 'flex';
                setTimeout(() => card.classList.add('fade-in'), 10);
            } else {
                if (card.dataset.category === tab) {
                    card.style.display = 'flex';
                    setTimeout(() => card.classList.add('fade-in'), 10);
                } else {
                    card.style.display = 'none';
                }
            }
        });
    });
});

// ================================
// FAVORITE FUNCTIONALITY
// ================================
favoriteButtons.forEach(button => {
    button.addEventListener('click', (e) => {
        e.stopPropagation(); // Prevent card click
        button.classList.toggle('active');
        
        // Add haptic-like animation
        button.style.transform = 'scale(1.2)';
        setTimeout(() => {
            button.style.transform = '';
        }, 200);

        // Optional: Save to localStorage
        const card = button.closest('.package-card');
        const packageName = card.querySelector('h3').textContent;
        
        if (button.classList.contains('active')) {
            console.log(`Added ${packageName} to favorites`);
            // You can save to localStorage here
        } else {
            console.log(`Removed ${packageName} from favorites`);
            // You can remove from localStorage here
        }
    });
});

// ================================
// THEME TOGGLE FUNCTIONALITY
// ================================
function initTheme() {
    // Check for saved theme preference or default to light
    const currentTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.classList.toggle('dark', currentTheme === 'dark');
    updateThemeButton(currentTheme);
}

function updateThemeButton(theme) {
    const icon = themeToggleBtn.querySelector('.theme-icon');
    const text = themeToggleBtn.querySelector('span');
    
    if (theme === 'dark') {
        icon.classList.remove('fa-moon');
        icon.classList.add('fa-sun');
        text.textContent = 'Light Mode';
    } else {
        icon.classList.remove('fa-sun');
        icon.classList.add('fa-moon');
        text.textContent = 'Dark Mode';
    }
}

themeToggleBtn.addEventListener('click', () => {
    const isDark = document.documentElement.classList.toggle('dark');
    const theme = isDark ? 'dark' : 'light';
    localStorage.setItem('theme', theme);
    updateThemeButton(theme);
    
    // Add animation to button
    themeToggleBtn.style.transform = 'scale(0.95)';
    setTimeout(() => {
        themeToggleBtn.style.transform = '';
    }, 150);
});

// Initialize theme on page load
initTheme();

// ================================
// VIEW DETAILS FUNCTIONALITY
// ================================
const detailButtons = document.querySelectorAll('.btn-details');

detailButtons.forEach(button => {
    button.addEventListener('click', (e) => {
        e.stopPropagation();
        const card = button.closest('.package-card');
        const packageName = card.querySelector('h3').textContent;
        
        // Add your detail view logic here
        alert(`Viewing details for: ${packageName}\n\nThis would open a detailed package view.`);
    });
});

// ================================
// SMOOTH SCROLL ENHANCEMENTS
// ================================
// Add smooth reveal animation when scrolling
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('slide-up');
        }
    });
}, observerOptions);

// Observe package cards
packageCards.forEach(card => {
    observer.observe(card);
});

// ================================
// PULL TO REFRESH (Optional)
// ================================
let touchStartY = 0;
let touchEndY = 0;
const pullThreshold = 80;

document.addEventListener('touchstart', (e) => {
    if (window.scrollY === 0) {
        touchStartY = e.touches[0].clientY;
    }
});

document.addEventListener('touchmove', (e) => {
    if (window.scrollY === 0) {
        touchEndY = e.touches[0].clientY;
        const pullDistance = touchEndY - touchStartY;
        
        if (pullDistance > 0 && pullDistance < pullThreshold * 2) {
            // You can add visual feedback here
        }
    }
});

document.addEventListener('touchend', () => {
    if (window.scrollY === 0) {
        const pullDistance = touchEndY - touchStartY;
        
        if (pullDistance > pullThreshold) {
            // Trigger refresh
            console.log('Pull to refresh triggered');
            // You can add actual refresh logic here
            // For example: location.reload();
        }
        
        touchStartY = 0;
        touchEndY = 0;
    }
});


// ================================
// Package Tabs
// ================================

const tabs = document.getElementById('packageTabs');
let isDown = false;
let startX;
let scrollLeft;

tabs.addEventListener('mousedown', (e) => {
  isDown = true;
  startX = e.pageX - tabs.offsetLeft;
  scrollLeft = tabs.scrollLeft;
});

tabs.addEventListener('mouseleave', () => isDown = false);
tabs.addEventListener('mouseup', () => isDown = false);

tabs.addEventListener('mousemove', (e) => {
  if (!isDown) return;
  e.preventDefault();
  const x = e.pageX - tabs.offsetLeft;
  const walk = (x - startX) * 1.2; // drag speed
  tabs.scrollLeft = scrollLeft - walk;
});

/* Arrow buttons */
document.querySelector('.tabs-nav.left').onclick = () =>
  tabs.scrollBy({ left: -120, behavior: 'smooth' });

document.querySelector('.tabs-nav.right').onclick = () =>
  tabs.scrollBy({ left: 120, behavior: 'smooth' });












// ================================
// PERFORMANCE OPTIMIZATIONS
// ================================

// Debounce scroll events
let scrollTimeout;
window.addEventListener('scroll', () => {
    clearTimeout(scrollTimeout);
    scrollTimeout = setTimeout(() => {
        // Add scroll-based logic here if needed
    }, 100);
});

// ================================
// INITIALIZATION
// ================================
console.log('Smart Escape Travel App initialized ✈️');

// Add loading animation removal
window.addEventListener('load', () => {
    document.body.classList.add('loaded');
});

// Prevent zoom on double tap (iOS)
let lastTouchEnd = 0;
document.addEventListener('touchend', (e) => {
    const now = Date.now();
    if (now - lastTouchEnd <= 300) {
        e.preventDefault();
    }
    lastTouchEnd = now;
}, false);