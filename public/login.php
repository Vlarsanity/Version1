<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <!-- 공통 스타일 -->
    <link rel="shortcut icon" href="../favicon.ico" />

    <!-- Initial Links -->

    <!-- Root Styles (Always on Top)-->
    <link href="../public/assets/css/root.css?v=<?= time(); ?>" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Page Specifics -->
    <link href="../public/assets/css/login.css?v=<?= time(); ?>" rel="stylesheet">

</head>

<body>

    <!-- Header -->
    <header class="header">
        <div class="header-left">
            <img src="./assets/images/logo.png" alt="Company Logo" class="header-logo">
            <span class="header-name">SMT-ESCAPE</span>
        </div>
        <div class="header-right">
            <!-- Dark Mode Toggle -->
            <button class="theme-toggle" id="themeToggle" aria-label="Toggle dark mode">
                <svg class="theme-toggle-icon" id="themeIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </button>
        </div>
    </header>

    <!-- Body Content -->
    <div class="body-content">

        <!-- Login Container -->
        <div class="login-container">

            <!-- Logo Section -->
            <div class="logo-container">
                <!-- <img src="../favicon.png" alt="Company Logo" class="logo-image"> -->
                <h1 class="logo-text">Welcome Back</h1>
                <p class="logo-subtitle">Sign in to continue</p>
            </div>

            <!-- Login Form -->
            <form class="login-form" id="loginForm">

                <!-- Email/Username Field -->
                <div class="form-group">
                    <label class="form-label" for="email">Email or Username</label>
                    <input
                        type="text"
                        id="email"
                        class="form-input"
                        placeholder="Enter your email or username"
                        required>
                </div>

                <!-- Password Field -->
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-wrapper">
                        <input
                            type="password"
                            id="password"
                            class="form-input"
                            placeholder="Enter your password"
                            required>
                        <button type="button" class="password-toggle" id="passwordToggle" aria-label="Toggle password visibility">
                            <!-- Eye icon (show password) -->
                            <svg id="eyeIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="form-options">
                    <div class="checkbox-wrapper">
                        <input type="checkbox" id="remember" class="checkbox-input">
                        <label for="remember" class="checkbox-label">Remember me</label>
                    </div>
                    <a href="#" class="forgot-link">Forgot password?</a>
                </div>

                <!-- Login Button -->
                <button type="submit" class="login-button">Login</button>

                <!-- Divider (for future social login) -->
                <!-- <div class="divider">
                        <span>or continue with</span>
                    </div> -->

                <!-- Social Login Buttons (Ready for future use) -->
                <!-- <div class="social-login">
                        <button type="button" class="social-button">
                        <span>Continue with Google</span>
                        </button>
                        <button type="button" class="social-button">
                        <span>Continue with GitHub</span>
                        </button>
                    </div> -->


                <!-- Sign Up Prompt -->
                <div class="signup-prompt">
                    Don't have an account? <a href="#" class="signup-link">Sign up</a>
                </div>

            </form>

        </div>

    </div>

</body>




<!-- JavaScript -->
<script src="./gen-js/login.js"></script>

</html>