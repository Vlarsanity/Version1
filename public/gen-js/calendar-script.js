/* =========================================================
   DOM REFERENCES
========================================================= */
const currentMonthEl = document.getElementById("currentMonth");
const calendarDays = document.getElementById("calendarDays");

const miniMonthEl = document.getElementById("miniMonth");
const miniDays = document.getElementById("miniDays");

const modalOverlay = document.getElementById("modalOverlay");
const modalDate = document.getElementById("modalDate");

const eventForm = document.getElementById("eventForm");
const colorSelect = document.getElementById("colorSelect");
const eventsList = document.getElementById("eventsList");

const tasksTitle = document.getElementById("tasksTitle");
const tasksList = document.getElementById("tasksList");

/* =========================================================
   STATE
========================================================= */
const TODAY = new Date(2026, 0, 14); // January 14, 2026

let currentDate = new Date(TODAY);
let miniDate = new Date(TODAY);
let selectedDate = new Date(TODAY); // Currently selected date for task panel
let selectedColor = "#4f46e5";

/* =========================================================
   SAMPLE EVENTS DATA
========================================================= */
let events = {
  "2026-1-2": [
    {
      id: 1,
      title: "Team Standup",
      startTime: "09:00",
      endTime: "09:30",
      color: "#4f46e5",
    },
    {
      id: 2,
      title: "Client Presentation",
      startTime: "14:00",
      endTime: "15:30",
      color: "#dc2626",
    },
  ],
  "2026-1-3": [
    {
      id: 3,
      title: "Dentist Appointment",
      startTime: "10:00",
      endTime: "11:00",
      color: "#16a34a",
    },
    {
      id: 4,
      title: "Code Review",
      startTime: "15:00",
      endTime: "16:00",
      color: "#4f46e5",
    },
  ],
  "2026-1-6": [
    {
      id: 5,
      title: "Gym Session",
      startTime: "06:00",
      endTime: "07:00",
      color: "#16a34a",
    },
    {
      id: 6,
      title: "Sprint Planning",
      startTime: "10:00",
      endTime: "12:00",
      color: "#dc2626",
    },
  ],
  "2026-1-14": [
    {
      id: 7,
      title: "Project Kickoff",
      startTime: "09:00",
      endTime: "10:30",
      color: "#dc2626",
    },
    {
      id: 8,
      title: "Weekly Review",
      startTime: "16:00",
      endTime: "17:00",
      color: "#4f46e5",
    },
  ],
  "2026-1-15": [
    {
      id: 9,
      title: "Team Lunch",
      startTime: "12:00",
      endTime: "13:00",
      color: "#16a34a",
    },
    {
      id: 10,
      title: "Product Demo",
      startTime: "14:00",
      endTime: "15:30",
      color: "#2563eb",
    },
  ],
  "2026-1-18": [
    {
      id: 11,
      title: "Yoga Class",
      startTime: "07:00",
      endTime: "08:00",
      color: "#16a34a",
    },
    {
      id: 12,
      title: "Birthday Party",
      startTime: "15:00",
      endTime: "18:00",
      color: "#ea580c",
    },
  ],
  "2026-1-22": [
    {
      id: 13,
      title: "Budget Meeting",
      startTime: "09:00",
      endTime: "10:30",
      color: "#dc2626",
    },
  ],
  "2026-1-25": [
    {
      id: 14,
      title: "Weekend Trip",
      startTime: "00:00",
      endTime: "23:59",
      color: "#7c3aed",
    },
  ],
};

/* =========================================================
   INITIALIZE
========================================================= */
modalOverlay.classList.remove("active"); // ensure modal hidden on load

renderCalendar();
renderMiniCalendar();
renderTasksPanel(selectedDate); // Show today's tasks by default

/* =========================================================
   NAVIGATION EVENTS
========================================================= */
document.getElementById("prevMonth").addEventListener("click", () => {
  currentDate.setMonth(currentDate.getMonth() - 1);
  renderCalendar();
});

document.getElementById("nextMonth").addEventListener("click", () => {
  currentDate.setMonth(currentDate.getMonth() + 1);
  renderCalendar();
});

