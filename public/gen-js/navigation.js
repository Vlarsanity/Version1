/**
 * Navigation Module
 * Handles active state management and navigation interactions
 */

(function() {
    'use strict';

    // Wait for DOM to be ready
    document.addEventListener('DOMContentLoaded', function() {
        initNavigation();
    });

    /**
     * Initialize navigation system
     */
    function initNavigation() {
        const navButtons = document.querySelectorAll('.nav-item');
        
        if (!navButtons.length) {
            console.warn('No navigation items found');
            return;
        }

        // Attach click handlers to all nav items
        navButtons.forEach(function(button) {
            button.addEventListener('click', function(e) {
                handleNavClick(this, navButtons);
            });
        });

        // Handle initial state based on URL or default
        setInitialActiveState(navButtons);
    }

    /**
     * Handle navigation item click
     * @param {HTMLElement} clickedButton - The clicked navigation button
     * @param {NodeList} allButtons - All navigation buttons
     */
    function handleNavClick(clickedButton, allButtons) {
        // Remove active state from all buttons
        allButtons.forEach(function(btn) {
            btn.classList.remove('active');
        });

        // Add active state to clicked button
        clickedButton.classList.add('active');

        // Store active page in sessionStorage
        const page = clickedButton.getAttribute('data-page');
        if (page) {
            sessionStorage.setItem('activePage', page);
        }

        // Optional: Dispatch custom event for other modules
        const event = new CustomEvent('navigationChange', {
            detail: { page: page }
        });
        document.dispatchEvent(event);
    }

    /**
     * Set initial active state
     * @param {NodeList} navButtons - All navigation buttons
     */
    function setInitialActiveState(navButtons) {
        // Check sessionStorage for last active page
        const savedPage = sessionStorage.getItem('activePage');
        
        if (savedPage) {
            const savedButton = document.querySelector(`[data-page="${savedPage}"]`);
            if (savedButton) {
                navButtons.forEach(function(btn) {
                    btn.classList.remove('active');
                });
                savedButton.classList.add('active');
                return;
            }
        }

        // Default: ensure Dashboard is active if no state is saved
        const defaultButton = navButtons[0];
        if (defaultButton && !document.querySelector('.nav-item.active')) {
            defaultButton.classList.add('active');
        }
    }

    /**
     * Keyboard navigation support
     */
    document.addEventListener('keydown', function(e) {
        // Support keyboard shortcut (e.g., Cmd/Ctrl + K for search)
        if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
            e.preventDefault();
            const searchInput = document.querySelector('.search-input');
            if (searchInput) {
                searchInput.focus();
            }
        }
    });

})();