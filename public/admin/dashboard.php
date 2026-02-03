<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Operating Status</title>

    <!-- 공통 스타일 -->
    <link rel="shortcut icon" href="../../favicon.ico" />

    <!-- Root Styles (Always on Top) and Components Styles -->
    <link href="../../public/assets/css/root.css?v=<?= time(); ?>" rel="stylesheet">

    <!-- Specific Components CSS -->
    <link href="../../public/assets/css/flatpckr-design.css?v=<?= time(); ?>" rel="stylesheet">
    <link href="../../public/assets/css/apexchart-design.css?v=<?= time(); ?>" rel="stylesheet">

    <!-- ApexCharts JS -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@latest"></script>


    <!-- Initial Links -->
    <?php include '../../public/includes/initial-links.php'; ?>


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

                        <!-- Card Divs Wrapper -->
                        <div class="count-card-wrapper">

                            <div class="count-card">

                                <div class="card-header">
                                    <div class="card-header-left">
                                        <h3>Reservation Status </h3>
                                    </div>

                                    <div class="card-header-right">
                                        <a href="#" class="card-header-link">
                                            See more <i class="fas fa-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>


                                <div class="card-body">

                                    <!-- Card 1 -->
                                    <div class="mini-count-card status-info">
                                        <div class="card-left">
                                            <div class="trend up">
                                                <i class="fas fa-arrow-up"></i> 234%
                                            </div>
                                            <div class="count">1,234</div>
                                            <div class="label">Advance Payment Deposit</div>
                                        </div>

                                        <div class="card-right">
                                            <!-- <div class="indicator">58</div> -->
                                        </div>
                                    </div>

                                    <!-- Card 2 -->
                                    <div class="mini-count-card status-success">
                                        <div class="card-left">
                                            <div class="trend down">
                                                <i class="fas fa-arrow-down"></i> 71%
                                            </div>
                                            <div class="count">256</div>
                                            <div class="label">Balance Payment</div>
                                        </div>
                                        <div class="card-right">
                                            <!-- <div class="indicator">62</div> -->
                                        </div>
                                    </div>

                                </div>

                            </div>

                            <div class="count-card">
                                <div class="card-header">
                                    <div class="card-header-left">
                                        <h3>Inquiry Status</h3>
                                    </div>

                                    <div class="card-header-right">
                                        <a href="#" class="card-header-link">
                                            See more <i class="fas fa-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>



                                <div class="card-body">

                                    <!-- Card 1 -->
                                    <div class="mini-count-card status-danger">
                                        <div class="card-left">
                                            <div class="trend up">
                                                <i class="fas fa-arrow-up"></i> 234%
                                            </div>
                                            <div class="count">1,234</div>
                                            <div class="label">Unanswered</div>
                                        </div>
                                        <div class="card-right">
                                            <!-- <div class="indicator">58</div> -->
                                        </div>
                                    </div>

                                    <!-- Card 2 -->
                                    <div class="mini-count-card status-success">
                                        <div class="card-left">
                                            <div class="trend down">
                                                <i class="fas fa-arrow-down"></i> 71%
                                            </div>
                                            <div class="count">256</div>
                                            <div class="label">Processing</div>
                                        </div>
                                        <div class="card-right">
                                            <!-- <div class="indicator">62</div> -->
                                        </div>
                                    </div>

                                </div>

                            </div>

                        </div>

                        <!-- Tabs Navigation -->
                        <div class="count-card-tabs">
                            <ul class="tabs-list">
                                <li>
                                    <button class="tab-btn active" data-tab="tab1">
                                        <i class="fas fa-route"></i>
                                        <span>Travel Itinerary</span>
                                    </button>
                                </li>

                                <li>
                                    <button class="tab-btn" data-tab="tab2">
                                        <i class="fas fa-chart-line"></i>
                                        <span>Sales Stats</span>
                                    </button>
                                </li>
                                <li>
                                    <button class="tab-btn" data-tab="tab3">
                                        <i class="fas fa-box"></i>
                                        <span>Sales Stats by Product</span>
                                    </button>
                                </li>

                            </ul>
                        </div>

                        <!-- Card-body -->
                        <div class="count-card-wrapper-body">

                            <div class="count-card-tab-content">
                                
                                <div id="tab1" class="tab-content">
                                    <div class="table-wrapper itinerary-table">

                                        <div class="table-wrapper-header">
                                            <div class="table-wrapper-header-left">
                                                <div>
                                                    <h2 class="table-wrapper-title">Today's Itinerary List</h2>
                                                </div>
                                            </div>

                                            <div class="table-wrapper-header-right">
                                                <!-- actions / buttons -->
                                            </div>
                                        </div>

                                        <!-- Body: table container -->
                                        <div class="table-wrapper-body">
                                            <table class="table-card-table">
                                                <thead>
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Email</th>
                                                        <th>Status</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    <tr>
                                                        <td>John Doe</td>
                                                        <td>john@example.com</td>
                                                        <td>
                                                            <span class="table-card-badge success">Active</span>
                                                        </td>
                                                        <td>
                                                            <button class="table-card-btn">Edit</button>
                                                            <button class="table-card-btn danger">Delete</button>
                                                        </td>
                                                    </tr>

                                                    <!-- repeat rows -->
                                                </tbody>
                                            </table>
                                        </div>

                                        <!-- Optional Table Card Footer -->
                                        <div class="table-card-footer">
                                            <span class="table-card-info">Showing 1-10 of 100 results</span>
                                            <div class="table-card-pagination itinerary-footer-pagination">
                                                <button class="table-card-btn">Previous</button>
                                                <button class="table-card-btn">Next</button>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <!-- Sales Stats (Default Tab) -->
                                <div id="tab2" class="tab-content hidden">

                                    <div class="table-wrapper">

                                        <!-- Header -->
                                        <div class="table-wrapper-header">
                                            <div class="table-wrapper-header-left">
                                                <div>
                                                    <h2 class="table-wrapper-title">Sales Statistics</h2>
                                                </div>
                                            </div>

                                            <div class="table-wrapper-header-right">

                                            </div>

                                        </div>

                                        <!-- Body: table container -->
                                        <div class="table-wrapper-body sales-statistics-table">

                                            <!-- Header (controls: calendar / buttons) -->
                                            <div class="wrapper-body-header">

                                                <!-- VERTICAL CHART CONTROLS -->
                                                <div class="wrapper-body-header-left">

                                                    <!-- Tabs -->
                                                    <div class="tab-buttons second-layer">
                                                        <button class="tab-btn" data-lan-eng="Daily" data-tab="daily" data-chart="vertical">일간</button>
                                                        
                                                        <button class="tab-btn" data-lan-eng="Weekly" data-tab="weekly" data-chart="vertical">주간</button>
                                                        <button class="tab-btn" data-lan-eng="Monthly" data-tab="monthly" data-chart="vertical">월간</button>
                                                        <button class="tab-btn" data-lan-eng="Annual" data-tab="annual" data-chart="vertical">연간</button>
                                                    </div>

                                                    <!-- Divider -->
                                                    <div class="vertical-divider"></div>

                                                    <!-- VERTICAL CHART DATE INPUTS -->
                                                    <div class="calendar-input">
                                                        <input type="text" class="flatpickr-single" id="date-specific-chart-vertical" placeholder="Select Specific Date" />
                                                        <span class="calendar-icon"><i class="fas fa-calendar-alt"></i></span>
                                                    </div>

                                                    <div class="calendar-range">
                                                        <input type="text" class="flatpickr-range" id="date-range-chart-vertical" placeholder="Select Date Range" />
                                                        <span class="calendar-icon"><i class="fas fa-calendar-alt"></i></span>
                                                    </div>

                                                    <!-- Clear Button -->
                                                    <button class="btn-clear" id="clear-dates-vertical"> <i class="fas fa-times"></i> Clear All </button>

                                                </div>

                                                <div class="wrapper-body-header-right">

                                                </div>

                                            </div>

                                            <!-- Main Body -->
                                            <div class="wrapper-main-body">

                                                <div class="main-body">

                                                    <!-- 20% -->
                                                    <div class="sales-summary-wrapper">
                                                        <div class="sales-summary">
                                                            <h3 class="table-wrapper-subtitle">Total Number of Sales</h3>
                                                            <span class="sales-summary-value">1,000</span>
                                                        </div>
                                                    </div>

                                                    <!-- 80% -->
                                                    <div class="sales-chart-wrapper">
                                                        <div id="sales-chart-vertical"></div>
                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                                <!-- Sales Stats by Product (Default Tab) -->
                                <div id="tab3" class="tab-content hidden">

                                    <div class="table-wrapper">

                                        <!-- Header -->
                                        <div class="table-wrapper-header">
                                            <div class="table-wrapper-header-left">
                                                <div>
                                                    <h2 class="table-wrapper-title">Sales Statistics by Product</h2>
                                                </div>
                                            </div>

                                            <div class="table-wrapper-header-right">

                                            </div>

                                        </div>

                                        <!-- Body: table container -->
                                        <div class="table-wrapper-body sales-statistics-table">

                                            <!-- Header (controls: calendar / buttons) -->
                                            <div class="wrapper-body-header">

                                                <!-- HORIZONTAL CHART CONTROLS -->
                                                <div class="wrapper-body-header-left">

                                                    <!-- Tabs -->
                                                    <div class="tab-buttons second-layer">
                                                        <button class="tab-btn" data-lan-eng="Daily" data-tab="daily" data-chart="horizontal">일간</button>
                                                        <button class="tab-btn" data-lan-eng="Weekly" data-tab="weekly" data-chart="horizontal">주간</button>
                                                        <button class="tab-btn" data-lan-eng="Monthly" data-tab="monthly" data-chart="horizontal">월간</button>
                                                        <button class="tab-btn" data-lan-eng="Annual" data-tab="annual" data-chart="horizontal">연간</button>
                                                    </div>

                                                    <!-- Divider -->
                                                    <div class="vertical-divider"></div>

                                                    <!-- HORIZONTAL CHART DATE INPUTS -->
                                                    <div class="calendar-input">
                                                        <input type="text" class="flatpickr-single" id="date-specific-chart-horizontal" placeholder="Select Specific Date" />
                                                        <span class="calendar-icon"><i class="fas fa-calendar-alt"></i></span>
                                                    </div>

                                                    <div class="calendar-range">
                                                        <input type="text" class="flatpickr-range" id="date-range-chart-horizontal" placeholder="Select Date Range" />
                                                        <span class="calendar-icon"><i class="fas fa-calendar-alt"></i></span>
                                                    </div>

                                                    <!-- Clear Button -->
                                                    <button class="btn-clear" id="clear-dates-horizontal"> <i class="fas fa-times"></i> Clear All </button>

                                                </div>

                                                <div class="wrapper-body-header-right">

                                                </div>

                                            </div>

                                            <!-- Main Body -->
                                            <div class="wrapper-main-body">

                                                <div class="main-body-left">

                                                    <!-- 20% – Sales Summary -->
                                                    <div class="body-left-summary-wrapper">
                                                        <div class="sales-summary">
                                                            <h3 class="table-wrapper-subtitle">Total Number of Sales</h3>
                                                            <span class="sales-summary-value">1,000</span>
                                                        </div>
                                                    </div>

                                                    <!-- 80% – Sales Chart -->
                                                    <div class="body-left-chart-wrapper">
                                                        <div id="sales-chart-horizontal"></div>
                                                    </div>

                                                </div>

                                                <div class="main-body-right">

                                                    <div class="sales-statistics-content">

                                                        <!-- Body: table container -->
                                                        <div class="mbr-table-wrapper-body">

                                                            <table class="mbr-table-card-table">
                                                                
                                                                <colgroup>
                                                                    <col style="width:60px;"> <!-- No -->
                                                                    <col> <!-- Product Name -->
                                                                    <col style="width:120px;"> <!-- Views -->
                                                                    <col style="width:120px;"> <!-- Reservations -->
                                                                    <col style="width:120px;"> <!-- Reservation Rate -->
                                                                    <col style="width:160px;"> <!-- Sales Amount -->
                                                                </colgroup>

                                                                <thead>
                                                                    <tr>
                                                                        <th>No</th>
                                                                        <th data-lan-eng="Product Name">상품명</th>
                                                                        <th data-lan-eng="Views">조회수</th>
                                                                        <th data-lan-eng="Number of reservations">예약건수</th>
                                                                        <th data-lan-eng="Reservation rate">예약률</th>
                                                                        <th data-lan-eng="Sales amount">판매액</th>
                                                                    </tr>
                                                                </thead>

                                                                <tbody>
                                                                    <tr>
                                                                        <td>1</td>
                                                                        <td>서울 벚꽃 명소 집중 탐방 5박 6일 패키지 – 전일정 가이드</td>
                                                                        <td>2,689</td>
                                                                        <td>200</td>
                                                                        <td>28.24%</td>
                                                                        <td>₩2,000,000</td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td>2</td>
                                                                        <td>한국 베이직 투어 4박 5일 – 남이섬, 에버랜드 포함</td>
                                                                        <td>2,397</td>
                                                                        <td>160</td>
                                                                        <td>11.76%</td>
                                                                        <td>₩800,000</td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td>3</td>
                                                                        <td>단풍 투어 & 설악산 케이블카 4박 5일</td>
                                                                        <td>3,950</td>
                                                                        <td>150</td>
                                                                        <td>8.24%</td>
                                                                        <td>₩2,500,000</td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td>4</td>
                                                                        <td>겨울 패키지 4박 5일 – 에버랜드 & 스키 옵션 포함</td>
                                                                        <td>2,782</td>
                                                                        <td>100</td>
                                                                        <td>7.06%</td>
                                                                        <td>₩2,000,000</td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td>5</td>
                                                                        <td>전주·군산 레트로 투어 3박 4일 – 한옥마을 + 철길마을</td>
                                                                        <td>398</td>
                                                                        <td>80</td>
                                                                        <td>5.88%</td>
                                                                        <td>₩400,000</td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td>6</td>
                                                                        <td>남해 힐링 투어 6박 7일 – 부산·여수·거제 포함</td>
                                                                        <td>2,691</td>
                                                                        <td>70</td>
                                                                        <td>5.01%</td>
                                                                        <td>₩490,000</td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td>7</td>
                                                                        <td>부산 & 경주 핵심 일주 3박 4일 – 해운대, 불국사 포함</td>
                                                                        <td>3,876</td>
                                                                        <td>50</td>
                                                                        <td>4.71%</td>
                                                                        <td>₩190,000</td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td>8</td>
                                                                        <td>제주도 환상 일주 4박 5일 – 우도 & 성산일출봉 포함</td>
                                                                        <td>1,299</td>
                                                                        <td>48</td>
                                                                        <td>3.53%</td>
                                                                        <td>₩390,000</td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td>9</td>
                                                                        <td>남도 정취 투어 4박 5일 – 목포·순천·보성</td>
                                                                        <td>693</td>
                                                                        <td>47</td>
                                                                        <td>3.53%</td>
                                                                        <td>₩920,000</td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td>10</td>
                                                                        <td>강릉 & 속초 바다 힐링 투어 3박 4일</td>
                                                                        <td>720</td>
                                                                        <td>42</td>
                                                                        <td>2.35%</td>
                                                                        <td>₩530,000</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>

                                                        </div>

                                                    </div>
                                                </div>

                                            </div>

                                        </div>

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





