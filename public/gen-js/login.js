// ================================
// CONFIGURATION
// ================================
const CONFIG = {
  API_ENDPOINT: "../public/functions/login.php",
  ADMIN_DASHBOARD: "../public/admin/dashboard.php",
  AGENT_DASHBOARD: "../public/agent/dashboard.php",
  GUEST_DASHBOARD: "../public/guest/dashboard.php",
  EMPLOYEE_DASHBOARD: "../public/employee/dashboard.php",
  MAX_LOGIN_ATTEMPTS: 99,
  LOCKOUT_DURATION: 15 * 60 * 1000,
  REQUEST_TIMEOUT: 10000,
};

// ================================
// THEME MANAGEMENT (DARK OR LIGHT MODE)
// ================================
const ThemeManager = {
  toggle: null,
  icon: null,
  html: document.documentElement,

  init() {
    this.toggle = document.getElementById("themeToggle");
    this.icon = document.getElementById("themeIcon");

    if (!this.toggle || !this.icon) {
      console.warn("Theme toggle elements not found");
      return;
    }

    // Check for saved theme preference or default to light mode
    const currentTheme = localStorage.getItem("theme") || "light";
    if (currentTheme === "dark") {
      this.html.classList.add("dark");
      this.updateIcon(true);
    }

    // Add event listener
    this.toggle.addEventListener("click", () => this.handleToggle());
  },

  handleToggle() {
    this.html.classList.toggle("dark");
    const isDark = this.html.classList.contains("dark");
    localStorage.setItem("theme", isDark ? "dark" : "light");
    this.updateIcon(isDark);
  },

  updateIcon(isDark) {
    if (isDark) {
      // Moon icon
      this.icon.innerHTML =
        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>';
    } else {
      // Sun icon
      this.icon.innerHTML =
        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>';
    }
  },
};

// ================================
// PASSWORD VISIBILITY TOGGLE
// ================================
const PasswordToggle = {
  input: null,
  button: null,
  icon: null,

  init() {
    this.input = document.getElementById("password");
    this.button = document.getElementById("passwordToggle");
    this.icon = document.getElementById("eyeIcon");

    if (!this.input || !this.button || !this.icon) {
      console.warn("Password toggle elements not found");
      return;
    }

    this.button.addEventListener("click", () => this.handleToggle());
  },

  handleToggle() {
    const isPassword = this.input.type === "password";
    this.input.type = isPassword ? "text" : "password";
    this.updateIcon(isPassword);
  },

  updateIcon(wasPassword) {
    if (wasPassword) {
      // Eye slash icon (hide password)
      this.icon.innerHTML =
        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>';
    } else {
      // Eye icon (show password)
      this.icon.innerHTML =
        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
    }
  },
};

// ================================
// FORM VALIDATION
// ================================
const FormValidator = {
  /** Validate email format */
  isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  },

  /** Validate username format (alphanumeric, underscore, dash, 3-20 chars) */
  isValidUsername(username) {
    const usernameRegex = /^[a-zA-Z0-9_-]{3,20}$/;
    return usernameRegex.test(username);
  },

  /** Validate login form inputs */
  validateLoginForm(loginId, password) {
    // Check if fields are empty
    if (!loginId || !password) {
      return {
        valid: false,
        message: "Please fill in all fields",
      };
    }

    // Check loginId length
    if (loginId.length < 3) {
      return {
        valid: false,
        message: "Email or username must be at least 3 characters",
      };
    }

    // Check password length
    if (password.length < 6) {
      return {
        valid: false,
        message: "Password must be at least 6 characters",
      };
    }

    // Validate email format if it looks like an email
    if (loginId.includes("@") && !this.isValidEmail(loginId)) {
      return {
        valid: false,
        message: "Please enter a valid email address",
      };
    }

    // Validate username format if it's not an email
    if (!loginId.includes("@") && !this.isValidUsername(loginId)) {
      return {
        valid: false,
        message:
          "Username can only contain letters, numbers, underscore, and dash",
      };
    }

    return { valid: true };
  },

  /**
   * Sanitize input (basic XSS prevention)
   */
  sanitizeInput(input) {
    return input.trim().replace(/[<>]/g, "");
  },
};

