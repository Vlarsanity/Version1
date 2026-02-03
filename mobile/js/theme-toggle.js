/* ================================
   ENHANCED THEME TOGGLE JAVASCRIPT
   ================================ */

// Get the toggle checkbox
const themeToggle = document.getElementById('themeToggle');
const themeLabelIcon = document.querySelector('.theme-label-icon');

// Initialize theme on page load
function initTheme() {
    // Check for saved theme preference or default to light
    const currentTheme = localStorage.getItem('theme') || 'light';
    const isDark = currentTheme === 'dark';
    
    // Apply theme
    document.documentElement.classList.toggle('dark', isDark);
    
    // Update checkbox state
    themeToggle.checked = isDark;
    
    // Update label icon
    updateLabelIcon(isDark);
}

// Update the label icon based on current theme
function updateLabelIcon(isDark) {
    if (isDark) {
        themeLabelIcon.classList.remove('fa-sun');
        themeLabelIcon.classList.add('fa-moon');
    } else {
        themeLabelIcon.classList.remove('fa-moon');
        themeLabelIcon.classList.add('fa-sun');
    }
}

// Handle theme toggle
themeToggle.addEventListener('change', function() {
    const isDark = this.checked;
    
    // Toggle dark class with smooth transition
    document.documentElement.classList.toggle('dark', isDark);
    
    // Save preference
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
    
    // Update label icon with animation
    updateLabelIcon(isDark);
    
    // Add a subtle pulse animation to the container
    const container = document.querySelector('.theme-toggle-container');
    container.style.transform = 'scale(0.98)';
    setTimeout(() => {
        container.style.transform = 'scale(1)';
    }, 100);
    
    // Optional: Log for debugging
    console.log(`Theme switched to: ${isDark ? 'dark' : 'light'} mode`);
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', initTheme);

// Optional: Listen for system theme changes
if (window.matchMedia) {
    const darkModeQuery = window.matchMedia('(prefers-color-scheme: dark)');
    
    darkModeQuery.addEventListener('change', (e) => {
        // Only auto-switch if user hasn't set a preference
        if (!localStorage.getItem('theme')) {
            const isDark = e.matches;
            document.documentElement.classList.toggle('dark', isDark);
            themeToggle.checked = isDark;
            updateLabelIcon(isDark);
        }
    });
}