<!-- JS: Flatpickr Initialization and Event Handling -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        console.log("=== INITIALIZING FLATPICKR ===");

        // --- SINGLE DATE PICKERS ---
        document.querySelectorAll(".flatpickr-single").forEach(function(input) {
            const calendarFlatpickr = flatpickr(input, {
                dateFormat: "Y-m-d",
                disableMobile: true,
                clickOpens: true,
                onReady: function() {
                    console.log("Single date picker READY for", input);
                },
                onOpen: function() {
                    console.log("Single date picker OPENED for", input);
                },
                onClose: function() {
                    console.log("Single date picker CLOSED for", input);
                }
            });

            // Wrapper click
            const wrapper = input.closest(".calendar-input");
            const icon = wrapper.querySelector(".calendar-icon");

            wrapper.addEventListener("click", function(e) {
                if (e.target !== input) {
                    calendarFlatpickr.open();
                }
            });

            // Icon click
            icon.addEventListener("click", function(e) {
                e.stopPropagation();
                calendarFlatpickr.open();
            });

            // Store reference on input element for clearing later
            input._flatpickrInstance = calendarFlatpickr;
        });

        // --- DATE RANGE PICKERS ---
        document.querySelectorAll(".flatpickr-range").forEach(function(input) {
            const dateRangeFlatpickr = flatpickr(input, {
                mode: "range",
                dateFormat: "Y-m-d",
                disableMobile: true,
                clickOpens: true,
                onReady: function() {
                    console.log("Range picker READY for", input);
                },
                onOpen: function() {
                    console.log("Range picker OPENED for", input);
                }
            });

            const wrapper = input.closest(".calendar-range");
            const icon = wrapper.querySelector(".calendar-icon");

            wrapper.addEventListener("click", function(e) {
                if (e.target !== input) {
                    dateRangeFlatpickr.open();
                }
            });

            icon.addEventListener("click", function(e) {
                e.stopPropagation();
                dateRangeFlatpickr.open();
            });

            input._flatpickrInstance = dateRangeFlatpickr;
        });

        // --- GENERAL CLEAR BUTTON ---
        const clearButton = document.getElementById("clear-dates");
        if (clearButton) {
            clearButton.addEventListener("click", function(e) {
                e.stopPropagation();
                console.log(">>> CLEAR ALL CLICKED");

                // Clear all single date pickers
                document.querySelectorAll(".flatpickr-single").forEach(function(input) {
                    input._flatpickrInstance.clear();
                    input.value = "";
                    input.blur();
                });

                // Clear all date range pickers
                document.querySelectorAll(".flatpickr-range").forEach(function(input) {
                    input._flatpickrInstance.clear();
                    input.value = "";
                    input.blur();
                });
            });
        }
    });
