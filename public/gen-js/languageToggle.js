// languageToggle.js - FIXED VERSION
(function () {
  const langToggle = document.getElementById("langToggle");
  const langLabel = document.getElementById("langLabel");
  
  // Profile dropdown toggle elements
  const languageToggleProfile = document.getElementById("languageToggle");
  const languageSwitchProfile = document.getElementById("languageSwitch");
  const languageCurrentText = document.getElementById("languageCurrentText");
  
  const fadeDuration = 180; // ms
  
  // Default language
  let isEN = true;
  const savedLang = localStorage.getItem("siteLanguage");
  if (savedLang) isEN = savedLang === "EN";
  
  // Initialize elements with data-lan-eng
  function initTranslation(container = document) {
    const elements = container.querySelectorAll("[data-lan-eng]");
    elements.forEach(el => {
      if (!el.hasAttribute("data-lan-other")) {
        el.setAttribute("data-lan-other", el.textContent);
        el.style.transition = `opacity ${fadeDuration}ms ease`;
      }
    });
  }
  
  // Apply translation
  function applyTranslation(container = document, instant = false) {
    const elements = container.querySelectorAll("[data-lan-eng]");
    elements.forEach(el => {
      const engText = el.getAttribute("data-lan-eng");
      const otherText = el.getAttribute("data-lan-other");
      const newText = isEN ? engText : otherText;
      
      if (instant) {
        el.textContent = newText;
        return;
      }
      
      el.style.opacity = 0;
      setTimeout(() => {
        el.textContent = newText;
        el.style.opacity = 1;
      }, fadeDuration);
    });
    
    // Update both language toggle buttons
    if (langLabel) langLabel.textContent = isEN ? "EN" : "KR";
    if (languageCurrentText) languageCurrentText.textContent = isEN ? "EN" : "KR";
    
    // Update profile toggle switch state
    if (languageSwitchProfile) {
      if (isEN) {
        languageSwitchProfile.classList.remove('active');
      } else {
        languageSwitchProfile.classList.add('active');
      }
    }
  }
  
  // Toggle language function
  function toggleLanguage() {
    isEN = !isEN;
    localStorage.setItem("siteLanguage", isEN ? "EN" : "KR");
    applyTranslation();
    
    // Debug log
    console.log('Language toggled to:', isEN ? 'EN' : 'KR');
  }
  
  // Wait for DOM to be fully loaded
  function initializeLanguageToggle() {
    console.log('Initializing language toggle...');
    console.log('languageToggleProfile found:', !!languageToggleProfile);
    console.log('languageSwitchProfile found:', !!languageSwitchProfile);
    console.log('languageCurrentText found:', !!languageCurrentText);
    
    // Initialize on page load
    initTranslation();
    applyTranslation(document, true);
    
    // Header toggle button (if exists)
    if (langToggle) {
      langToggle.addEventListener("click", toggleLanguage);
      console.log('Header language toggle initialized');
    }
    
    // Profile dropdown toggle button
    if (languageToggleProfile) {
      // Remove any existing listeners by cloning
      const newToggle = languageToggleProfile.cloneNode(true);
      languageToggleProfile.parentNode.replaceChild(newToggle, languageToggleProfile);
      
      // Add click listener
      newToggle.addEventListener("click", function(e) {
        e.preventDefault();
        e.stopPropagation();
        toggleLanguage();
      });
      
      // Keyboard support for profile toggle
      newToggle.addEventListener("keydown", (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          e.stopPropagation();
          toggleLanguage();
        }
      });
      
      console.log('Profile language toggle initialized');
    } else {
      console.error('languageToggle element not found!');
    }
    
    // Watch for dynamically added elements
    const observer = new MutationObserver(mutations => {
      mutations.forEach(mutation => {
        mutation.addedNodes.forEach(node => {
          if (node.nodeType === 1) {
            initTranslation(node);
            applyTranslation(node, true); // instant for new elements
          }
        });
      });
    });
    
    observer.observe(document.body, { childList: true, subtree: true });
  }
  
  // Initialize when DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeLanguageToggle);
  } else {
    // DOM is already loaded
    initializeLanguageToggle();
  }
})();