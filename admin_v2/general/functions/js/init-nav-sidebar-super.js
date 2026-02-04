init({
  headerUrl: "../../admin_v2/inc/header.html",
  navUrl: "../../admin_v2/inc/nav_super copy.html",
});

// Prevent flash of unstyled navigation
document.documentElement.style.setProperty('--nav-transition-speed', '0s');

// Initialize after content loads
setTimeout(initComponents, 500);
window.addEventListener("load", initComponents);
if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", initComponents);
} else {
  initComponents();
}

function initComponents() {
  initNavigation();
  initMemberMenu();
}

function initNavigation() {
  const navButtons = document.querySelectorAll(".nav-btn");
  if (navButtons.length === 0) {
    setTimeout(initNavigation, 200);
    return;
  }

  // Set active based on current page FIRST (before adding click handlers)
  const currentPage = window.location.pathname
    .split("/")
    .pop()
    .replace(".html", "")
    .replace(".php", "");
  
  let pageFound = false;

  // Apply active states immediately without transition
  document.querySelectorAll(".side-link").forEach((link) => {
    const pageAttr = link.getAttribute("data-page");
    if (pageAttr) {
      const pages = pageAttr.split(",").map((p) => p.trim().toLowerCase());
      if (pages.includes(currentPage.toLowerCase())) {
        link.classList.add("active");
        const navItem = link.closest(".nav-item");
        navItem.classList.add("active", "no-transition");
        
        // Save this as the active nav item
        const navItemIndex = Array.from(document.querySelectorAll(".nav-item")).indexOf(navItem);
        sessionStorage.setItem(`nav-item-${navItemIndex}`, "open");
        pageFound = true;
      }
    }
  });

  // If no page match found, restore session storage state
  if (!pageFound) {
    document.querySelectorAll(".nav-item").forEach((navItem, index) => {
      if (sessionStorage.getItem(`nav-item-${index}`) === "open") {
        navItem.classList.add("active", "no-transition");
      }
    });
  }

  // Enable transitions after initial state is set
  requestAnimationFrame(() => {
    document.documentElement.style.setProperty('--nav-transition-speed', '0.3s');
    document.querySelectorAll(".nav-item").forEach((item) => {
      item.classList.remove("no-transition");
    });
  });

  // NOW add click handlers
  navButtons.forEach((button) => {
    button.addEventListener("click", function (e) {
      e.preventDefault();
      const navItem = this.closest(".nav-item");
      const navItemIndex = Array.from(document.querySelectorAll(".nav-item")).indexOf(navItem);
      const wasActive = navItem.classList.contains("active");

      // Close other open menus
      document.querySelectorAll(".nav-item").forEach((item, index) => {
        if (item !== navItem) {
          item.classList.remove("active");
          sessionStorage.removeItem(`nav-item-${index}`);
        }
      });

      // Toggle current menu
      navItem.classList.toggle("active");
      
      // Save state to session storage
      if (!wasActive) {
        sessionStorage.setItem(`nav-item-${navItemIndex}`, "open");
      } else {
        sessionStorage.removeItem(`nav-item-${navItemIndex}`);
      }
    });
  });
}

function initMemberMenu() {
  const memberBtn = document.querySelector(".memberbtn");
  if (!memberBtn) {
    setTimeout(initMemberMenu, 200);
    return;
  }

  memberBtn.addEventListener("click", function (e) {
    e.stopPropagation();
    const position = this.nextElementSibling;
    const isActive = position.classList.contains("active");

    // Close all dropdowns first
    document.querySelectorAll(".membermenu .position").forEach((p) => {
      p.classList.remove("active");
    });

    // Toggle current dropdown
    if (!isActive) {
      position.classList.add("active");
      // Load content if wrap is empty
      const wrap = position.querySelector(".wrap");
      if (wrap && wrap.innerHTML.trim() === "") {
        fetch("../inc/header_memberinfo.html")
          .then((response) => response.text())
          .then((html) => {
            wrap.innerHTML = html;
          })
          .catch((error) => {
            console.error("Error loading menu:", error);
          });
      }
    }
  });

  // Close dropdown when clicking outside
  document.addEventListener("click", function (e) {
    if (!e.target.closest(".membermenu")) {
      document.querySelectorAll(".membermenu .position").forEach((p) => {
        p.classList.remove("active");
      });
    }
  });
}