</script>

<!-- JS: Tab Functionality | First Layer -->
<script>
    const tabButtons = document.querySelectorAll('.count-card-tabs .tab-btn');
    const tabContents = document.querySelectorAll('.count-card-tab-content .tab-content');

    // Load last tab or default
    const lastTab = localStorage.getItem('lastTab') || 'tab1';

    function showTab(tabId) {
        tabContents.forEach(c => c.classList.add('hidden'));
        document.getElementById(tabId).classList.remove('hidden');

        tabButtons.forEach(btn => btn.classList.remove('active'));
        document.querySelector(`.tab-btn[data-tab="${tabId}"]`).classList.add('active');

        localStorage.setItem('lastTab', tabId);
    }

    // Show last tab on load
    showTab(lastTab);

    // Add click events
    tabButtons.forEach(btn => {
        btn.addEventListener('click', () => showTab(btn.dataset.tab));
    });
</script>

<!-- JS: Tab Functionality | Second Layer -->
<script>
    // Select buttons and content within the sales statistics wrapper
    const secondLayerWrapper = document.querySelector('.sales-statistics-table');
    if (secondLayerWrapper) {

        const secondLayerButtons = secondLayerWrapper.querySelectorAll('.wrapper-body-header-left .tab-buttons.second-layer .tab-btn');
        const secondLayerContents = secondLayerWrapper.querySelectorAll('.wrapper-main-body .tab-change-content .subtab-content');

        // Load last active subtab from localStorage, default to subtab1
        const lastSubTab = localStorage.getItem('lastSubTab') || 'subtab1';

        function showSecondLayerTab(tabId) {
            // Hide all subtab contents
            secondLayerContents.forEach(c => c.classList.add('hidden'));

            // Show the selected tab content
            const activeContent = secondLayerWrapper.querySelector(`#${tabId}`);
            if (activeContent) activeContent.classList.remove('hidden');

            // Remove active state from all buttons
            secondLayerButtons.forEach(btn => btn.classList.remove('active'));

            // Add active state to clicked button
            const activeButton = secondLayerWrapper.querySelector(`.tab-btn[data-tab="${tabId}"]`);
            if (activeButton) activeButton.classList.add('active');

            // Save last active subtab
            localStorage.setItem('lastSubTab', tabId);
        }

        // Show last active subtab on load
        showSecondLayerTab(lastSubTab);

        // Attach click events to buttons
        secondLayerButtons.forEach(btn => {
            btn.addEventListener('click', () => showSecondLayerTab(btn.dataset.tab));
        });
    }
