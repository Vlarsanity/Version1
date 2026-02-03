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
    <link href="../../dist/styles.css?v=<?= time(); ?>" rel="stylesheet">
    <link href="../../public/assets/css/dashboard-structure.css?v=<?= time(); ?>" rel="stylesheet">

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer">


    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            <main id="app" class="main-content">

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