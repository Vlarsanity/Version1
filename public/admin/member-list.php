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

    <!-- Tabulator CSS (REQUIRED!) -->
    <link href="https://unpkg.com/tabulator-tables@6.2.1/dist/css/tabulator.min.css" rel="stylesheet">

    <!-- Tabulator JS -->
    <script src="https://unpkg.com/tabulator-tables@6.2.1/dist/js/tabulator.min.js"></script>


    <?php include '../../public/includes/initial-links.php'; ?>


    <!-- Page Specifics -->

    <!-- Main Script for ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@latest"></script>



    <link href="../../public/assets/css/member-list.css?v=<?= time(); ?>" rel="stylesheet">
    <link href="../../public/assets/css/general-full-table.css?v=<?= time(); ?>" rel="stylesheet">
    <link href="../../public/assets/css/tabulator.css?v=<?= time(); ?>" rel="stylesheet">


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

                <div class="content-wrapper member-list-main-wrapper">

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

                            <div class="table-header-actions">

                                <div class="table-actions-left">
                                    <div class="table-search-wrapper">
                                        <i class="fas fa-search search-icon"></i>

                                        <input
                                            type="text"
                                            id="tableSearchInput"
                                            class="table-search-input"
                                            placeholder="Search customers..."
                                            aria-label="Search table">

                                        <button
                                            type="button"
                                            id="tableSearchClear"
                                            class="search-clear-btn"
                                            aria-label="Clear search">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="table-actions-right">

                                    <!-- Filter Dropdown -->
                                    <div class="action-dropdown">
                                        <button class="icon-action-btn" data-dropdown="filter" aria-label="Filter" title="Filter">
                                            <i class="fas fa-filter"></i>
                                            <span class="filter-badge">0</span>
                                        </button>

                                        <div class="dropdown-menu">
                                            <button class="dropdown-item" data-filter="Active">
                                                Active
                                            </button>
                                            <button class="dropdown-item" data-filter="Pending">
                                                Pending
                                            </button>
                                            <button class="dropdown-item" data-filter="Suspended">
                                                Suspended
                                            </button>
                                            <hr>
                                            <button class="dropdown-item danger" data-filter="clear">
                                                <i class="fas fa-times"></i> Clear Filters
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Export Dropdown - NEW ADDITION -->
                                    <div class="action-dropdown">
                                        <button class="icon-action-btn" data-dropdown="export" aria-label="Export" title="Export">
                                            <i class="fas fa-download"></i>
                                        </button>

                                        <div class="dropdown-menu">
                                            <button class="dropdown-item" onclick="window.exportTable.toCSV()">
                                                <i class="fas fa-file-csv"></i> Export to CSV
                                            </button>
                                            <button class="dropdown-item" onclick="window.exportTable.toJSON()">
                                                <i class="fas fa-file-code"></i> Export to JSON
                                            </button>
                                            <button class="dropdown-item" onclick="window.exportTable.toXLSX()">
                                                <i class="fas fa-file-excel"></i> Export to Excel
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Refresh Button - NEW ADDITION -->
                                    <button class="icon-action-btn" onclick="window.tableUtils.refreshTable()" title="Refresh" aria-label="Refresh">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>

                                </div>

                            </div>

                            <!-- Loading State - NEW -->
                            <div class="table-loading" id="tableLoading" style="display: none;">
                                <i class="fas fa-spinner"></i>
                                <p style="margin-top: 12px;">Loading data...</p>
                            </div>


                            <div class="table-container" id="customerTable"></div>


                            <div id="branchHoverCard" class="branch-hover-card"></div>


                            <div class="table-footer">
                                <div class="table-footer-left">
                                    <div class="rows-per-page">
                                        <span class="rows-label">Rows per page:</span>

                                        <div class="rows-dropdown">
                                            <select id="pageSizeSelector" class="rows-select-btn">
                                                <option value="13">13</option>
                                                <option value="20">20</option>
                                                <option value="25">25</option>
                                                <option value="50">50</option>
                                                <option value="all">All</option>
                                            </select>
                                            <button id="resetPageSize" class="rows-reset-btn" title="Reset to default">
                                                Clear
                                            </button>
                                        </div>

                                    </div>
                                    <div class="table-info">
                                        <span class="info-text" id="tableInfo">Showing 1-13 of 0 results</span>
                                    </div>
                                </div>

                                <div class="table-footer-right">
                                    <div class="pagination" id="tablePagination"></div>
                                </div>

                            </div>

                        </div>
                    </div>

            </main>

        </div>
    </div>
</body>

<?php include '../../public/includes/initial-js.php'; ?>


<script>
    document.addEventListener("click", function(e) {
        const dropdowns = document.querySelectorAll(".action-dropdown");

        dropdowns.forEach(dropdown => {
            const button = dropdown.querySelector(".icon-action-btn");

            // Toggle dropdown
            if (button.contains(e.target)) {
                dropdown.classList.toggle("open");
            }
            // Close if clicked outside
            else if (!dropdown.contains(e.target)) {
                dropdown.classList.remove("open");
            }
        });
    });
</script>


<!-- 2. Lookup data -->
<script src="../../public/gen-js/data-js/table-lookup-data.js"></script>

<!-- 3. Customer data -->
<script src="../../public/gen-js/data-js/customer-data.js"></script>

<!-- 4. Table configuration -->
<script>
    window.TABLE_CONFIG_OVERRIDE = {
        tableName: "customerTable",
        tableId: "customer",

        pagination: {
            defaultSize: 13,
            availableSizes: [13, 20, 25, 50, "all"]
        },

        features: {
            selectable: true,
            search: true,
            pagination: true,
            export: true,
            dynamicRowFitting: true,
            hoverCards: true
        },

        elements: {
            table: "customerTable",
            searchInput: "tableSearchInput",
            searchClear: "tableSearchClear",
            pageSizeSelector: "pageSizeSelector",
            resetPageSize: "resetPageSize",
            tableInfo: "tableInfo",
            pagination: "tablePagination",
            hoverCard: "branchHoverCard"
        }
    };

    window.ROW_ACTIONS_OVERRIDE = {
        enabled: true,
        actionType: "modal",

        modal: {
            modalId: "customerModal",
            onOpen: (rowData) => {
                console.log('Row clicked:', rowData);
                // Your modal logic
            }
        }
    };
</script>

<!-- 5. Your table script -->
<script src="../../public/gen-js/tabulator-init.js"></script>

<!-- 6. Filter dropdown functionality -->
<script src="../../../public/gen-js/data-js/table-filter-dropdown.js"></script>


</html>