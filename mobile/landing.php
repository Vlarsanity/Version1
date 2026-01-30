<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Escape - Travel & Tours</title>

    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts - Travel-themed elegant fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="../mobile/assets/css/root.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="../mobile/assets/css/mobile.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="../mobile/assets/css/landing.css?v=<?= time(); ?>">
</head>

<body>
    <!-- Top Navigation -->
    <header class="mobile-header">
        <div class="header-left">
            <button class="sidebar-toggle" aria-label="Menu">
                <span></span>
            </button>
        </div>

        <div class="header-center">
            <img src="../mobile/assets/images/logo.png" alt="Smart Escape Logo" class="header-logo">
            <h2>SMART ESCAPE</h2>
        </div>


        <div class="header-right">
            <img src="" alt="" class="profile-avatar">
        </div>
    </header>

    <!-- Sidebar Overlay (Backdrop) -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <aside class="mobile-sidebar" id="mobileSidebar">

        <div class="sidebar-header">
            <div class="sidebar-profile">
                <img src="" alt="" class="sidebar-avatar">
                <div class="sidebar-profile-info">
                    <h3>John Traveler</h3>
                    <p>john@travel.com</p>
                </div>
            </div>

            <button class="sidebar-close" id="sidebarClose" aria-label="Close menu">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <nav class="sidebar-nav">
            <a href="#" class="active" data-page="home">
                <i class="fas fa-home"></i>
                <span>Home</span>
            </a>
            <a href="#" data-page="bookings">
                <i class="fas fa-calendar-check"></i>
                <span>My Bookings</span>
            </a>
            <a href="#" data-page="packages">
                <i class="fas fa-box"></i>
                <span>All Packages</span>
            </a>
            <a href="#" data-page="favorites">
                <i class="fas fa-heart"></i>
                <span>Favorites</span>
            </a>
            <a href="#" data-page="profile">
                <i class="fas fa-user"></i>
                <span>Profile Settings</span>
            </a>
            <div class="sidebar-divider"></div>
            <a href="#" class="danger" data-page="logout">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </nav>

        <!-- Theme Toggle Button -->
        <div class="sidebar-footer">
            <div class="theme-toggle-container">
                <div class="theme-toggle-label">
                    <span>Theme</span>
                </div>
                <label class="toggle-switch" for="themeToggle">
                    <input type="checkbox" id="themeToggle" aria-label="Toggle dark mode">
                    <span class="toggle-slider"></span>
                </label>
            </div>
        </div>

    </aside>

    <!-- Main Content -->
    <main class="dashboard">

        <!-- Home Page Content -->
        <div class="page-content active" id="homePage">

            <!-- Hero Carousel -->
            <div class="hero-carousel" id="heroCarousel">
                <div class="carousel-container">
                    <div class="carousel-track" id="carouselTrack">


                        <div class="carousel-slide active">
                            <img src="https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?w=800&h=400&fit=crop" alt="Travel Destination 1">
                            <div class="carousel-overlay"></div>
                            <div class="carousel-content">
                                <h3>Discover Paradise</h3>
                                <p>Exclusive beach destinations</p>
                            </div>
                        </div>


                        <div class="carousel-slide">
                            <img src="https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?w=800&h=400&fit=crop" alt="Travel Destination 2">
                            <div class="carousel-overlay"></div>
                            <div class="carousel-content">
                                <h3>Urban Adventures</h3>
                                <p>Explore vibrant city life</p>
                            </div>
                        </div>


                        <div class="carousel-slide">
                            <img src="https://images.unsplash.com/photo-1528164344705-47542687000d?w=800&h=400&fit=crop" alt="Travel Destination 3">
                            <div class="carousel-overlay"></div>
                            <div class="carousel-content">
                                <h3>Mountain Escapes</h3>
                                <p>Breathtaking alpine views</p>
                            </div>
                        </div>


                        <div class="carousel-slide">
                            <img src="https://images.unsplash.com/photo-1506929562872-bb421503ef21?w=800&h=400&fit=crop" alt="Travel Destination 4">
                            <div class="carousel-overlay"></div>
                            <div class="carousel-content">
                                <h3>Cultural Journeys</h3>
                                <p>Immerse in local traditions</p>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="carousel-indicators" id="carouselIndicators">
                    <span class="indicator active" data-slide="0"></span>
                    <span class="indicator" data-slide="1"></span>
                    <span class="indicator" data-slide="2"></span>
                    <span class="indicator" data-slide="3"></span>
                </div>
            </div>

            <!-- Package Tabs Header -->
           <div class="package-header">
            
                <div class="package-title">
                    <div class="package-title-text">
                        <h2>Travel Packages</h2>
                        <p class="package-subtitle">Find your perfect getaway</p>
                    </div>

                    <div class="package-title-action">
                        <a href="#" class="package-see-all">See all</a>
                    </div>
                </div>


                <div class="package-tabs-wrapper">
                    <!-- <button class="tabs-nav left" disabled>
                        <i class="fas fa-chevron-left"></i>
                    </button> -->

                    <div class="package-tabs" id="packageTabs">
                        <button class="tab-btn active" data-tab="all">
                           
                            <span>All</span>
                        </button>

                        <button class="tab-btn" data-tab="korea">
                            
                            <span>Korea</span>
                        </button>

                        <button class="tab-btn" data-tab="japan">
                          
                            <span>Japan</span>
                        </button>

                        <button class="tab-btn" disabled data-tab="japan">
                            <span>Vietnam</span>
                        </button>

                        <button class="tab-btn" disabled data-tab="japan">
                            <span>Thailand</span>
                        </button>

                        <button class="tab-btn" disabled>
                            <span>Coming Soon</span>
                        </button>

                    </div>

                    <!-- <button class="tabs-nav right">
                        <i class="fas fa-chevron-right"></i>
                    </button> -->
                </div>

            </div>


            <!-- Package Grid -->
            <div class="package-grid">
                <!-- Featured Card (Full Width) -->
                <div class="package-card featured" data-category="korea">
                    <div class="package-badge">Featured</div>
                    <button class="package-favorite" aria-label="Add to favorites">
                        <i class="far fa-heart"></i>
                    </button>
                    <img src="https://images.unsplash.com/photo-1517154421773-0529f29ea451?w=800&h=400&fit=crop" alt="Korea City Tour">
                    <div class="package-info">
                        <div class="package-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                            <span>4.8</span>
                        </div>
                        <h3>Seoul City Explorer</h3>
                        <p><i class="far fa-clock"></i> 5 Days / 4 Nights</p>
                        <div class="package-footer">
                            <span class="price">₱29,999</span>
                            <button class="btn-details">View Details</button>
                        </div>
                    </div>
                </div>

                <!-- Regular Cards -->
                <div class="package-card" data-category="japan">
                    <button class="package-favorite" aria-label="Add to favorites">
                        <i class="far fa-heart"></i>
                    </button>
                    <img src="https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?w=600&h=400&fit=crop" alt="Tokyo Explorer">
                    <div class="package-info">
                        <div class="package-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <span>5.0</span>
                        </div>
                        <h3>Tokyo Explorer</h3>
                        <p><i class="far fa-clock"></i> 7 Days / 6 Nights</p>
                        <div class="package-footer">
                            <span class="price">₱49,999</span>
                            <button class="btn-details">View</button>
                        </div>
                    </div>
                </div>

                <div class="package-card" data-category="japan">
                    <div class="package-badge popular">Popular</div>
                    <button class="package-favorite" aria-label="Add to favorites">
                        <i class="far fa-heart"></i>
                    </button>
                    <img src="https://images.unsplash.com/photo-1528164344705-47542687000d?w=600&h=400&fit=crop" alt="Kyoto & Osaka">
                    <div class="package-info">
                        <div class="package-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="far fa-star"></i>
                            <span>4.5</span>
                        </div>
                        <h3>Kyoto & Osaka</h3>
                        <p><i class="far fa-clock"></i> 9 Days / 8 Nights</p>
                        <div class="package-footer">
                            <span class="price">₱69,999</span>
                            <button class="btn-details">View</button>
                        </div>
                    </div>
                </div>

                <div class="package-card" data-category="korea">
                    <button class="package-favorite" aria-label="Add to favorites">
                        <i class="far fa-heart"></i>
                    </button>
                    <img src="https://images.unsplash.com/photo-1509023464722-18d996393ca8?w=600&h=400&fit=crop" alt="Busan Beach">
                    <div class="package-info">
                        <div class="package-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="far fa-star"></i>
                            <span>4.3</span>
                        </div>
                        <h3>Busan Beach</h3>
                        <p><i class="far fa-clock"></i> 4 Days / 3 Nights</p>
                        <div class="package-footer">
                            <span class="price">₱24,999</span>
                            <button class="btn-details">View</button>
                        </div>
                    </div>
                </div>

                <div class="package-card" data-category="japan">
                    <button class="package-favorite" aria-label="Add to favorites">
                        <i class="far fa-heart"></i>
                    </button>
                    <img src="https://images.unsplash.com/photo-1542640244-7e672d6cef4e?w=600&h=400&fit=crop" alt="Hokkaido Winter">
                    <div class="package-info">
                        <div class="package-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                            <span>4.7</span>
                        </div>
                        <h3>Hokkaido Winter</h3>
                        <p><i class="far fa-clock"></i> 6 Days / 5 Nights</p>
                        <div class="package-footer">
                            <span class="price">₱54,999</span>
                            <button class="btn-details">View</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Bookings Page Content -->
        <div class="page-content" id="bookingsPage">
            <div class="page-header">
                <h2>My Bookings</h2>
                <p>View and manage your travel reservations</p>
            </div>
            <div class="empty-state">
                <i class="fas fa-calendar-alt"></i>
                <h3>No bookings yet</h3>
                <p>Start exploring our packages and book your dream vacation!</p>
                <button class="btn-primary" onclick="switchToHome()">Browse Packages</button>
            </div>
        </div>

        <!-- Profile Page Content -->
        <div class="page-content" id="profilePage">
            <div class="page-header">
                <h2>Profile Settings</h2>
                <p>Manage your account information</p>
            </div>
            <div class="profile-content">
                <div class="profile-section">
                    <div class="profile-avatar-large">
                        <img src="" alt="">
                        <button class="avatar-edit">
                            <i class="fas fa-camera"></i>
                        </button>
                    </div>
                    <h3>John Traveler</h3>
                    <p class="profile-email">john@travel.com</p>
                </div>

                <div class="profile-menu">
                    <a href="#" class="profile-menu-item">
                        <i class="fas fa-user"></i>
                        <span>Personal Information</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <a href="#" class="profile-menu-item">
                        <i class="fas fa-credit-card"></i>
                        <span>Payment Methods</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <a href="#" class="profile-menu-item">
                        <i class="fas fa-bell"></i>
                        <span>Notifications</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <a href="#" class="profile-menu-item">
                        <i class="fas fa-shield-alt"></i>
                        <span>Privacy & Security</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <a href="#" class="profile-menu-item">
                        <i class="fas fa-question-circle"></i>
                        <span>Help & Support</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </main>

    
    <!-- Bottom Navigation -->
    <nav class="bottom-nav">
        <a class="active" data-page="home">
            <div class="nav-indicator"></div>
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a data-page="bookings">
            <div class="nav-indicator"></div>
            <i class="fas fa-calendar-check"></i>
            <span>Bookings</span>
        </a>
        <a data-page="profile">
            <div class="nav-indicator"></div>
            <i class="fas fa-user"></i>
            <span>Profile</span>
        </a>
    </nav>

    <!-- Scripts -->
    <script src="../mobile/js/landing.js"></script>
    <script src="../mobile/js/theme-toggle.js"></script>
</body>







</html>