// ================================
// RATE LIMITING
// ================================
const RateLimiter = {
  attempts: [],
  lockoutUntil: null,

  /**
   * Check if user is currently locked out
   */
  isLockedOut() {
    if (this.lockoutUntil && Date.now() < this.lockoutUntil) {
      const remainingMinutes = Math.ceil(
        (this.lockoutUntil - Date.now()) / 60000
      );
      return {
        locked: true,
        message: `Too many login attempts. Please try again in ${remainingMinutes} minute${
          remainingMinutes > 1 ? "s" : ""
        }`,
      };
    }

    // Clear lockout if expired
    if (this.lockoutUntil && Date.now() >= this.lockoutUntil) {
      this.reset();
    }

    return { locked: false };
  },

  /**
   * Record a failed login attempt
   */
  recordAttempt() {
    const now = Date.now();
    this.attempts.push(now);

    // Keep only attempts from the last 15 minutes
    this.attempts = this.attempts.filter(
      (time) => now - time < CONFIG.LOCKOUT_DURATION
    );

    // Check if we've exceeded max attempts
    if (this.attempts.length >= CONFIG.MAX_LOGIN_ATTEMPTS) {
      this.lockoutUntil = now + CONFIG.LOCKOUT_DURATION;
      this.saveToStorage();
      return true; // Locked out
    }

    this.saveToStorage();
    return false;
  },

  /**
   * Reset rate limiter
   */
  reset() {
    this.attempts = [];
    this.lockoutUntil = null;
    this.removeFromStorage();
  },

  /**
   * Save rate limit data to sessionStorage
   */
  saveToStorage() {
    sessionStorage.setItem(
      "rateLimiter",
      JSON.stringify({
        attempts: this.attempts,
        lockoutUntil: this.lockoutUntil,
      })
    );
  },

  /**
   * Load rate limit data from sessionStorage
   */
  loadFromStorage() {
    const data = sessionStorage.getItem("rateLimiter");
    if (data) {
      try {
        const parsed = JSON.parse(data);
        this.attempts = parsed.attempts || [];
        this.lockoutUntil = parsed.lockoutUntil || null;
      } catch (e) {
        console.error("Error loading rate limiter data:", e);
      }
    }
  },

  /**
   * Remove rate limit data from sessionStorage
   */
  removeFromStorage() {
    sessionStorage.removeItem("rateLimiter");
  },

  /**
   * Initialize rate limiter
   */
  init() {
    this.loadFromStorage();
  },
};

// ================================
// UI FEEDBACK
// ================================
const UIFeedback = {
  form: null,

  init() {
    this.form = document.getElementById("loginForm");
  },

  /**
   * Show error message
   */
  showError(message) {
    this.removeMessages();
    const errorDiv = document.createElement("div");
    errorDiv.className = "message-box error-message";
    errorDiv.innerHTML = `
      <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
      </svg>
      <span>${this.escapeHtml(message)}</span>
    `;

    // Insert after the signup prompt
    const signupPrompt = this.form.querySelector(".signup-prompt");
    if (signupPrompt) {
      signupPrompt.insertAdjacentElement("afterend", errorDiv);
    } else {
      this.form.appendChild(errorDiv);
    }

    // Auto-remove after 5 seconds
    setTimeout(() => this.removeMessages(), 5000);
  },

  /**
   * Show success message
   */
  showSuccess(message) {
    this.removeMessages();
    const successDiv = document.createElement("div");
    successDiv.className = "message-box success-message";
    successDiv.innerHTML = `
      <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
      </svg>
      <span>${this.escapeHtml(message)}</span>
    `;

    // Insert after the signup prompt
    const signupPrompt = this.form.querySelector(".signup-prompt");
    if (signupPrompt) {
      signupPrompt.insertAdjacentElement("afterend", successDiv);
    } else {
      this.form.appendChild(successDiv);
    }
  },

  /**
   * Remove all message boxes
   */
  removeMessages() {
    const messages = this.form.querySelectorAll(".message-box");
    messages.forEach((msg) => msg.remove());
  },

  /**
   * Set loading state
   */
  setLoading(isLoading, button, emailInput, passwordInput) {
    button.disabled = isLoading;
    emailInput.disabled = isLoading;
    passwordInput.disabled = isLoading;

    if (isLoading) {
      button.innerHTML = `
        <svg style="width: 20px; height: 20px; animation: spin 1s linear infinite; display: inline-block;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
        </svg>
        <span style="margin-left: 8px;">Logging in...</span>
      `;
    } else {
      button.innerHTML = "Login";
    }
  },

  /**
   * Escape HTML to prevent XSS
   */
  escapeHtml(text) {
    const div = document.createElement("div");
    div.textContent = text;
    return div.innerHTML;
  },
};