document.getElementById("todayBtn").addEventListener("click", () => {
  currentDate = new Date(TODAY);
  selectedDate = new Date(TODAY);
  renderCalendar();
  renderTasksPanel(selectedDate);
  renderMiniCalendar();
});

document.getElementById("miniPrev").addEventListener("click", () => {
  miniDate.setMonth(miniDate.getMonth() - 1);
  renderMiniCalendar();
});

document.getElementById("miniNext").addEventListener("click", () => {
  miniDate.setMonth(miniDate.getMonth() + 1);
  renderMiniCalendar();
});

document.getElementById("createBtn").addEventListener("click", () => {
  openModal(
    selectedDate.getFullYear(),
    selectedDate.getMonth(),
    selectedDate.getDate()
  );
});

/* =========================================================
   MODAL EVENTS
========================================================= */
document.getElementById("cancelBtn").addEventListener("click", closeModal);
document.getElementById("closeModal").addEventListener("click", closeModal);

modalOverlay.addEventListener("click", (e) => {
  if (e.target === modalOverlay) closeModal();
});

eventForm.addEventListener("submit", (e) => {
  e.preventDefault();
  addEvent();
});

/* =========================================================
   COLOR PICKER
========================================================= */
colorSelect.addEventListener("click", (e) => {
  const option = e.target.closest(".calendar-color-option");
  if (!option) return;

  document
    .querySelectorAll(".calendar-color-option")
    .forEach((o) => o.classList.remove("selected"));

  option.classList.add("selected");
  selectedColor = option.dataset.color;
});

/* =========================================================
   KEYBOARD SHORTCUTS
========================================================= */
document.addEventListener("keydown", (e) => {
  // Escape - close modal
  if (e.key === "Escape" && modalOverlay.classList.contains("active")) {
    closeModal();
  }

  // Don't handle shortcuts if typing in input
  if (e.target.tagName === "INPUT" || e.target.tagName === "TEXTAREA") {
    return;
  }

  // C - create event
  if (e.key === "c" || e.key === "C") {
    openModal(
      selectedDate.getFullYear(),
      selectedDate.getMonth(),
      selectedDate.getDate()
    );
  }

  // T - go to today
  if (e.key === "t" || e.key === "T") {
    currentDate = new Date(TODAY);
    selectedDate = new Date(TODAY);
    renderCalendar();
    renderTasksPanel(selectedDate);
    renderMiniCalendar();
  }

  // Arrow keys - navigate days
  if (e.key === "ArrowLeft") {
    selectedDate.setDate(selectedDate.getDate() - 1);
    renderCalendar();
    renderTasksPanel(selectedDate);
  }

  if (e.key === "ArrowRight") {
    selectedDate.setDate(selectedDate.getDate() + 1);
    renderCalendar();
    renderTasksPanel(selectedDate);
  }
});

/* =========================================================
   CALENDAR RENDERING
========================================================= */
function renderCalendar() {
  const year = currentDate.getFullYear();
  const month = currentDate.getMonth();

  currentMonthEl.textContent = new Date(year, month).toLocaleDateString(
    "en-US",
    {
      month: "long",
      year: "numeric",
    }
  );

  const firstDay = new Date(year, month, 1).getDay();
  const daysInMonth = new Date(year, month + 1, 0).getDate();
  const prevDays = new Date(year, month, 0).getDate();

  calendarDays.innerHTML = "";

  // Previous month days
  for (let i = firstDay - 1; i >= 0; i--) {
    calendarDays.appendChild(createDay(prevDays - i, true, year, month - 1));
  }

  // Current month days
  for (let d = 1; d <= daysInMonth; d++) {
    calendarDays.appendChild(createDay(d, false, year, month));
  }

  // Next month days
  const remaining = 42 - (firstDay + daysInMonth);
  for (let d = 1; d <= remaining; d++) {
    calendarDays.appendChild(createDay(d, true, year, month + 1));
  }
}