</script>

<!-- JS: Count-Up Animation -->
<script>
    document.querySelectorAll('.count[data-count]').forEach(el => {
        const target = +el.dataset.count;
        let current = 0;
        const step = Math.max(1, Math.floor(target / 40));

        const tick = () => {
            current += step;
            if (current >= target) {
                el.textContent = target.toLocaleString();
                return;
            }
            el.textContent = current.toLocaleString();
            requestAnimationFrame(tick);
        };
        tick();
    });
</script>

<!-- JS: Flatpickr Datepicker Initialization -->
<script>
    document.addEventListener("DOMContentLoaded", function() {

        // Generic Flatpickr initializer for all inputs with the given class
        function initFlatpickrByClass(className) {
            const elements = document.querySelectorAll(`.${className}`);
            elements.forEach(el => {
                const isRange = el.classList.contains('flatpickr-range');
                flatpickr(el, {
                    mode: isRange ? 'range' : 'single',
                    dateFormat: 'Y-m-d',
                    onOpen: () => el.classList.add('active-flatpickr'),
                    onClose: () => {
                        el.classList.remove('active-flatpickr');
                        // Auto-detect chart type by input ID
                        if (el.id.includes("vertical")) applyInputFiltersVertical();
                        if (el.id.includes("horizontal")) applyInputFiltersHorizontal();
                    }
                });
            });
        }

        // Initialize all single and range inputs
        initFlatpickrByClass('flatpickr-single');
        initFlatpickrByClass('flatpickr-range');

    });
