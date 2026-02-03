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

    <link href="../../public/assets/css/page-layout-calendar.css?v=<?= time(); ?>" rel="stylesheet">


    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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
                            <h1 class="page-title">Calendar</h1>
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

                                <!-- Calendar -->
                                <div class="calendar-wrapper">
                                    <div class="calendar-grid">
                                        <div class="weekdays">
                                            <div class="weekday">Sunday</div>
                                            <div class="weekday">Monday</div>
                                            <div class="weekday">Tuesday</div>
                                            <div class="weekday">Wednesday</div>
                                            <div class="weekday">Thursday</div>
                                            <div class="weekday">Friday</div>
                                            <div class="weekday">Saturday</div>
                                        </div>
                                        <div class="days" id="calendarDays"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="calendar-side-wrapper">
                                <div class="calendar-sidebar">
                                    <div class="calendar-sidebar-wrapper">
                                        <!-- Create Event Button -->
                                        <button class="calendar-create-btn" id="createBtn">Create Event</button>

                                        <!-- Mini Calendar -->
                                        <div class="calendar-mini-calendar">
                                            <div class="calendar-mini-header">
                                                <div class="calendar-mini-month" id="miniMonth">January 2026</div>
                                                <div class="calendar-mini-nav">
                                                    <button class="calendar-mini-nav-btn" id="miniPrev">‹</button>
                                                    <button class="calendar-mini-nav-btn" id="miniNext">›</button>
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

                                <div class="calendar-task">
                                    <div class="calendar-task-wrapper">
                                        <div class="calendar-task-header">
                                            <div class="calendar-task-title" id="tasksTitle">Today's Schedule</div>
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
                                    <div class="calendar-modal-date" id="modalDate">January 15, 2026</div>
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
                                            <label class="calendar-form-label">Event title</label>
                                            <input type="text" class="calendar-form-input" id="eventTitle" placeholder="Add title" required>
                                        </div>
                                        <div class="calendar-time-inputs">
                                            <div class="calendar-form-group">
                                                <label class="calendar-form-label">Start time</label>
                                                <input type="time" class="calendar-form-input" id="startTime" required>
                                            </div>
                                            <div class="calendar-form-group">
                                                <label class="calendar-form-label">End time</label>
                                                <input type="time" class="calendar-form-input" id="endTime" required>
                                            </div>
                                        </div>
                                        <div class="calendar-form-group">
                                            <label class="calendar-form-label">Event color</label>
                                            <div class="calendar-color-select" id="colorSelect">
                                                <div class="calendar-color-option selected" data-color="#4f46e5" style="background: #4f46e5"></div>
                                                <div class="calendar-color-option" data-color="#16a34a" style="background: #16a34a"></div>
                                                <div class="calendar-color-option" data-color="#ea580c" style="background: #ea580c"></div>
                                                <div class="calendar-color-option" data-color="#dc2626" style="background: #dc2626"></div>
                                                <div class="calendar-color-option" data-color="#2563eb" style="background: #2563eb"></div>
                                                <div class="calendar-color-option" data-color="#7c3aed" style="background: #7c3aed"></div>
                                            </div>
                                        </div>
                                        <div class="calendar-form-actions">
                                            <button type="button" class="calendar-btn calendar-btn-cancel" id="cancelBtn">Cancel</button>
                                            <button type="submit" class="calendar-btn calendar-btn-primary">Save</button>
                                        </div>
                                    </form>

                                    <div class="calendar-events-header">Events for this day</div>
                                    <div class="calendar-events-list" id="eventsList"></div>

                                </div>
                            </div>
                        </div>



                    </div>
                </div>

            </main>
        </div>
    </div>
</body>




<script src="../../public/gen-js/calendar-script.js"></script>


<?php include '../../public/includes/initial-js.php'; ?>


</html>