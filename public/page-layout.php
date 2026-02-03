<?php
// session_start();
// // Check login
// if (!isset($_SESSION['user_id'])) {
//     header('Location: login.php');
//     exit;
// }
?>
<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

    <!-- Initial Links -->
    <?php include '../../public/includes/initial-links.php'; ?>

</head>

<body>
    <div class="dashboard-container">

        <!-- Sidebar -->
        <?php include '../../public/includes/admin-sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main-wrapper">

            <!-- Header -->
            <?php include '../../public/includes/header.php'; ?>

            <!-- Main Content Area -->
            <main class="main-content">

                <div class="content-wrapper">

                </div>

            </main>

        </div>
    </div>
</body>



<!-- Important Imports (JS) -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script src="../../public/gen-js/dashboard-structure-defaults.js"></script>


</html>