</script>

<!-- SHARED CHART DATA -->
<script>
    const chartData = {
        daily: {
            categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            values: [12, 18, 10, 22, 30, 25, 16],
            byDate: {
                '2025-01-01': 85,
                '2025-01-02': 120,
                '2025-01-03': 60
            }
        },
        weekly: {
            categories: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
            values: [120, 150, 135, 180],
            byRange: {
                '2025-01-01|2025-01-07': [110, 145, 130, 170],
                '2025-01-08|2025-01-14': [125, 160, 140, 185]
            }
        },
        monthly: {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            values: [320, 280, 350, 400, 370, 420],
            byMonth: {
                '2025-01': [300, 270, 330, 380, 350, 400],
                '2025-02': [340, 290, 360, 410, 380, 430]
            }
        },
        annual: {
            categories: ['2021', '2022', '2023', '2024'],
            values: [3200, 3600, 4100, 4800],
            byYear: {
                '2023': [3000, 3400, 3900, 4500],
                '2024': [3200, 3600, 4100, 4800]
            }
        }
    };
</script>

<!-- VERTICAL CHART SCRIPT -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let currentTabVertical = 'daily';

        const chartVertical = new ApexCharts(document.querySelector("#sales-chart-vertical"), {
            chart: {
                type: 'bar',
                height: '100%',
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '45%',
                    borderRadius: 4
                }
            },
            dataLabels: {
                enabled: false
            },
            series: [{
                name: 'Sales',
                data: chartData.daily.values
            }],
            xaxis: {
                categories: chartData.daily.categories
            },
            grid: {
                strokeDashArray: 4
            },
            responsive: [{
                breakpoint: 768,
                options: {
                    chart: {
                        height: 300
                    }
                }
            }]
        });
        chartVertical.render();

        // Tabs
        document.querySelectorAll('.tab-btn[data-chart="vertical"]').forEach(tab => {
            tab.addEventListener("click", function() {
                document.querySelectorAll('.tab-btn[data-chart="vertical"]').forEach(btn => btn.classList.remove("active"));
                this.classList.add("active");
                currentTabVertical = this.dataset.tab;
                const data = chartData[currentTabVertical];
                resetDateInputsVertical();
                chartVertical.updateOptions({
                    xaxis: {
                        categories: data.categories
                    },
                    series: [{
                        name: 'Sales',
                        data: data.values
                    }]
                });
            });
        });

        // Flatpickr Inputs
        flatpickr("#date-specific-chart-vertical", {
            dateFormat: "Y-m-d",
            onClose: applyInputFiltersVertical
        });
        flatpickr("#date-range-chart-vertical", {
            mode: "range",
            dateFormat: "Y-m-d",
            onClose: applyInputFiltersVertical
        });

        // Clear
        document.getElementById("clear-dates-vertical").addEventListener("click", () => {
            resetDateInputsVertical();
            const data = chartData[currentTabVertical];
            chartVertical.updateOptions({
                xaxis: {
                    categories: data.categories
                },
                series: [{
                    data: data.values
                }]
            });
        });

        function resetDateInputsVertical() {
            document.querySelector("#date-specific-chart-vertical").value = "";
            document.querySelector("#date-range-chart-vertical").value = "";
        }

        function applyInputFiltersVertical() {
            const singleDate = document.querySelector("#date-specific-chart-vertical").value;
            const rangeDate = document.querySelector("#date-range-chart-vertical").value;
            const data = chartData[currentTabVertical];

            if (currentTabVertical === 'daily' && singleDate) {
                chartVertical.updateOptions({
                    xaxis: {
                        categories: [singleDate]
                    },
                    series: [{
                        data: [data.byDate[singleDate] ?? 0]
                    }]
                });
                return;
            }
            if (currentTabVertical === 'weekly' && rangeDate) {
                const [start, end] = rangeDate.split(" to ");
                const key = `${start}|${end||start}`;
                chartVertical.updateOptions({
                    xaxis: {
                        categories: data.categories
                    },
                    series: [{
                        data: data.byRange[key] ?? data.values.map(v => Math.round(v * (0.8 + Math.random() * 0.4)))
                    }]
                });
                return;
            }
            if (currentTabVertical === 'monthly' && singleDate) {
                const key = singleDate.substring(0, 7);
                chartVertical.updateOptions({
                    xaxis: {
                        categories: data.categories
                    },
                    series: [{
                        data: data.byMonth[key] || data.values
                    }]
                });
                return;
            }
            if (currentTabVertical === 'annual' && singleDate) {
                const key = singleDate.substring(0, 4);
                chartVertical.updateOptions({
                    xaxis: {
                        categories: data.categories
                    },
                    series: [{
                        data: data.byYear[key] || data.values
                    }]
                });
                return;
            }
            chartVertical.updateOptions({
                xaxis: {
                    categories: data.categories
                },
                series: [{
                    data: data.values
                }]
            });
        }

        document.querySelector('.tab-btn[data-chart="vertical"][data-tab="daily"]')?.classList.add("active");
        window.addEventListener('resize', () => chartVertical.resize());
    });
