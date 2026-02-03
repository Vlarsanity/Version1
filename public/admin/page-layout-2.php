<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Operating Status</title>

    <!-- Initial Links -->

    <!-- 공통 스타일 -->
    <link rel="shortcut icon" href="../../favicon.ico" />

    <!-- Root Styles (Always on Top) and Components Styles -->
    <link href="../../public/assets/css/root.css?v=<?= time(); ?>" rel="stylesheet">

    <link href="../../public/assets/css/flatpckr-design.css?v=<?= time(); ?>" rel="stylesheet">
    <link href="../../public/assets/css/apexchart-design.css?v=<?= time(); ?>" rel="stylesheet">


    <?php include '../../public/includes/initial-links.php'; ?>


    <script src="https://cdn.jsdelivr.net/npm/apexcharts@latest"></script>


    <!-- Page Specifics -->
    <link href="../../public/assets/css/dashboard.css?v=<?= time(); ?>" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>


</head>

<?php
include '../functions/csrf.php';
?>

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

                    <div class="content-header">

                        <!-- Left: Page title / breadcrumb -->
                        <div class="content-header-left">
                            <h1 class="page-title">Dashboard</h1>
                        </div>

                        <!-- Right: Actions -->
                        <div class="content-header-right">
                            <div class="date-pill">
                                <i class="fas fa-calendar-alt"></i>
                                <span id="todayDate"></span>
                            </div>
                        </div>

                    </div>


                    <div class="content-body">

                        

                    </div>
                </div>

            </main>
        </div>
    </div>
</body>











<?php include '../../public/includes/initial-js.php'; ?>


</html>