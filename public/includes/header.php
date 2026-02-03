<!-- Header -->
<header class="header">

    <div class="header-content">

        <!-- Sidebar toggle -->
        <button
            id="sidebarToggle"
            class="sidebar-toggle"
            aria-label="Toggle sidebar"
            aria-expanded="true">
            <i class="fas fa-bars"></i>
        </button>


        <!-- Header actions -->
        <div class="header-actions">

            <!-- Notifications -->
            <div class="header-notifications">

                <div class="notification-btn" role="button" tabindex="0" id="notificationBtn" aria-label="View notifications" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">3</span>
                </div>

                <!-- Notification Dropdown -->
                <div class="dropdown-menu" id="notificationDropdown">
                    <!-- Card Header -->
                    <div class="notification-card-header">
                        <div class="notification-header-content">
                            <div class="notification-text">
                                <div class="notification-title">Notifications</div>
                                <div class="notification-subtitle">You have 3 unread notifications</div>
                            </div>
                        </div>
                        <button class="mark-all-read-btn" aria-label="Mark all as read">
                            <span>Mark all as read</span>
                        </button>
                    </div>

                    <!-- Card Body -->
                    <div class="notification-card-body">
                        <div class="notification-item unread">
                            <div class="notification-item-icon success">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="notification-item-content">
                                <div class="notification-item-title">New Message</div>
                                <div class="notification-item-message">You have a new message from John.</div>
                                <div class="notification-item-time">2 min ago</div>
                            </div>
                        </div>
                        <div class="notification-item unread">
                            <div class="notification-item-icon info">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div class="notification-item-content">
                                <div class="notification-item-title">New User Registered</div>
                                <div class="notification-item-message">Jane Doe has joined the platform.</div>
                                <div class="notification-item-time">10 min ago</div>
                            </div>
                        </div>
                        <div class="notification-item unread">
                            <div class="notification-item-icon error">
                                <i class="fas fa-exclamation-circle"></i>
                            </div>
                            <div class="notification-item-content">
                                <div class="notification-item-title">Server Alert</div>
                                <div class="notification-item-message">Server downtime reported in US region.</div>
                                <div class="notification-item-time">30 min ago</div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Footer -->
                    <div class="notification-card-footer">
                        <button class="dropdown-item view-all">
                            <i class="fas fa-external-link-alt"></i>
                            <span>View all notifications</span>
                        </button>
                    </div>
                </div>

            </div>


            <?php
            // -----------------------------
            // Get username and initials
            // -----------------------------
            $username = $_SESSION['displayName'] ?? 'De Guzman, Andrei Vincent';
            $cleanName = trim($username);

            if (strpos($cleanName, ',') !== false) {
                [$last, $first] = array_map('trim', explode(',', $cleanName, 2));
                $parts = array_merge(explode(' ', $first), [$last]);
            } else {
                $parts = explode(' ', $cleanName);
            }

            $initials = '';
            foreach ($parts as $part) {
                if (!empty($part)) {
                    $initials .= strtoupper(mb_substr($part, 0, 1));
                }
                if (strlen($initials) === 2) break;
            }
            ?>


            <?php
            $accountTypeSafe = 'N/A';
            try {
                if (!empty($_SESSION['accountType'])) {
                    $accountTypeSafe = ucfirst(strtolower($_SESSION['accountType']));
                    $accountTypeSafe = htmlspecialchars($accountTypeSafe, ENT_QUOTES, 'UTF-8');
                }
            } catch (Throwable $e) {
                error_log('Account type output error: ' . $e->getMessage());
            }
            ?>


            <!-- Profile dropdown -->
            <div class="header-action-wrapper">

                <div class="header-profile" id="profileDropdown">

                    <!-- Main profile button -->
                    <div class="header-profile-btn" role="button" tabindex="0" id="profileToggle" aria-haspopup="true" aria-expanded="false">

                        <div class="header-profile-avatar-wrapper">
                            <img
                                src="https://ui-avatars.com/api/?name=<?php echo urlencode($initials); ?>&background=4F46E5&color=ffffff&rounded=true"
                                alt="Profile Avatar"
                                class="header-profile-avatar-image">
                        </div>

                        <div class="header-profile-name-wrapper">
                            <span class="header-profile-account-type">
                                <?php echo $accountTypeSafe; ?>
                            </span>
                            <span class="header-profile-username">
                                <?php echo htmlspecialchars($username); ?>
                            </span>
                        </div>

                        <div class="header-profile-icon-wrapper">
                            <i class="fas fa-chevron-down"></i>
                        </div>

                    </div>

                    <!-- Dropdown menu -->
                    <div class="header-profile-dropdown" id="profileMenu">

                        <!-- Profile Info -->
                        <div class="header-profile-info">
                            <img
                                src="https://ui-avatars.com/api/?name=<?php echo urlencode($initials); ?>&background=4F46E5&color=ffffff&rounded=true"
                                alt="Profile Avatar"
                                class="header-profile-info-avatar">
                            <div class="header-profile-info-text">
                                <div class="header-profile-info-name"><?php echo htmlspecialchars($username); ?></div>
                                <div class="header-profile-info-role">Admin</div>
                            </div>
                        </div>

                        <hr class="header-profile-divider">

                        <!-- Language Dropdown Section -->
                        <div class="header-profile-language-container">
                            <div class="header-profile-language-wrapper">
                                <div class="header-profile-language-label-wrapper">
                                    <span class="header-profile-language-label" data-lan-eng="Language:">Language:</span>
                                    <span class="header-profile-language-current" id="languageCurrentText">EN</span>
                                </div>
                                <div class="header-profile-language-dropdown" id="languageDropdown" tabindex="0" aria-label="Select language">
                                    <span class="language-dropdown-selected" id="languageCurrent">EN</span>
                                    <ul class="language-dropdown-menu" id="languageMenu">
                                        <li data-lang="EN">English</li>
                                        <li data-lang="KR">한국어</li>
                                    </ul>
                                </div>
                            </div>
                        </div>



                        <hr class="header-profile-divider">

                        <!-- Theme Setting Section -->
                        <div class="header-profile-theme-container">
                            <div class="header-profile-theme-wrapper">
                                <div class="header-profile-theme-label-wrapper">
                                    <span class="header-profile-theme-label">Theme:</span>
                                    <span class="header-profile-theme-current" id="themeCurrentText">Light</span>
                                </div>
                                <div class="header-profile-theme-toggle" role="button" tabindex="0" id="themeToggle" aria-label="Toggle theme">
                                    <div class="header-profile-theme-switch" id="themeSwitch"></div>
                                </div>
                            </div>
                        </div>

                        <hr class="header-profile-divider">

                        <!-- Actions Section -->
                        <div class="header-profile-actions">
                            <button class="header-profile-action-item" id="myProfileBtn">
                                <i class="fas fa-user"></i>
                                <span>My Profile</span>
                            </button>
                            <button class="header-profile-action-item" id="settingsBtn">
                                <i class="fas fa-cog"></i>
                                <span>Settings</span>
                            </button>
                            <button class="header-profile-action-item header-profile-action-danger" id="logoutBtn">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Logout</span>
                            </button>
                        </div>

                    </div>

                </div>
            </div>

        </div>

    </div>

