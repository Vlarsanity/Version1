// Navigation toggle functionality
document.addEventListener("DOMContentLoaded", function () {
  const navButtons = document.querySelectorAll(".nav-btn");

  navButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const navItem = this.closest(".nav-item");
      const isActive = navItem.classList.contains("active");

      // Optional: Close other open menus (accordion behavior)
      document.querySelectorAll(".nav-item").forEach((item) => {
        if (item !== navItem) {
          item.classList.remove("active");
        }
      });

      // Toggle current menu
      navItem.classList.toggle("active");
    });
  });

  // Optional: Set active state based on current page
  const currentPage =
    document.body.dataset.page ||
    window.location.pathname.split("/").pop().replace(".html", "");

  document.querySelectorAll(".side-link").forEach((link) => {
    const pageAttr = link.getAttribute("data-page");
    if (
      pageAttr &&
      pageAttr
        .split(",")
        .map((p) => p.trim())
        .includes(currentPage)
    ) {
      link.classList.add("active");
      link.closest(".nav-item").classList.add("active");
    }
  });
});
