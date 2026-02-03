<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Calendar</title>

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
    <link href="../../public/assets/css/full-calendar-defaults.css?v=<?= time(); ?>" rel="stylesheet">
    <link href="../../public/assets/css/page-layout-calendar-2.css?v=<?= time(); ?>" rel="stylesheet">


    <!-- Add before closing </head> tag -->

    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">

    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>


    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

</head>

<?php
// include '../functions/csrf.php';
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
                            <h1 class="page-title">Booking Calendar</h1>
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

                        <div class="app-container">

                            <!-- Main Content -->
                            <div class="main-content-calendar-wrapper">

                                <!-- Top Bar -->
                                <div class="top-bar">
                                    <div class="top-left">
                                        <button class="menu-btn">☰</button>
                                        <div class="current-month" id="currentMonth">January 2026</div>
                                    </div>
                                    <div class="top-right">
                                        <button class="icon-btn" id="prevMonth">‹</button>
                                        <button class="today-btn" id="todayBtn">Today</button>
                                        <button class="icon-btn" id="nextMonth">›</button>
                                    </div>
                                </div>

                                <!-- FullCalendar Container -->
                                <div class="calendar-wrapper">
                                    <div id="calendar"></div>
                                </div>

                            </div>

                            <!-- Sidebar -->
                            <div class="calendar-side-wrapper">

                                <div class="calendar-sidebar">

                                    <div class="calendar-sidebar-wrapper">
                                        <!-- Mini Calendar -->
                                        <div class="calendar-mini-calendar">

                                            <div class="calendar-mini-header">
                                                <div class="calendar-mini-month" id="miniMonth">January 2026</div>
                                                <div class="calendar-mini-nav">
                                                    <button class="calendar-mini-nav-btn" id="miniPrev" aria-label="Previous month">
                                                        <span>‹</span>
                                                    </button>
                                                    <button class="calendar-mini-nav-btn" id="miniNext" aria-label="Next month">
                                                        <span>›</span>
                                                    </button>
                                                </div>
                                            </div>


                                            <div class="calendar-mini-weekdays">
                                                <div class="calendar-mini-weekday">S</div>
                                                <div class="calendar-mini-weekday">M</div>
                                                <div class="calendar-mini-weekday">T</div>
                                                <div class="calendar-mini-weekday">W</div>
                                                <div class="calendar-mini-weekday">T</div>
                                                <div class="calendar-mini-weekday">F</div>
                                                <div class="calendar-mini-weekday">S</div>
                                            </div>

                                            <div class="calendar-mini-days" id="miniDays"></div>
                                        </div>
                                    </div>

                                </div>

                                <!-- Tasks Panel -->
                                <div class="calendar-task">
                                    <div class="calendar-task-wrapper">

                                        <div class="calendar-task-header">
                                            <div class="calendar-task-header-left">
                                                <div class="calendar-task-title" id="tasksTitle">Bookings for the Day</div>
                                            </div>

                                            <div class="calendar-task-header-right">
                                                <button class="calendar-create-btn" id="createBtn">New Booking</button>
                                            </div>
                                        </div>



                                        <div class="calendar-task-list" id="tasksList">
                                            <div class="calendar-task-empty">
                                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                                </svg>
                                                <p>No events scheduled</p>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>

                        </div>

                        <!-- Modal -->
                        <div class="calendar-modal-overlay" id="modalOverlay">
                            <div class="calendar-modal">
                                <div class="calendar-modal-header">
                                    <div class="calendar-modal-date" id="modalDate">Create New Booking</div>
                                    <button class="calendar-modal-close" id="closeModal" aria-label="Close modal">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <line x1="18" y1="6" x2="6" y2="18"></line>
                                            <line x1="6" y1="6" x2="18" y2="18"></line>
                                        </svg>
                                    </button>
                                </div>

                                <div class="calendar-modal-body">
                                    <form class="calendar-event-form" id="eventForm">

                                        <div class="calendar-form-group">
                                            <label class="calendar-form-label">Booking Title *</label>
                                            <input type="text" class="calendar-form-input" id="bookingTitle" placeholder="e.g., Family Vacation, Business Trip" required>
                                        </div>

                                        <div class="calendar-form-row">
                                            <div class="calendar-form-group">
                                                <label class="calendar-form-label">Season Package *</label>
                                                <select class="calendar-form-input" id="seasonSelect" required>
                                                    <option value="">Select Season</option>
                                                    <option value="Summer">Summer</option>
                                                    <option value="Spring">Spring</option>
                                                    <option value="Winter">Winter</option>
                                                    <option value="Autumn">Autumn</option>
                                                </select>
                                            </div>

                                            <div class="calendar-form-group">
                                                <label class="calendar-form-label">Branch *</label>
                                                <select class="calendar-form-input" id="branchSelect" required>
                                                    <option value="">Select Branch</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Outbound Flight -->
                                        <div class="calendar-flight-section">
                                            <div class="calendar-flight-header">Outbound Flight</div>

                                            <div class="calendar-date-time-grid">
                                                <div class="calendar-form-group">
                                                    <label class="calendar-form-label">Departure Date *</label>
                                                    <input type="date" class="calendar-form-input" id="departureDate" required>
                                                </div>
                                                <div class="calendar-form-group">
                                                    <label class="calendar-form-label">Departure Time *</label>
                                                    <select class="calendar-form-input" id="departureTime" required>
                                                        <option value="">Select time</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="calendar-date-time-grid">
                                                <div class="calendar-form-group">
                                                    <label class="calendar-form-label">Arrival Date *</label>
                                                    <input type="date" class="calendar-form-input" id="arrivalDate" required>
                                                </div>
                                                <div class="calendar-form-group">
                                                    <label class="calendar-form-label">Arrival Time *</label>
                                                    <select class="calendar-form-input" id="arrivalTime" required>
                                                        <option value="">Select time</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Return Flight -->
                                        <div class="calendar-flight-section">
                                            <div class="calendar-flight-header">Return Flight</div>

                                            <div class="calendar-date-time-grid">
                                                <div class="calendar-form-group">
                                                    <label class="calendar-form-label">Departure Date *</label>
                                                    <input type="date" class="calendar-form-input" id="returnDepartureDate" required>
                                                </div>
                                                <div class="calendar-form-group">
                                                    <label class="calendar-form-label">Departure Time *</label>
                                                    <select class="calendar-form-input" id="returnDepartureTime" required>
                                                        <option value="">Select time</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="calendar-date-time-grid">
                                                <div class="calendar-form-group">
                                                    <label class="calendar-form-label">Arrival Date *</label>
                                                    <input type="date" class="calendar-form-input" id="returnArrivalDate" required>
                                                </div>
                                                <div class="calendar-form-group">
                                                    <label class="calendar-form-label">Arrival Time *</label>
                                                    <select class="calendar-form-input" id="returnArrivalTime" required>
                                                        <option value="">Select time</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Days/Nights Display -->
                                        <div class="calendar-duration-display">
                                            <div class="calendar-duration-label">Total Duration:</div>
                                            <div class="calendar-duration-value" id="daysNightsDisplay">0D 0N</div>
                                        </div>

                                        <div class="calendar-form-group">
                                            <label class="calendar-form-label">Description (Optional)</label>
                                            <textarea class="calendar-form-input calendar-form-textarea" id="eventDescription" placeholder="Add notes or details..." rows="4"></textarea>
                                        </div>

                                        <div class="calendar-form-actions">
                                            <button type="button" class="calendar-btn calendar-btn-cancel" id="cancelBtn">Cancel</button>
                                            <button type="submit" class="calendar-btn calendar-btn-primary">Save Booking</button>
                                        </div>

                                    </form>

                                    <div class="calendar-events-header">Bookings for Selected Date</div>
                                    <div class="calendar-events-list" id="eventsList"></div>
                                </div>
                            </div>
                        </div>

                        <!-- FullCalendar JS -->
                        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

                        <!-- Custom JS -->
                        <script src="../../public/gen-js/calendar-script-2.js"></script>


                    </div>
                </div>

            </main>
        </div>
    </div>
</body>






<?php include '../../public/includes/initial-js.php'; ?>


</html>