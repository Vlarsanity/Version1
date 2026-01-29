// Grab all theme toggle buttons
const themeToggles = document.querySelectorAll('[data-theme-toggle]');
const root = document.documentElement;

// Update button icon based on current theme
function updateThemeIcon() {
  const isDark = root.classList.contains('dark');
  themeToggles.forEach(btn => {
    const icon = btn.querySelector('.theme-icon') || btn;
    if (icon) icon.textContent = isDark ? '☀️' : '🌙';
  });
}

// Initialize icons on page load
updateThemeIcon();

// Attach click listener to all toggles
themeToggles.forEach(btn => {
  btn.addEventListener('click', () => {
    root.classList.toggle('dark');
    updateThemeIcon();

    // Send GET request to server to update session
    fetch(`${window.location.pathname}?theme=${root.classList.contains('dark') ? 'dark' : 'light'}`, {
      method: 'GET',
      credentials: 'same-origin'
    }).then(() => console.log('Theme updated in session'));
  });
});