</script>

<!-- HORIZONTAL CHART SCRIPT -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let currentTabHorizontal = 'daily';

        const chartHorizontal = new ApexCharts(document.querySelector("#sales-chart-horizontal"), {
            chart: {
                type: 'bar',
                height: '100%',
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    barHeight: '70%',
                    borderRadius: 4
                }
            },
            dataLabels: {
                enabled: false
            },
            series: [{
                name: 'Sales',
                data: chartData.daily.values
            }],
            yaxis: {
                categories: chartData.daily.categories
            },
            grid: {
                strokeDashArray: 4
            },
            responsive: [{
                breakpoint: 768,
                options: {
                    chart: {
                        height: 300
                    }
                }
            }]
        });
        chartHorizontal.render();

        // Tabs
        document.querySelectorAll('.tab-btn[data-chart="horizontal"]').forEach(tab => {
            tab.addEventListener("click", function() {
                document.querySelectorAll('.tab-btn[data-chart="horizontal"]').forEach(btn => btn.classList.remove("active"));
                this.classList.add("active");
                currentTabHorizontal = this.dataset.tab;
                const data = chartData[currentTabHorizontal];
                resetDateInputsHorizontal();
                chartHorizontal.updateOptions({
                    yaxis: {
                        categories: data.categories
                    },
                    series: [{
                        name: 'Sales',
                        data: data.values
                    }]
                });
            });
        });

        // Flatpickr Inputs
        flatpickr("#date-specific-chart-horizontal", {
            dateFormat: "Y-m-d",
            onClose: applyInputFiltersHorizontal
        });
        flatpickr("#date-range-chart-horizontal", {
            mode: "range",
            dateFormat: "Y-m-d",
            onClose: applyInputFiltersHorizontal
        });

        // Clear
        document.getElementById("clear-dates-horizontal").addEventListener("click", () => {
            resetDateInputsHorizontal();
            const data = chartData[currentTabHorizontal];
            chartHorizontal.updateOptions({
                yaxis: {
                    categories: data.categories
                },
                series: [{
                    data: data.values
                }]
            });
        });

        function resetDateInputsHorizontal() {
            document.querySelector("#date-specific-chart-horizontal").value = "";
            document.querySelector("#date-range-chart-horizontal").value = "";
        }

        function applyInputFiltersHorizontal() {
            const singleDate = document.querySelector("#date-specific-chart-horizontal").value;
            const rangeDate = document.querySelector("#date-range-chart-horizontal").value;
            const data = chartData[currentTabHorizontal];

            if (currentTabHorizontal === 'daily' && singleDate) {
                chartHorizontal.updateOptions({
                    yaxis: {
                        categories: [singleDate]
                    },
                    series: [{
                        data: [data.byDate[singleDate] ?? 0]
                    }]
                });
                return;
            }
            if (currentTabHorizontal === 'weekly' && rangeDate) {
                const [start, end] = rangeDate.split(" to ");
                const key = `${start}|${end||start}`;
                chartHorizontal.updateOptions({
                    yaxis: {
                        categories: data.categories
                    },
                    series: [{
                        data: data.byRange[key] ?? data.values.map(v => Math.round(v * (0.8 + Math.random() * 0.4)))
                    }]
                });
                return;
            }
            if (currentTabHorizontal === 'monthly' && singleDate) {
                const key = singleDate.substring(0, 7);
                chartHorizontal.updateOptions({
                    yaxis: {
                        categories: data.categories
                    },
                    series: [{
                        data: data.byMonth[key] || data.values
                    }]
                });
                return;
            }
            if (currentTabHorizontal === 'annual' && singleDate) {
                const key = singleDate.substring(0, 4);
                chartHorizontal.updateOptions({
                    yaxis: {
                        categories: data.categories
                    },
                    series: [{
                        data: data.byYear[key] || data.values
                    }]
                });
                return;
            }
            chartHorizontal.updateOptions({
                yaxis: {
                    categories: data.categories
                },
                series: [{
                    data: data.values
                }]
            });
        }

        document.querySelector('.tab-btn[data-chart="horizontal"][data-tab="daily"]')?.classList.add("active");
        window.addEventListener('resize', () => chartHorizontal.resize());
    });
</script>


<?php include '../../public/includes/initial-js.php'; ?>


</html>