function createDay(day, isOtherMonth, year, month) {
  const el = document.createElement("div");
  el.className = "day";

  if (isOtherMonth) {
    el.classList.add("other-month");
  }

  const cellDate = new Date(year, month, day);

  // Check if today
  if (isSameDate(cellDate, TODAY)) {
    el.classList.add("today");
  }

  // Check if selected
  if (isSameDate(cellDate, selectedDate)) {
    el.classList.add("selected");
  }

  // Click handler - select date and show tasks
  el.addEventListener("click", () => {
    selectedDate = new Date(year, month, day);
    renderCalendar();
    renderTasksPanel(selectedDate);
    renderMiniCalendar();
  });

  const dateKey = `${year}-${month + 1}-${day}`;
  const dayEvents = events[dateKey] || [];

  el.innerHTML = `
        <div class="day-number">${day}</div>
        <div class="day-events">
            ${dayEvents
              .slice(0, 3)
              .map(
                (ev) =>
                  `<div class="day-event" style="background:${ev.color}" data-event-id="${ev.id}">${ev.title}</div>`
              )
              .join("")}
            ${
              dayEvents.length > 3
                ? `<div class="more-events">+${dayEvents.length - 3} more</div>`
                : ""
            }
        </div>
    `;

  // Event click handlers
  el.querySelectorAll(".day-event, .more-events").forEach((eventEl) => {
    eventEl.addEventListener("click", (e) => {
      e.stopPropagation();
      openModal(year, month, day);
    });
  });

  return el;
}

/* =========================================================
   MINI CALENDAR
========================================================= */
function renderMiniCalendar() {
  const year = miniDate.getFullYear();
  const month = miniDate.getMonth();

  miniMonthEl.textContent = new Date(year, month).toLocaleDateString("en-US", {
    month: "long",
    year: "numeric",
  });

  const firstDay = new Date(year, month, 1).getDay();
  const daysInMonth = new Date(year, month + 1, 0).getDate();
  const prevDays = new Date(year, month, 0).getDate();

  miniDays.innerHTML = "";

  // Previous month days
  for (let i = firstDay - 1; i >= 0; i--) {
    miniDays.appendChild(createMiniDay(prevDays - i, true, year, month - 1));
  }

  // Current month days
  for (let d = 1; d <= daysInMonth; d++) {
    miniDays.appendChild(createMiniDay(d, false, year, month));
  }

  // Next month days
  const remaining = 42 - (firstDay + daysInMonth);
  for (let d = 1; d <= remaining; d++) {
    miniDays.appendChild(createMiniDay(d, true, year, month + 1));
  }
}

function createMiniDay(day, isOtherMonth, year, month) {
  const el = document.createElement("div");
  el.className = "calendar-mini-day";
  el.textContent = day;

  if (isOtherMonth) {
    el.classList.add("other-month");
  }

  const cellDate = new Date(year, month, day);
  const dateKey = `${year}-${month + 1}-${day}`;

  // Check if today
  if (isSameDate(cellDate, TODAY)) {
    el.classList.add("today");
  }

  // Check if has events
  if (events[dateKey]?.length) {
    el.classList.add("has-events");
  }

  // Click handler - navigate to date
  el.addEventListener("click", () => {
    currentDate = new Date(year, month, day);
    selectedDate = new Date(year, month, day);
    renderCalendar();
    renderTasksPanel(selectedDate);
    renderMiniCalendar();
  });

  return el;
}

/* =========================================================
   TASKS PANEL
========================================================= */
function renderTasksPanel(date) {
  const year = date.getFullYear();
  const month = date.getMonth();
  const day = date.getDate();
  const dateKey = `${year}-${month + 1}-${day}`;
  const dayEvents = events[dateKey] || [];

  // Update title
  const isToday = isSameDate(date, TODAY);
  const dateStr = isToday
    ? "Today"
    : date.toLocaleDateString("en-US", {
        month: "short",
        day: "numeric",
      });
  tasksTitle.textContent = `${dateStr}'s Schedule`;

  // Clear and render tasks
  tasksList.innerHTML = "";

  if (dayEvents.length === 0) {
    tasksList.innerHTML = `
            <div class="calendar-task-empty">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                <p>No events scheduled</p>
            </div>
        `;
    return;
  }

  // Sort events by start time
  const sortedEvents = [...dayEvents].sort((a, b) => {
    return a.startTime.localeCompare(b.startTime);
  });

  sortedEvents.forEach((evt) => {
    const taskItem = document.createElement("div");
    taskItem.className = "calendar-task-item";
    taskItem.style.borderLeftColor = evt.color;

    taskItem.innerHTML = `
            <div class="calendar-task-time">${evt.startTime} - ${evt.endTime}</div>
            <div class="calendar-task-name">${evt.title}</div>
            <div class="calendar-task-actions">
                <button class="calendar-task-delete" data-id="${evt.id}">Delete</button>
            </div>
        `;

    taskItem.addEventListener("click", (e) => {
      if (!e.target.classList.contains("calendar-task-delete")) {
        openModal(year, month, day);
      }
    });

    const deleteBtn = taskItem.querySelector(".calendar-task-delete");
    deleteBtn.addEventListener("click", (e) => {
      e.stopPropagation();
      deleteEvent(dateKey, evt.id);
    });

    tasksList.appendChild(taskItem);
  });
}