// ================================
// AUTHENTICATION API
// ================================
const AuthAPI = {
  /** Send login request to server */
  async login(loginId, password) {
    const controller = new AbortController();
    const timeoutId = setTimeout(
      () => controller.abort(),
      CONFIG.REQUEST_TIMEOUT
    );

    try {
      const response = await fetch(CONFIG.API_ENDPOINT, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          loginId: loginId,
          password: password,
        }),
        signal: controller.signal,
      });

      clearTimeout(timeoutId);

      // Parse JSON response
      const data = await response.json();

      // Check for HTTP errors
      if (!response.ok) {
        return {
          success: false,
          message: data.message || `Server error: ${response.status}`,
          statusCode: response.status,
        };
      }

      return data;
    } catch (error) {
      clearTimeout(timeoutId);

      if (error.name === "AbortError") {
        return {
          success: false,
          message:
            "Request timeout. Please check your connection and try again.",
        };
      }

      console.error("Login API error:", error);
      return {
        success: false,
        message: "Unable to connect to server. Please try again later.",
      };
    }
  },

  /**
   * Redirect to dashboard
   */
  redirect(url, delay = 1000) {
    setTimeout(() => {
      window.location.href = url;
    }, delay);
  },
};

// ================================
// LOGIN FORM HANDLER
// ================================
const LoginFormHandler = {
  form: null,
  button: null,
  emailInput: null,
  passwordInput: null,
  rememberCheckbox: null,

  init() {
    this.form = document.getElementById("loginForm");
    if (!this.form) {
      console.error("Login form not found");
      return;
    }

    this.button = this.form.querySelector(".login-button");
    this.emailInput = document.getElementById("email");
    this.passwordInput = document.getElementById("password");
    this.rememberCheckbox = document.getElementById("remember");

    if (!this.button || !this.emailInput || !this.passwordInput) {
      console.error("Required form elements not found");
      return;
    }

    // Submit event
    this.form.addEventListener("submit", (e) => this.handleSubmit(e));

    // Enter key support for password field
    this.passwordInput.addEventListener("keypress", (e) => {
      if (e.key === "Enter") {
        e.preventDefault(); // prevent native submit
        this.handleSubmit(e); // call handler directly
      }
    });
  },

  async handleSubmit(e) {
    if (e.cancelable) e.preventDefault(); // ensure default submit is prevented

    // Check rate limiting
    const lockoutStatus = RateLimiter.isLockedOut();
    if (lockoutStatus.locked) {
      UIFeedback.showError(lockoutStatus.message);
      return;
    }

    // Get form values
    const loginId = FormValidator.sanitizeInput(this.emailInput.value);
    const password = this.passwordInput.value; // Do not sanitize password
    const remember = this.rememberCheckbox
      ? this.rememberCheckbox.checked
      : false;

    // Validate form
    const validation = FormValidator.validateLoginForm(loginId, password);
    if (!validation.valid) {
      UIFeedback.showError(validation.message);
      return;
    }

    // Show loading state
    UIFeedback.setLoading(
      true,
      this.button,
      this.emailInput,
      this.passwordInput
    );

    try {
      // Call authentication API
      const response = await AuthAPI.login(loginId, password);

      if (response.success) {
        // Reset rate limiter
        RateLimiter.reset();

        // Show success message
        UIFeedback.showSuccess(
          response.message || "Login successful! Redirecting..."
        );

        // Redirect based on user type from response.data.accountType
        const userType = response.data?.accountType || "admin"; // default to admin

        switch (userType) {
          case "admin":
            AuthAPI.redirect(CONFIG.ADMIN_DASHBOARD, 1000);
            break;
          case "agent":
            AuthAPI.redirect(CONFIG.AGENT_DASHBOARD, 1000);
            break;
          case "guest":
            AuthAPI.redirect(CONFIG.GUEST_DASHBOARD, 1000);
            break;
          case "employee":
            AuthAPI.redirect(CONFIG.EMPLOYEE_DASHBOARD, 1000);
            break;
          default:
            console.warn("Unknown user type. Redirecting to admin dashboard.");
            AuthAPI.redirect(CONFIG.ADMIN_DASHBOARD, 1000);
        }
      } else {
        const isLockedOut = RateLimiter.recordAttempt();

        // Always show server response message
        UIFeedback.showError(
          response.message || "Invalid credentials. Please try again."
        );

        // Override message if now locked out
        if (isLockedOut) {
          UIFeedback.showError(
            `Too many failed attempts. Account locked for ${
              CONFIG.LOCKOUT_DURATION / 60000
            } minutes.`
          );
        }

        // Clear password field
        this.passwordInput.value = "";
        this.passwordInput.focus();
      }
    } catch (error) {
      console.error("Login error:", error);
      UIFeedback.showError("An unexpected error occurred. Please try again.");
    } finally {
      UIFeedback.setLoading(
        false,
        this.button,
        this.emailInput,
        this.passwordInput
      );
    }
  },
};

// ================================
// INITIALIZE ALL MODULES
// ================================
document.addEventListener("DOMContentLoaded", () => {
  console.log("Initializing login page...");

  // Initialize all modules
  ThemeManager.init();
  PasswordToggle.init();
  RateLimiter.init();
  UIFeedback.init();
  LoginFormHandler.init();

  console.log("Login page initialized successfully");
});
