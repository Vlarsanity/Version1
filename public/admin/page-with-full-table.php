<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member List</title>

    <!-- Initial Links -->

    <!-- 공통 스타일 -->
    <link rel="shortcut icon" href="../../favicon.ico" />

    <!-- Root Styles (Always on Top) and Components Styles -->
    <link href="../../public/assets/css/root.css?v=<?= time(); ?>" rel="stylesheet">

    <link href="../../public/assets/css/flatpckr-design.css?v=<?= time(); ?>" rel="stylesheet">
    <link href="../../public/assets/css/apexchart-design.css?v=<?= time(); ?>" rel="stylesheet">


    <?php include '../../public/includes/initial-links.php'; ?>




    <!-- Page Specifics -->

    <!-- Main Script for ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@latest"></script>

    <link href="../../public/assets/css/general-full-table.css?v=<?= time(); ?>" rel="stylesheet">
    <link href="../../public/assets/css/member-list.css?v=<?= time(); ?>" rel="stylesheet">


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

                    <div class="content-header member-list-main-header">

                        <!-- Left: Page title / breadcrumb -->
                        <div class="content-header-left">
                            <h1 class="page-title">Member List</h1>
                        </div>

                        <!-- Right: Actions -->
                        <div class="content-header-right">

                        </div>

                    </div>

                    <div class="content-body member-list-main-body">

                        <div class="full-table-wrapper">

                            <!-- Table Header Actions -->
                            <div class="table-header-actions">

                                <div class="table-actions-left">
                                    <!-- <div class="bulk-action-dropdown"> 
                                        <button class="bulk-action-btn">
                                            <span>Bulk Action</span>
                                            <i class="fas fa-chevron-down"></i>
                                        </button>
                                    </div>
                                    <button class="apply-btn">Apply</button> -->
                                </div>

                                <div class="table-actions-right">
                                    <button class="icon-action-btn" aria-label="Search">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    <button class="icon-action-btn" aria-label="Filter">
                                        <i class="fas fa-filter"></i>
                                        <span class="filter-badge">2</span>
                                    </button>
                                    <button class="icon-action-btn" aria-label="Settings">
                                        <i class="fas fa-cog"></i>
                                    </button>
                                </div>
                                
                            </div>

                            <!-- Table Container -->
                            <div class="table-container">

                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th class="checkbox-col">
                                                <input type="checkbox" id="selectAll" aria-label="Select all">
                                            </th>
                                            <th class="customer-col">Customer</th>
                                            <th class="email-col">Email</th>
                                            <th class="phone-col">Phone</th>
                                            <th class="company-col">Company</th>
                                            <th class="payment-col">Payment Methods</th>
                                            <th class="joined-col">Joined</th>
                                            <th class="status-col">Status</th>
                                            <th class="actions-col">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <!-- Row 1 -->
                                        <tr>
                                            <td class="checkbox-col">
                                                <input type="checkbox" aria-label="Select Bobby Gilbert">
                                            </td>
                                            <td class="customer-col">
                                                <div class="customer-info">
                                                    <div class="customer-avatar" style="background-color: #6366f1;">BG</div>
                                                    <span class="customer-name">Bobby Gilbert</span>
                                                </div>
                                            </td>
                                            <td class="email-col">bobby@softnio.com</td>
                                            <td class="phone-col">+342 675-6578</td>
                                            <td class="company-col">Softnio</td>
                                            <td class="payment-col">
                                                <div class="payment-method">
                                                    <span class="payment-icon visa">
                                                        <i class="fab fa-cc-visa"></i>
                                                    </span>
                                                    <span class="payment-number">**** 1955</span>
                                                </div>
                                            </td>
                                            <td class="joined-col">12 Dec 2021, 12:12 am</td>
                                            <td class="status-col">
                                                <span class="status-badge active">Active</span>
                                            </td>
                                            <td class="actions-col">
                                                <button class="actions-btn" aria-label="More actions">
                                                    <i class="fas fa-ellipsis-h"></i>
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- Row 2 -->
                                        <tr>
                                            <td class="checkbox-col">
                                                <input type="checkbox" aria-label="Select Olivia Poulsen">
                                            </td>
                                            <td class="customer-col">
                                                <div class="customer-info">
                                                    <div class="customer-avatar" style="background-color: #ef4444;">OP</div>
                                                    <span class="customer-name">Olivia Poulsen</span>
                                                </div>
                                            </td>
                                            <td class="email-col">olivia@apple.com</td>
                                            <td class="phone-col">+782 332-8328</td>
                                            <td class="company-col">Apple</td>
                                            <td class="payment-col">
                                                <div class="payment-method">
                                                    <span class="payment-icon mastercard">
                                                        <i class="fab fa-cc-mastercard"></i>
                                                    </span>
                                                    <span class="payment-number">**** 7473</span>
                                                </div>
                                            </td>
                                            <td class="joined-col">08 Dec 2021, 04:03 am</td>
                                            <td class="status-col">
                                                <span class="status-badge active">Active</span>
                                            </td>
                                            <td class="actions-col">
                                                <button class="actions-btn" aria-label="More actions">
                                                    <i class="fas fa-ellipsis-h"></i>
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- Row 3 -->
                                        <tr>
                                            <td class="checkbox-col">
                                                <input type="checkbox" aria-label="Select Heather Marshall">
                                            </td>
                                            <td class="customer-col">
                                                <div class="customer-info">
                                                    <img src="https://i.pravatar.cc/150?img=1" alt="Heather Marshall" class="customer-avatar-img">
                                                    <span class="customer-name">Heather Marshall</span>
                                                </div>
                                            </td>
                                            <td class="email-col">marshall@reaktit.com</td>
                                            <td class="phone-col">+342 545-5639</td>
                                            <td class="company-col">Reaktit</td>
                                            <td class="payment-col">
                                                <div class="payment-method">
                                                    <span class="payment-icon amex">
                                                        <i class="fab fa-cc-amex"></i>
                                                    </span>
                                                    <span class="payment-number">**** 4355</span>
                                                </div>
                                            </td>
                                            <td class="joined-col">02 Dec 2021, 02:34 am</td>
                                            <td class="status-col">
                                                <span class="status-badge inactive">Inactive</span>
                                            </td>
                                            <td class="actions-col">
                                                <button class="actions-btn" aria-label="More actions">
                                                    <i class="fas fa-ellipsis-h"></i>
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- Row 4 -->
                                        <tr>
                                            <td class="checkbox-col">
                                                <input type="checkbox" aria-label="Select Benjamin Harris">
                                            </td>
                                            <td class="customer-col">
                                                <div class="customer-info">
                                                    <div class="customer-avatar" style="background-color: #8b5cf6;">BH</div>
                                                    <span class="customer-name">Benjamin Harris</span>
                                                </div>
                                            </td>
                                            <td class="email-col">info@mediavest.com</td>
                                            <td class="phone-col">+342 675-6578</td>
                                            <td class="company-col">MediaVest</td>
                                            <td class="payment-col">
                                                <div class="payment-method">
                                                    <span class="payment-icon visa">
                                                        <i class="fab fa-cc-visa"></i>
                                                    </span>
                                                    <span class="payment-number">**** 3472</span>
                                                </div>
                                            </td>
                                            <td class="joined-col">29 Nov 2021, 03:19 am</td>
                                            <td class="status-col">
                                                <span class="status-badge active">Active</span>
                                            </td>
                                            <td class="actions-col">
                                                <button class="actions-btn" aria-label="More actions">
                                                    <i class="fas fa-ellipsis-h"></i>
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- Row 5 -->
                                        <tr>
                                            <td class="checkbox-col">
                                                <input type="checkbox" aria-label="Select Joshua Kennedy">
                                            </td>
                                            <td class="customer-col">
                                                <div class="customer-info">
                                                    <div class="customer-avatar" style="background-color: #f59e0b;">JK</div>
                                                    <span class="customer-name">Joshua Kennedy</span>
                                                </div>
                                            </td>
                                            <td class="email-col">joshua@softnio.com</td>
                                            <td class="phone-col">+323 345-8676</td>
                                            <td class="company-col">Softnio</td>
                                            <td class="payment-col">
                                                <div class="payment-method">
                                                    <span class="payment-icon visa">
                                                        <i class="fab fa-cc-visa"></i>
                                                    </span>
                                                    <span class="payment-number">**** 9878</span>
                                                </div>
                                            </td>
                                            <td class="joined-col">24 Nov 2021, 04:21 am</td>
                                            <td class="status-col">
                                                <span class="status-badge active">Active</span>
                                            </td>
                                            <td class="actions-col">
                                                <button class="actions-btn" aria-label="More actions">
                                                    <i class="fas fa-ellipsis-h"></i>
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- Row 6 -->
                                        <tr>
                                            <td class="checkbox-col">
                                                <input type="checkbox" aria-label="Select Justine Bauwens">
                                            </td>
                                            <td class="customer-col">
                                                <div class="customer-info">
                                                    <img src="https://i.pravatar.cc/150?img=5" alt="Justine Bauwens" class="customer-avatar-img">
                                                    <span class="customer-name">Justine Bauwens</span>
                                                </div>
                                            </td>
                                            <td class="email-col">bauwens@kline.com</td>
                                            <td class="phone-col">+657 879-3214</td>
                                            <td class="company-col">Kline</td>
                                            <td class="payment-col">
                                                <div class="payment-method">
                                                    <span class="payment-icon mastercard">
                                                        <i class="fab fa-cc-mastercard"></i>
                                                    </span>
                                                    <span class="payment-number">**** 7657</span>
                                                </div>
                                            </td>
                                            <td class="joined-col">19 Nov 2021, 09:56 am</td>
                                            <td class="status-col">
                                                <span class="status-badge active">Active</span>
                                            </td>
                                            <td class="actions-col">
                                                <button class="actions-btn" aria-label="More actions">
                                                    <i class="fas fa-ellipsis-h"></i>
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- Row 7 -->
                                        <tr>
                                            <td class="checkbox-col">
                                                <input type="checkbox" aria-label="Select Ethan Hunter">
                                            </td>
                                            <td class="customer-col">
                                                <div class="customer-info">
                                                    <div class="customer-avatar" style="background-color: #a855f7;">EH</div>
                                                    <span class="customer-name">Ethan Hunter</span>
                                                </div>
                                            </td>
                                            <td class="email-col">ethan@bergerpaints.com</td>
                                            <td class="phone-col">+435 675-2345</td>
                                            <td class="company-col">Berger Paints</td>
                                            <td class="payment-col">
                                                <div class="payment-method">
                                                    <span class="payment-icon visa">
                                                        <i class="fab fa-cc-visa"></i>
                                                    </span>
                                                    <span class="payment-number">**** 5435</span>
                                                </div>
                                            </td>
                                            <td class="joined-col">13 Nov 2021, 05:45 am</td>
                                            <td class="status-col">
                                                <span class="status-badge active">Active</span>
                                            </td>
                                            <td class="actions-col">
                                                <button class="actions-btn" aria-label="More actions">
                                                    <i class="fas fa-ellipsis-h"></i>
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- Row 7 -->
                                        <tr>
                                            <td class="checkbox-col">
                                                <input type="checkbox" aria-label="Select Ethan Hunter">
                                            </td>
                                            <td class="customer-col">
                                                <div class="customer-info">
                                                    <div class="customer-avatar" style="background-color: #a855f7;">EH</div>
                                                    <span class="customer-name">Ethan Hunter</span>
                                                </div>
                                            </td>
                                            <td class="email-col">ethan@bergerpaints.com</td>
                                            <td class="phone-col">+435 675-2345</td>
                                            <td class="company-col">Berger Paints</td>
                                            <td class="payment-col">
                                                <div class="payment-method">
                                                    <span class="payment-icon visa">
                                                        <i class="fab fa-cc-visa"></i>
                                                    </span>
                                                    <span class="payment-number">**** 5435</span>
                                                </div>
                                            </td>
                                            <td class="joined-col">13 Nov 2021, 05:45 am</td>
                                            <td class="status-col">
                                                <span class="status-badge active">Active</span>
                                            </td>
                                            <td class="actions-col">
                                                <button class="actions-btn" aria-label="More actions">
                                                    <i class="fas fa-ellipsis-h"></i>
                                                </button>
                                            </td>
                                        </tr>

                                    </tbody>
                                </table>

                            </div>

                            <!-- Table Footer -->
                            <div class="table-footer">
                                <div class="table-footer-left">
                                    <div class="rows-per-page">
                                        <span class="rows-label">Rows per page:</span>
                                        <div class="rows-dropdown">
                                            <button class="rows-select-btn">
                                                <span>10</span>
                                                <i class="fas fa-chevron-down"></i>
                                            </button>
                                            <!-- Dropdown menu can be added here -->
                                        </div>
                                    </div>
                                    <div class="table-info">
                                        <span class="info-text">Showing 1-10 of 245 results</span>
                                    </div>
                                </div>

                                <div class="table-footer-right">
                                    <div class="pagination">
                                        <button class="pagination-btn prev-btn" aria-label="Previous page" disabled>
                                            <i class="fas fa-chevron-left"></i>
                                        </button>

                                        <div class="pagination-numbers">
                                            <button class="pagination-number active">1</button>
                                            <button class="pagination-number">2</button>
                                            <button class="pagination-number">3</button>
                                            <button class="pagination-number">4</button>
                                            <span class="pagination-ellipsis">...</span>
                                            <button class="pagination-number">25</button>
                                        </div>

                                        <button class="pagination-btn next-btn" aria-label="Next page">
                                            <i class="fas fa-chevron-right"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                    
                        </div>
                        
                    </div>

                </div>

            </main>

        </div>
    </div>
</body>


<?php include '../../public/includes/initial-js.php'; ?>


</html>