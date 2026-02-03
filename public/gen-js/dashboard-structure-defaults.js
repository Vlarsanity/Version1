// ===============================
// Dashboard JS: Dropdowns, Sidebar, Language, Theme, Notifications
// ===============================

document.addEventListener("DOMContentLoaded", () => {
  // -------------------------------
  // Sidebar Dropdown Management (Enhanced)
  // -------------------------------
  const dropdownToggles = document.querySelectorAll(".nav-dropdown-toggle");

  // Get current page name from URL
  function getCurrentPage() {
    const path = window.location.pathname;
    const page = path.split("/").pop().replace(".php", "");
    return page;
  }

  // Initialize active state on page load
  document.addEventListener("DOMContentLoaded", () => {
    const currentPage = getCurrentPage();
    const currentPath = window.location.pathname;

    console.log("Current page:", currentPage); // Debug

    // Check regular nav items (like Dashboard)
    document.querySelectorAll(".nav-item[data-page]").forEach((item) => {
      if (item.dataset.page === currentPage) {
        item.classList.add("active");
        console.log("Active nav item:", item);
      }
    });

    // Check sub-nav items
    document.querySelectorAll(".sub-nav-item").forEach((item) => {
      const itemPage = item.dataset.page;
      const itemPath = item.dataset.path;
      const itemHref = item.getAttribute("href");

      // Multiple matching conditions
      const isActive =
        (itemHref && itemHref !== "#" && itemHref === window.location.href) ||
        (itemPath && itemPath === currentPath) ||
        (itemPage && itemPage === currentPage);

      if (isActive) {
        console.log("Found active sub-nav item:", item);

        // Mark sub-item as active
        item.classList.add("active");

        // Get parent dropdown and toggle
        const parentDropdown = item.closest(".nav-dropdown");
        const toggle = parentDropdown?.querySelector(".nav-dropdown-toggle");

        if (toggle) {
          console.log("Setting parent toggle as active");

          // Add persistent active state to parent
          toggle.classList.add("has-active-child");

          // Keep parent dropdown open on load (only in expanded mode)
          const sidebar = document.querySelector(".sidebar");
          if (!sidebar?.classList.contains("is-collapsed")) {
            parentDropdown.classList.add("open");
            toggle.classList.add("open");
          }
        }
      }
    });

    // Prevent default action on # links
    document.querySelectorAll('.sub-nav-item[href="#"]').forEach((item) => {
      item.addEventListener("click", (e) => {
        e.preventDefault();
        console.log("Clicked placeholder link:", item);
      });
    });
  });

  // Handle toggle clicks
  dropdownToggles.forEach((toggle) => {
    toggle.addEventListener("click", (e) => {
      e.preventDefault(); // Prevent any default button behavior

      const parent = toggle.closest(".nav-dropdown");
      const isCurrentlyOpen = parent.classList.contains("open");
      const hasActiveChild = toggle.classList.contains("has-active-child");

      console.log("Toggle clicked:", {
        isCurrentlyOpen,
        hasActiveChild,
      }); // Debug

      // Close all other dropdowns
      dropdownToggles.forEach((otherToggle) => {
        const otherParent = otherToggle.closest(".nav-dropdown");
        if (otherToggle !== toggle && otherParent.classList.contains("open")) {
          otherParent.classList.remove("open");
          otherToggle.classList.remove("open");
        }
      });

      // Toggle current dropdown
      if (parent) {
        if (isCurrentlyOpen && hasActiveChild) {
          // If open and IS active parent - keep it open (do nothing)
          console.log("Keeping active parent open");
          return;
        } else if (isCurrentlyOpen && !hasActiveChild) {
          // If open and NOT active parent - close it
          console.log("Closing non-active parent");
          parent.classList.remove("open");
          toggle.classList.remove("open");
        } else {
          // If closed - open it
          console.log("Opening dropdown");
          parent.classList.add("open");
          toggle.classList.add("open");
        }
      }
    });

    // Keyboard accessibility: Enter or Space
    toggle.addEventListener("keydown", (e) => {
      if (e.key === "Enter" || e.key === " ") {
        e.preventDefault();
        toggle.click();
      }
    });
  });

  // Optional: close non-active dropdowns when clicking outside the sidebar
  document.addEventListener("click", (e) => {
    if (!e.target.closest(".sidebar")) {
      dropdownToggles.forEach((toggle) => {
        const parent = toggle.closest(".nav-dropdown");
        if (!parent) return;

        // Don't close if parent has active child
        if (toggle.classList.contains("has-active-child")) return;

        // Close non-active dropdowns
        parent.classList.remove("open");
        toggle.classList.remove("open");
      });
    }
  });

  // -------------------------------
  // Language Dropdown with Persistence
  // -------------------------------

  // Elements
  const languageDropdown = document.getElementById("languageDropdown");
  const languageCurrent = document.getElementById("languageCurrent");
  const languageMenu = document.getElementById("languageMenu");
  const languageCurrentText = document.getElementById("languageCurrentText");

  // Translation dictionary
  const translationsKR = {
    "Member Management": "회원 관리",
    "Member List": "전체 회원 목록",
    Dashboard: "대시보드",
    "B2B Customer List": "B2B 고객 목록",
    "B2C Customer List": "B2C 고객 목록",
    "Agent List": "에이전트 목록",
    "Guide List": "가이드 목록",
    "Reservation Management": "예약 관리",
    "B2B Reservation List": "B2B 예약 목록",
    "B2C Reservation List": "B2C 예약 목록",
    "Sales Management": "판매 관리",
    "Sales by Date": "날짜별 판매",
    "Sales by Product": "상품별 판매",
    "Product Management": "상품 관리",
    "Product List": "상품 목록",
    "Product Registration": "상품 등록",
    "Inventory Management": "재고 관리",
    "Template List": "템플릿 목록",
    "Category Management": "카테고리 관리",
    "Inquiry Management": "문의 관리",
    "Member Inquiry List": "회원 문의 목록",
    "Agent Inquiry List": "에이전트 문의 목록",
    "Visa Application Management": "비자 신청 관리",
    "Visa Application List": "비자 신청 목록",
    "Site Settings Management": "사이트 설정 관리",
    "Popup Management": "팝업 관리",
    "Banner Management": "배너 관리",
    Announcements: "공지사항",
    "Terms of Use": "이용약관",
    "Company Information": "회사 정보",
    "Confirm Logout": "로그아웃 확인",
    "Are you sure you want to log out?": "정말 로그아웃 하시겠습니까?",
    Cancel: "취소",
    Logout: "로그아웃",

    // Time-based filters
    Monthly: "월간",
    Weekly: "주간",
    Daily: "일간",
    Annual: "연간",
  };

  // Current language
  let currentLang = localStorage.getItem("siteLanguage") || "EN";

  // Apply translation
  function applyTranslation(container = document) {
    container.querySelectorAll("[data-lan-eng]").forEach((el) => {
      const eng = el.getAttribute("data-lan-eng");
      el.textContent = currentLang === "EN" ? eng : translationsKR[eng] || eng;
    });

    if (languageCurrent) languageCurrent.textContent = currentLang;
    if (languageCurrentText) languageCurrentText.textContent = currentLang;
  }

  // Initialize
  applyTranslation(document);

  // Toggle dropdown menu
  languageDropdown.addEventListener("click", () => {
    languageMenu.style.display =
      languageMenu.style.display === "block" ? "none" : "block";
  });

  // Keyboard accessibility
  languageDropdown.addEventListener("keydown", (e) => {
    if (e.key === "Enter" || e.key === " ") {
      e.preventDefault();
      languageMenu.style.display =
        languageMenu.style.display === "block" ? "none" : "block";
    }
  });

  // Select language
  languageMenu.querySelectorAll("li").forEach((item) => {
    item.addEventListener("click", () => {
      currentLang = item.dataset.lang;
      localStorage.setItem("siteLanguage", currentLang);

      // Highlight selected
      languageMenu
        .querySelectorAll("li")
        .forEach((li) => li.classList.remove("selected"));
      item.classList.add("selected");

      applyTranslation();
      languageMenu.style.display = "none";
    });
  });

  // Close dropdown when clicking outside
  document.addEventListener("click", (e) => {
    if (!languageDropdown.contains(e.target)) {
      languageMenu.style.display = "none";
    }
  });

  // -------------------------------
  // Notification Dropdown
  // -------------------------------
  const notificationBtn = document.getElementById("notificationBtn");
  const notificationDropdown = document.getElementById("notificationDropdown");

  if (notificationBtn && notificationDropdown) {
    notificationBtn.addEventListener("click", (e) => {
      e.stopPropagation(); // prevent immediate close
      notificationDropdown.classList.toggle("show");
    });

    // Close dropdown if clicked outside
    document.addEventListener("click", (event) => {
      if (
        !notificationBtn.contains(event.target) &&
        !notificationDropdown.contains(event.target)
      ) {
        notificationDropdown.classList.remove("show");
      }
    });
  }

  // -------------------------------
  // Sidebar Toggle, Persistence & Responsive Sync
  // -------------------------------
  const sidebar = document.querySelector(".sidebar");
  const toggleBtn = document.getElementById("sidebarToggle");

  const SIDEBAR_COLLAPSE_KEY = "sidebar-collapsed";
  const SIDEBAR_DROPDOWN_KEY = "sidebar-open-dropdown";
  const COLLAPSE_BREAKPOINT = 1023;

  if (sidebar && toggleBtn) {
    /* -------------------------------
       Apply Sidebar State
       - forceCollapse: boolean (optional)
       - respects localStorage for small screens
    ------------------------------- */
    function applySidebarState(forceCollapse = null) {
      let isCollapsed;

      if (forceCollapse !== null) {
        isCollapsed = forceCollapse;
      } else if (window.innerWidth <= COLLAPSE_BREAKPOINT) {
        // Use stored preference if small screen
        isCollapsed = localStorage.getItem(SIDEBAR_COLLAPSE_KEY) === "true";
      } else {
        // Always expanded on large screens
        isCollapsed = false;
      }

      sidebar.classList.toggle("is-collapsed", isCollapsed);

      // Save preference only for small screens
      if (window.innerWidth <= COLLAPSE_BREAKPOINT) {
        localStorage.setItem(SIDEBAR_COLLAPSE_KEY, isCollapsed);
      }

      // Dropdown handling
      const dropdowns = document.querySelectorAll(".nav-dropdown");
      if (isCollapsed) {
        dropdowns.forEach((d) => d.classList.remove("open"));
      } else {
        const savedDropdown = localStorage.getItem(SIDEBAR_DROPDOWN_KEY);
        if (savedDropdown) {
          const el = document.querySelector(
            `.nav-dropdown[data-dropdown="${savedDropdown}"]`,
          );
          if (el) el.classList.add("open");
        }
      }
    }

    /* -------------------------------
       Manual Toggle
    ------------------------------- */
    toggleBtn.addEventListener("click", () => {
      const isCollapsed = !sidebar.classList.contains("is-collapsed");
      applySidebarState(isCollapsed);
    });

    /* -------------------------------
       Debounced Resize Handling
    ------------------------------- */
    let resizeTimeout;
    window.addEventListener("resize", () => {
      clearTimeout(resizeTimeout);
      resizeTimeout = setTimeout(() => {
        if (window.innerWidth <= COLLAPSE_BREAKPOINT) {
          applySidebarState(true); // force collapse
        } else {
          applySidebarState(false); // always expand
        }
      }, 100); // 100ms debounce
    });

    /* -------------------------------
       Initialize on Load
    ------------------------------- */
    if (window.innerWidth <= COLLAPSE_BREAKPOINT) {
      applySidebarState(true);
    } else {
      applySidebarState(false);
    }
  }

  // -------------------------------
  // Dark / Light Mode Toggle
  // -------------------------------
  const root = document.documentElement;
  const themeToggle = document.getElementById("themeToggle");
  const themeSwitch = document.getElementById("themeSwitch");
  const themeCurrentText = document.getElementById("themeCurrentText");

  if (themeToggle && themeSwitch && themeCurrentText) {
    // Initialize theme on page load
    const initializeTheme = () => {
      const isDark =
        localStorage.theme === "dark" ||
        (!localStorage.theme &&
          window.matchMedia("(prefers-color-scheme: dark)").matches);

      if (isDark) {
        root.classList.add("dark");
        themeSwitch.classList.add("active");
        themeCurrentText.textContent = "Dark";
      } else {
        root.classList.remove("dark");
        themeSwitch.classList.remove("active");
        themeCurrentText.textContent = "Light";
      }
    };

    // Initialize on page load
    initializeTheme();

    // Toggle theme on click
    themeToggle.addEventListener("click", () => {
      root.classList.toggle("dark");
      themeSwitch.classList.toggle("active");

      const isDark = root.classList.contains("dark");
      localStorage.theme = isDark ? "dark" : "light";

      // Update text to show current theme
      themeCurrentText.textContent = isDark ? "Dark" : "Light";
    });

    // Handle keyboard accessibility
    themeToggle.addEventListener("keydown", (e) => {
      if (e.key === "Enter" || e.key === " ") {
        e.preventDefault();
        themeToggle.click();
      }
    });
  }
});
