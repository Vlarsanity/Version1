<!DOCTYPE html>

<?php include_once("../mobile/includes/themes-session.php"); ?>

<html lang="en" class="<?php echo $themeClass; ?>">

<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../mobile/assets/css/root.css">
    <link rel="stylesheet" href="../mobile/assets/css/mobile.css">
    <link rel="stylesheet" href="../mobile/assets/css/landing.css">

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
            <div class="landing-logo">
                <!-- <img src="../mobile/assets/images/logo.png" alt="App Logo"> -->
            </div>
            <h2>SMART-ESCAPE</h2>
        </div>

        <div class="header-right">
            <img src="avatar.png" alt="Profile" class="profile-avatar">
        </div>
    </header>


    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <aside class="mobile-sidebar" id="mobileSidebar">
        <nav class="sidebar-nav">
            <a class="active">Home</a>
            <a>Bookings</a>
            <a>Packages</a>
            <a>Profile</a>
            <a class="danger">Logout</a>
        </nav>

        <!-- Theme Toggle Button -->
        <div class="sidebar-theme-toggle">
            <button class="theme-toggle-btn" data-theme-toggle aria-label="Toggle theme">🌙</button>
        </div>

    </aside>




    <!-- Content -->

    <main class="dashboard">

        <!-- Package Tabs Header -->
        <div class="package-header">

            <div class="package-title">
                <h2>Travel Packages</h2>
            </div>

            <div class="package-tabs">
                <button class="tab-btn active" data-tab="all">All</button>
                <button class="tab-btn" data-tab="korea">Korea</button>
                <button class="tab-btn" data-tab="japan">Japan</button>
            </div>
        </div>

        <!-- Package Grid -->
        <div class="package-grid">

            <div class="package-card" data-category="korea">
                <img src="tour1.jpg" alt="Tour">
                <div class="package-info">
                    <h3>Korea City Tour</h3>
                    <p>5 Days / 4 Nights</p>
                    <span class="price">₱29,999</span>
                    <button>View Details</button>
                </div>
            </div>

            <div class="package-card" data-category="japan">
                <img src="tour2.jpg" alt="Tour">
                <div class="package-info">
                    <h3>Japan Explorer</h3>
                    <p>7 Days / 6 Nights</p>
                    <span class="price">₱49,999</span>
                    <button>View Details</button>
                </div>
            </div>

            <div class="package-card" data-category="japan">
                <img src="tour3.jpg" alt="Tour">
                <div class="package-info">
                    <h3>Japan Advanced Tour</h3>
                    <p>9 Days / 8 Nights</p>
                    <span class="price">₱69,999</span>
                    <button>View Details</button>
                </div>
            </div>

        </div>

    </main>





    <!-- Bottom Navigation -->
    <nav class="bottom-nav">
        <a class="active" data-tab="home">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a data-tab="bookings">
            <i class="fas fa-calendar-check"></i>
            <span>Bookings</span>
        </a>
        <a data-tab="profile">
            <i class="fas fa-user"></i>
            <span>Profile</span>
        </a>
    </nav>



</body>


<script>
    const tabButtons = document.querySelectorAll('.tab-btn');
    const packageCards = document.querySelectorAll('.package-card');

    tabButtons.forEach(btn => {
        btn.addEventListener('click', () => {

            // Remove active class from all tabs
            tabButtons.forEach(b => b.classList.remove('active'));

            // Activate clicked tab
            btn.classList.add('active');

            const tab = btn.dataset.tab;

            packageCards.forEach(card => {
                if (tab === 'all') {
                    card.style.display = 'block';
                } else {
                    card.style.display = card.dataset.category === tab ? 'block' : 'none';
                }
            });

        });
    });
</script>



<script>
    const tabs = document.querySelectorAll('.bottom-nav a');

    tabs.forEach(tab => {
        tab.addEventListener('click', (e) => {
            e.preventDefault();

            // Remove active class from all tabs
            tabs.forEach(t => t.classList.remove('active'));

            // Add active class to clicked tab
            tab.classList.add('active');

            const selected = tab.dataset.tab;
            console.log('Selected tab:', selected);

            // Optional: switch content based on tab
            // Example:
            // showTabContent(selected);
        });
    });
</script>


<script>
    const sidebar = document.getElementById('mobileSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const toggleBtn = document.querySelector('.sidebar-toggle');

    // Open sidebar
    toggleBtn.addEventListener('click', () => {
        sidebar.classList.add('open');
        overlay.classList.add('active');
    });

    // Close sidebar when clicking on overlay
    overlay.addEventListener('click', closeSidebar);

    // Close sidebar when clicking outside of it
    document.addEventListener('click', (e) => {
        if (!sidebar.contains(e.target) && !toggleBtn.contains(e.target) && sidebar.classList.contains('open')) {
            closeSidebar();
        }
    });

    // Function to close sidebar
    function closeSidebar() {
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
    }
</script>


<script src="../mobile/js/theme-toggle.js"></script>

</html>