/* =========================================================
   MODAL LOGIC
========================================================= */
function openModal(year, month, day) {
  selectedDate = new Date(year, month, day);

  modalDate.textContent = new Date(year, month, day).toLocaleDateString(
    "en-US",
    {
      weekday: "long",
      month: "long",
      day: "numeric",
      year: "numeric",
    }
  );

  renderEvents();
  modalOverlay.classList.add("active");
  document.body.style.overflow = "hidden";

  const titleInput = document.getElementById("eventTitle");
  setTimeout(() => {
    if (titleInput) titleInput.focus();
  }, 100);
}

function closeModal() {
  modalOverlay.classList.remove("active");
  eventForm.reset();

  document
    .querySelectorAll(".calendar-color-option")
    .forEach((o) => o.classList.remove("selected"));

  const defaultOption = document.querySelector(".calendar-color-option");
  if (defaultOption) defaultOption.classList.add("selected");

  selectedColor = "#4f46e5";
  document.body.style.overflow = "";
}

/* =========================================================
   EVENTS CRUD
========================================================= */
function addEvent() {
  const title = eventTitle.value.trim();
  const start = startTime.value;
  const end = endTime.value;

  if (!title || !start || !end) return;

  const key = `${selectedDate.getFullYear()}-${
    selectedDate.getMonth() + 1
  }-${selectedDate.getDate()}`;
  events[key] ??= [];

  events[key].push({
    id: Date.now(),
    title,
    startTime: start,
    endTime: end,
    color: selectedColor,
  });

  closeModal();
  renderCalendar();
  renderMiniCalendar();
  renderTasksPanel(selectedDate);
}

function renderEvents() {
  const key = `${selectedDate.getFullYear()}-${
    selectedDate.getMonth() + 1
  }-${selectedDate.getDate()}`;
  const list = events[key] || [];

  if (!list.length) {
    eventsList.innerHTML = `<div class="calendar-empty-state">No events scheduled</div>`;
    return;
  }

  eventsList.innerHTML = "";

  // Sort events by start time
  const sortedList = [...list].sort((a, b) =>
    a.startTime.localeCompare(b.startTime)
  );

  sortedList.forEach((ev) => {
    const el = document.createElement("div");
    el.className = "calendar-event-item";
    el.style.borderLeftColor = ev.color;

    el.innerHTML = `
            <div class="calendar-event-title">${ev.title}</div>
            <div class="calendar-event-time">${ev.startTime} - ${ev.endTime}</div>
            <button class="calendar-delete-event">Delete</button>
        `;

    el.querySelector(".calendar-delete-event").onclick = () => {
      events[key] = events[key].filter((e) => e.id !== ev.id);
      if (events[key].length === 0) {
        delete events[key];
      }
      renderCalendar();
      renderMiniCalendar();
      renderTasksPanel(selectedDate);
      renderEvents();
    };

    eventsList.appendChild(el);
  });
}

function deleteEvent(dateKey, eventId) {
  if (events[dateKey]) {
    events[dateKey] = events[dateKey].filter((e) => e.id !== eventId);

    if (events[dateKey].length === 0) {
      delete events[dateKey];
    }
  }

  renderCalendar();
  renderMiniCalendar();
  renderTasksPanel(selectedDate);
}

/* =========================================================
   UTILITY FUNCTIONS
========================================================= */
function isSameDate(date1, date2) {
  return (
    date1.getFullYear() === date2.getFullYear() &&
    date1.getMonth() === date2.getMonth() &&
    date1.getDate() === date2.getDate()
  );
}