</header>


<!-- LOGOUT MODAL -->
<div id="logoutModal" class="modal">
    <div id="modalContent" class="modal-content">
        <h2 data-lan-eng="Confirm Logout">로그아웃 확인</h2>
        <p data-lan-eng="Are you sure you want to log out?">정말 로그아웃 하시겠습니까?</p>
        <div class="modal-buttons">
            <button type="button" id="cancelLogout" class="btn-cancel" data-lan-eng="Cancel">
                취소
            </button>
            <button type="button" id="confirmLogout" class="btn-logout" data-lan-eng="Logout">
                로그아웃
            </button>
        </div>
    </div>
</div>




<!-- JS: Profile Toggle -->
<script>
    document.getElementById('profileToggle').addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            this.click();
        }
    });
</script>


<!-- JS: Notifications Toggle -->
<script>
    // Set CSRF token from PHP session
    window.CSRF_TOKEN = "<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8'); ?>";
</script>


<!-- JS: Logout Functionality -->
<script>
    document.addEventListener('DOMContentLoaded', () => {

        // Element references
        const logoutBtn = document.getElementById('logoutBtn');
        const logoutModal = document.getElementById('logoutModal');
        const modalContent = document.getElementById('modalContent');
        const cancelLogout = document.getElementById('cancelLogout');
        const confirmLogout = document.getElementById('confirmLogout');

        // Early return if elements don't exist
        if (!logoutBtn || !logoutModal || !modalContent) {
            console.warn('Logout: Required elements not found on this page');
            return;
        }

        // Validate CSRF token exists
        if (!window.CSRF_TOKEN || window.CSRF_TOKEN === '') {
            console.error('Logout: CSRF token is missing or empty');
        }

        // Modal control functions
        const openModal = () => {
            logoutModal.classList.add('show');
            // Optional: Focus on confirm button for accessibility
            setTimeout(() => confirmLogout?.focus(), 100);
        };

        const closeModal = () => {
            logoutModal.classList.remove('show');
            // Reset button state
            if (confirmLogout) {
                confirmLogout.disabled = false;
                confirmLogout.classList.remove('loading');
            }
        };

        // --------------------------------------------------
        // Event: Open modal
        // --------------------------------------------------
        logoutBtn.addEventListener('click', (e) => {
            e.preventDefault();
            openModal();
        });


        // --------------------------------------------------
        // Event: Cancel logout
        // --------------------------------------------------
        if (cancelLogout) {
            cancelLogout.addEventListener('click', (e) => {
                e.preventDefault();
                closeModal();
            });
        }

        // --------------------------------------------------
        // Event: Close modal on backdrop click
        // --------------------------------------------------
        logoutModal.addEventListener('click', (e) => {
            if (e.target === logoutModal) {
                closeModal();
            }
        });

        // --------------------------------------------------
        // Event: Confirm logout (AJAX with fallback)
        // --------------------------------------------------
        if (confirmLogout) {
            confirmLogout.addEventListener('click', async (e) => {
                e.preventDefault();

                // Prevent double-clicks
                if (confirmLogout.disabled) return;

                // Update button state
                confirmLogout.disabled = true;
                confirmLogout.classList.add('loading');

                // Optional: Update button text
                const originalText = confirmLogout.textContent;
                confirmLogout.textContent = 'Logging out...';

                try {
                    console.log('Logout: Initiating AJAX request...');

                    // Make the request
                    const response = await fetch('../../public/functions/logout.php', {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-Token': window.CSRF_TOKEN || ''
                        }
                    });

                    console.log('Logout: Response status:', response.status);
                    console.log('Logout: Response content-type:', response.headers.get('content-type'));

                    // Check response status
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }

                    // Get raw response text first (for debugging)
                    const responseText = await response.text();

                    // Check if response is empty
                    if (!responseText || responseText.trim() === '') {
                        throw new Error('Empty response from server');
                    }

                    console.log('Logout: Raw response:', responseText.substring(0, 100));

                    // Try to parse JSON
                    let data;
                    try {
                        data = JSON.parse(responseText);
                        console.log('Logout: Parsed response:', data);
                    } catch (parseError) {
                        console.error('Logout: JSON parse failed');
                        console.error('First 200 chars:', responseText.substring(0, 200));
                        console.error('First char code:', responseText.charCodeAt(0), '(should be 123 for "{")');

                        throw new Error('Invalid JSON response from server');
                    }

                    // Check for success
                    if (data.success) {
                        console.log('Logout: Success! Redirecting...');
                        closeModal();

                        // Small delay for better UX
                        setTimeout(() => {
                            window.location.href = data.redirect || '../../public/login.php';
                        }, 100);

                        return;
                    }

                    // Handle unsuccessful logout
                    throw new Error(data.message || 'Logout failed');

                } catch (error) {
                    console.error('Logout: Error occurred:', error);
                    console.error('Error type:', error.name);
                    console.error('Error message:', error.message);

                    // Reset button
                    confirmLogout.textContent = originalText;
                    confirmLogout.disabled = false;
                    confirmLogout.classList.remove('loading');

                    // --------------------------------------------------
                    // Fallback: Redirect to login page
                    // --------------------------------------------------
                    const errorMsg = error.message || 'Unknown error';
                    const shouldRedirect = confirm(
                        `A problem occurred while logging out:\n\n${errorMsg}\n\n` +
                        'Click OK to go to the login page, or Cancel to try again.'
                    );

                    if (shouldRedirect) {
                        console.log('Logout: Redirecting to login page (fallback)');
                        window.location.href = '../../public/login.php';
                    } else {
                        closeModal();
                    }
                }
            });
        }

        // --------------------------------------------------
        // Event: Close modal on ESC key
        // --------------------------------------------------
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && logoutModal.classList.contains('show')) {
                closeModal();
            }
        });

        // --------------------------------------------------
        // Debug mode (add ?debug=1 to URL to enable)
        // --------------------------------------------------
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('debug') === '1') {
            console.log('Logout Debug Info:', {
                csrfToken: window.CSRF_TOKEN ? `Present (${window.CSRF_TOKEN.substring(0, 10)}...)` : 'Missing',
                elements: {
                    logoutBtn: !!logoutBtn,
                    logoutModal: !!logoutModal,
                    modalContent: !!modalContent,
                    confirmLogout: !!confirmLogout,
                    cancelLogout: !!cancelLogout
                },
                paths: {
                    current: window.location.pathname,
                    logoutEndpoint: '../../public/functions/logout.php',
                    loginRedirect: '../../public/login.php'
                }
            });
        }
    });
</script>