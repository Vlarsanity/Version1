/* =========================================================
   BRANCHES CONFIGURATION (JSON)
========================================================= */
const BRANCHES = [
  { id: 1, name: "Sulit Traveler", color: "#4f46e5" },
  { id: 2, name: "Lipad Lakbay", color: "#16a34a" },
  { id: 3, name: "P91", color: "#ea580c" },
  { id: 4, name: "Future Diamond", color: "#dc2626" },
  { id: 5, name: "E-Winer", color: "#2563eb" },
  { id: 6, name: "Francia", color: "#7c3aed" },
  { id: 7, name: "Travel Escape", color: "#059669" },
  { id: 8, name: "APD", color: "#d97706" }
];

const SEASONS = ["Summer", "Spring", "Winter", "Autumn"];

/* =========================================================
   DOM REFERENCES
========================================================= */
const currentMonthEl = document.getElementById("currentMonth");
const miniMonthEl = document.getElementById("miniMonth");
const miniDays = document.getElementById("miniDays");
const modalOverlay = document.getElementById("modalOverlay");
const modalDate = document.getElementById("modalDate");
const eventForm = document.getElementById("eventForm");
const eventsList = document.getElementById("eventsList");
const tasksTitle = document.getElementById("tasksTitle");
const tasksList = document.getElementById("tasksList");

// Form elements
const bookingTitleInput = document.getElementById("bookingTitle");
const seasonSelect = document.getElementById("seasonSelect");
const branchSelect = document.getElementById("branchSelect");
const departureDateInput = document.getElementById("departureDate");
const departureTimeInput = document.getElementById("departureTime");
const arrivalDateInput = document.getElementById("arrivalDate");
const arrivalTimeInput = document.getElementById("arrivalTime");
const returnDepartureDateInput = document.getElementById("returnDepartureDate");
const returnDepartureTimeInput = document.getElementById("returnDepartureTime");
const returnArrivalDateInput = document.getElementById("returnArrivalDate");
const returnArrivalTimeInput = document.getElementById("returnArrivalTime");
const daysNightsDisplay = document.getElementById("daysNightsDisplay");
const eventDescriptionInput = document.getElementById("eventDescription");

/* =========================================================
   STATE
========================================================= */
const TODAY = new Date();
let miniDate = new Date(TODAY);
let selectedDate = new Date(TODAY);
let calendar;
let editingEventId = null;

/* =========================================================
   TIME SLOT GENERATION (24-HOUR FORMAT, 30-MIN INTERVALS)
========================================================= */
function generateTimeSlots() {
  const slots = [];
  for (let hour = 0; hour < 24; hour++) {
    for (let min = 0; min < 60; min += 30) {
      const timeStr = `${hour.toString().padStart(2, '0')}:${min.toString().padStart(2, '0')}`;
      slots.push({ value: timeStr, display: timeStr });
    }
  }
  return slots;
}

/* =========================================================
   POPULATE TIME DROPDOWNS
========================================================= */
function populateTimeDropdown(selectElement, selectedTime = null, minTime = null) {
  const slots = generateTimeSlots();
  selectElement.innerHTML = '<option value="">Select time</option>';
  
  slots.forEach(slot => {
    if (minTime && slot.value < minTime) {
      return;
    }
    
    const option = document.createElement('option');
    option.value = slot.value;
    option.textContent = slot.value;
    
    if (selectedTime && slot.value === selectedTime) {
      option.selected = true;
    }
    
    selectElement.appendChild(option);
  });
}

/* =========================================================
   GET CURRENT TIME ROUNDED TO NEXT 30-MIN SLOT
========================================================= */
function getNextAvailableTime(date = new Date()) {
  const hours = date.getHours();
  const minutes = date.getMinutes();
  
  let nextMin = minutes <= 30 ? 30 : 0;
  let nextHour = minutes <= 30 ? hours : hours + 1;
  
  if (nextHour >= 24) {
    nextHour = 0;
  }
  
  return `${nextHour.toString().padStart(2, '0')}:${nextMin.toString().padStart(2, '0')}`;
}

/* =========================================================
   CALCULATE DAYS AND NIGHTS
========================================================= */
function calculateDaysNights(departureDate, returnArrivalDate) {
  if (!departureDate || !returnArrivalDate) {
    return { days: 0, nights: 0 };
  }
  
  const start = new Date(departureDate);
  const end = new Date(returnArrivalDate);
  
  // Calculate difference in days
  const diffTime = Math.abs(end - start);
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  
  const days = diffDays + 1; // Include both start and end day
  const nights = days - 1; // Nights are one less than days
  
  return { days, nights };
}

function updateDaysNightsDisplay() {
  const departureDate = departureDateInput.value;
  const returnArrivalDate = returnArrivalDateInput.value;
  
  if (departureDate && returnArrivalDate) {
    const { days, nights } = calculateDaysNights(departureDate, returnArrivalDate);
    daysNightsDisplay.textContent = `${days}D ${nights}N`;
  } else {
    daysNightsDisplay.textContent = '0D 0N';
  }
}

/* =========================================================
   DATE HANDLING
========================================================= */
function formatDateForInput(date) {
  const year = date.getFullYear();
  const month = (date.getMonth() + 1).toString().padStart(2, '0');
  const day = date.getDate().toString().padStart(2, '0');
  return `${year}-${month}-${day}`;
}

function isSameDay(date1, date2) {
  return formatDateForInput(date1) === formatDateForInput(date2);
}

/* =========================================================
   SAMPLE BOOKINGS DATA
========================================================= */
const getCurrentMonthYear = () => {
  const now = new Date();
  return { year: now.getFullYear(), month: now.getMonth() + 1 };
};

const { year, month } = getCurrentMonthYear();

const sampleEvents = [
  {
    id: 1,
    title: "Summer - Sulit Traveler",
    start: `${year}-${String(month).padStart(2, '0')}-20T09:00:00`,
    end: `${year}-${String(month).padStart(2, '0')}-25T17:00:00`,
    backgroundColor: BRANCHES[0].color,
    borderColor: BRANCHES[0].color,
    extendedProps: {
      bookingTitle: "Business Trip",
      season: "Summer",
      branchId: 1,
      branchName: "Sulit Traveler",
      departureDate: `${year}-${String(month).padStart(2, '0')}-20`,
      departureTime: "09:00",
      arrivalDate: `${year}-${String(month).padStart(2, '0')}-20`,
      arrivalTime: "14:00",
      returnDepartureDate: `${year}-${String(month).padStart(2, '0')}-25`,
      returnDepartureTime: "10:00",
      returnArrivalDate: `${year}-${String(month).padStart(2, '0')}-25`,
      returnArrivalTime: "17:00",
      days: 6,
      nights: 5,
      description: "Corporate team building and business meetings"
    }
  },
  {
    id: 2,
    title: "Winter - Lipad Lakbay",
    start: `${year}-${String(month).padStart(2, '0')}-22T08:00:00`,
    end: `${year}-${String(month).padStart(2, '0')}-27T20:00:00`,
    backgroundColor: BRANCHES[1].color,
    borderColor: BRANCHES[1].color,
    extendedProps: {
      bookingTitle: "Family Beach Holiday",
      season: "Winter",
      branchId: 2,
      branchName: "Lipad Lakbay",
      departureDate: `${year}-${String(month).padStart(2, '0')}-22`,
      departureTime: "08:00",
      arrivalDate: `${year}-${String(month).padStart(2, '0')}-22`,
      arrivalTime: "12:00",
      returnDepartureDate: `${year}-${String(month).padStart(2, '0')}-27`,
      returnDepartureTime: "16:00",
      returnArrivalDate: `${year}-${String(month).padStart(2, '0')}-27`,
      returnArrivalTime: "20:00",
      days: 6,
      nights: 5,
      description: "Family beach vacation package with activities"
    }
  },
  {
    id: 3,
    title: "Spring - P91",
    start: `${year}-${String(month).padStart(2, '0')}-15T10:00:00`,
    end: `${year}-${String(month).padStart(2, '0')}-18T18:00:00`,
    backgroundColor: BRANCHES[2].color,
    borderColor: BRANCHES[2].color,
    extendedProps: {
      bookingTitle: "Weekend Getaway",
      season: "Spring",
      branchId: 3,
      branchName: "P91",
      departureDate: `${year}-${String(month).padStart(2, '0')}-15`,
      departureTime: "10:00",
      arrivalDate: `${year}-${String(month).padStart(2, '0')}-15`,
      arrivalTime: "13:00",
      returnDepartureDate: `${year}-${String(month).padStart(2, '0')}-18`,
      returnDepartureTime: "15:00",
      returnArrivalDate: `${year}-${String(month).padStart(2, '0')}-18`,
      returnArrivalTime: "18:00",
      days: 4,
      nights: 3,
      description: "Quick spring weekend escape"
    }
  },
  {
    id: 4,
    title: "Autumn - Future Diamond",
    start: `${year}-${String(month + 1).padStart(2, '0')}-05T07:00:00`,
    end: `${year}-${String(month + 1).padStart(2, '0')}-12T19:00:00`,
    backgroundColor: BRANCHES[3].color,
    borderColor: BRANCHES[3].color,
    extendedProps: {
      bookingTitle: "Honeymoon Package",
      season: "Autumn",
      branchId: 4,
      branchName: "Future Diamond",
      departureDate: `${year}-${String(month + 1).padStart(2, '0')}-05`,
      departureTime: "07:00",
      arrivalDate: `${year}-${String(month + 1).padStart(2, '0')}-05`,
      arrivalTime: "11:00",
      returnDepartureDate: `${year}-${String(month + 1).padStart(2, '0')}-12`,
      returnDepartureTime: "14:00",
      returnArrivalDate: `${year}-${String(month + 1).padStart(2, '0')}-12`,
      returnArrivalTime: "19:00",
      days: 8,
      nights: 7,
      description: "Romantic honeymoon destination package"
    }
  },
  {
    id: 5,
    title: "Summer - E-Winer",
    start: `${year}-${String(month + 1).padStart(2, '0')}-10T09:30:00`,
    end: `${year}-${String(month + 1).padStart(2, '0')}-14T16:00:00`,
    backgroundColor: BRANCHES[4].color,
    borderColor: BRANCHES[4].color,
    extendedProps: {
      bookingTitle: "Adventure Tour",
      season: "Summer",
      branchId: 5,
      branchName: "E-Winer",
      departureDate: `${year}-${String(month + 1).padStart(2, '0')}-10`,
      departureTime: "09:30",
      arrivalDate: `${year}-${String(month + 1).padStart(2, '0')}-10`,
      arrivalTime: "12:30",
      returnDepartureDate: `${year}-${String(month + 1).padStart(2, '0')}-14`,
      returnDepartureTime: "13:00",
      returnArrivalDate: `${year}-${String(month + 1).padStart(2, '0')}-14`,
      returnArrivalTime: "16:00",
      days: 5,
      nights: 4,
      description: "Exciting adventure activities and tours"
    }
  },
  {
    id: 6,
    title: "Winter - Francia",
    start: `${year}-${String(month + 1).padStart(2, '0')}-18T08:00:00`,
    end: `${year}-${String(month + 1).padStart(2, '0')}-25T20:00:00`,
    backgroundColor: BRANCHES[5].color,
    borderColor: BRANCHES[5].color,
    extendedProps: {
      bookingTitle: "Cultural Heritage Tour",
      season: "Winter",
      branchId: 6,
      branchName: "Francia",
      departureDate: `${year}-${String(month + 1).padStart(2, '0')}-18`,
      departureTime: "08:00",
      arrivalDate: `${year}-${String(month + 1).padStart(2, '0')}-18`,
      arrivalTime: "11:30",
      returnDepartureDate: `${year}-${String(month + 1).padStart(2, '0')}-25`,
      returnDepartureTime: "17:00",
      returnArrivalDate: `${year}-${String(month + 1).padStart(2, '0')}-25`,
      returnArrivalTime: "20:00",
      days: 8,
      nights: 7,
      description: "Historical sites and cultural immersion experience"
    }
  },
  {
    id: 7,
    title: "Spring - Travel Escape",
    start: `${year}-${String(month + 2).padStart(2, '0')}-03T10:00:00`,
    end: `${year}-${String(month + 2).padStart(2, '0')}-08T15:00:00`,
    backgroundColor: BRANCHES[6].color,
    borderColor: BRANCHES[6].color,
    extendedProps: {
      bookingTitle: "Wellness Retreat",
      season: "Spring",
      branchId: 7,
      branchName: "Travel Escape",
      departureDate: `${year}-${String(month + 2).padStart(2, '0')}-03`,
      departureTime: "10:00",
      arrivalDate: `${year}-${String(month + 2).padStart(2, '0')}-03`,
      arrivalTime: "13:00",
      returnDepartureDate: `${year}-${String(month + 2).padStart(2, '0')}-08`,
      returnDepartureTime: "12:00",
      returnArrivalDate: `${year}-${String(month + 2).padStart(2, '0')}-08`,
      returnArrivalTime: "15:00",
      days: 6,
      nights: 5,
      description: "Spa and wellness relaxation package"
    }
  },
  {
    id: 8,
    title: "Autumn - APD",
    start: `${year}-${String(month + 2).padStart(2, '0')}-12T07:30:00`,
    end: `${year}-${String(month + 2).padStart(2, '0')}-16T18:30:00`,
    backgroundColor: BRANCHES[7].color,
    borderColor: BRANCHES[7].color,
    extendedProps: {
      bookingTitle: "Island Hopping",
      season: "Autumn",
      branchId: 8,
      branchName: "APD",
      departureDate: `${year}-${String(month + 2).padStart(2, '0')}-12`,
      departureTime: "07:30",
      arrivalDate: `${year}-${String(month + 2).padStart(2, '0')}-12`,
      arrivalTime: "10:30",
      returnDepartureDate: `${year}-${String(month + 2).padStart(2, '0')}-16`,
      returnDepartureTime: "15:30",
      returnArrivalDate: `${year}-${String(month + 2).padStart(2, '0')}-16`,
      returnArrivalTime: "18:30",
      days: 5,
      nights: 4,
      description: "Multi-island adventure and exploration"
    }
  }
];

/* =========================================================
   INITIALIZE FULLCALENDAR
========================================================= */
document.addEventListener("DOMContentLoaded", function () {
  const calendarEl = document.getElementById("calendar");

  if (!calendarEl) {
    console.error("Calendar element not found!");
    return;
  }

  if (typeof FullCalendar === 'undefined') {
    console.error("FullCalendar library not loaded!");
    return;
  }

  // Populate branch select dropdown
  populateBranchSelect();

  calendar = new FullCalendar.Calendar(calendarEl, {
    initialDate: TODAY,
    initialView: "dayGridMonth",
    headerToolbar: false,
    editable: true,
    selectable: true,
    selectMirror: true,
    dayMaxEvents: false, // Allow unlimited events per day to show overlaps
    eventOverlap: true, // Allow events to overlap
    selectOverlap: true, // Allow selecting over existing events
    height: "100%",
    aspectRatio: null,
    handleWindowResize: true,
    events: sampleEvents,
    displayEventTime: true,
    displayEventEnd: true,
    
    eventContent: function(arg) {
      const event = arg.event;
      const props = event.extendedProps;
      const branchName = props.branchName || '';
      const season = props.season || '';
      const bookingTitle = props.bookingTitle || '';
      const days = props.days || 0;
      const nights = props.nights || 0;
      const startDate = new Date(event.start);
      const endDate = event.end ? new Date(event.end) : startDate;
      
      const startStr = startDate.toLocaleDateString("en-US", { month: 'short', day: 'numeric' });
      const endStr = endDate.toLocaleDateString("en-US", { month: 'short', day: 'numeric' });
      const dateRange = `${startStr} - ${endStr}`;
      
      return {
        html: `
          <div class="fc-event-main-frame" style="padding: 4px 6px; display: flex; justify-content: space-between; align-items: center; gap: 8px;">
            <div class="fc-event-package" style="font-weight: 600; font-size: 0.75rem; white-space: nowrap; flex-shrink: 0;">
              ${season} | ${bookingTitle}
            </div>
            <div class="fc-event-details" style="font-size: 0.7rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; opacity: 0.95;">
              ${branchName} | ${days}D ${nights}N | ${dateRange}
            </div>
          </div>
        `
      };
    },
    
    dateClick: function (info) {
      selectedDate = info.date;
      renderTasksPanel(selectedDate);
      renderMiniCalendar();
      
      const taskPanel = document.querySelector('.calendar-task');
      if (taskPanel && window.innerWidth < 1024) {
        taskPanel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
      }
    },

    eventClick: function (info) {
      info.jsEvent.preventDefault();
      const event = info.event;
      editingEventId = event.id;
      openModalForEditing(event);
    },

    select: function (info) {
      selectedDate = info.start;
      const daysDiff = Math.ceil((info.end - info.start) / (1000 * 60 * 60 * 24));
      
      if (daysDiff > 1) {
        const endDate = new Date(info.end);
        endDate.setDate(endDate.getDate() - 1);
        openModal(info.start, endDate);
      } else {
        renderTasksPanel(selectedDate);
        renderMiniCalendar();
      }
      
      calendar.unselect();
    },

    datesSet: function (info) {
      const viewDate = calendar.getDate();
      currentMonthEl.textContent = viewDate.toLocaleDateString("en-US", {
        month: "long",
        year: "numeric",
      });
    },

    eventDidMount: function (info) {
      info.el.style.cursor = "pointer";
      const branchName = info.event.extendedProps.branchName || '';
      info.el.title = `${info.event.title}\n${branchName}\n${info.event.start.toLocaleString()}`;
    },
  });

  calendar.render();
  
  setupDoubleClickHandler();
  populateTimeDropdown(departureTimeInput);
  populateTimeDropdown(arrivalTimeInput);
  populateTimeDropdown(returnDepartureTimeInput);
  populateTimeDropdown(returnArrivalTimeInput);
  
  renderMiniCalendar();
  renderTasksPanel(selectedDate);
  setupDateTimeListeners();
});

/* =========================================================
   POPULATE BRANCH SELECT
========================================================= */
function populateBranchSelect() {
  branchSelect.innerHTML = '<option value="">Select Branch</option>';
  BRANCHES.forEach(branch => {
    const option = document.createElement('option');
    option.value = branch.id;
    option.textContent = branch.name;
    option.dataset.color = branch.color;
    branchSelect.appendChild(option);
  });
}

/* =========================================================
   DOUBLE-CLICK HANDLER
========================================================= */
function setupDoubleClickHandler() {
  const calendarEl = document.getElementById("calendar");
  
  calendarEl.addEventListener('dblclick', function(e) {
    const dayCell = e.target.closest('.fc-daygrid-day');
    
    if (dayCell) {
      const dateStr = dayCell.getAttribute('data-date');
      
      if (dateStr) {
        const clickedDate = new Date(dateStr + 'T00:00:00');
        selectedDate = clickedDate;
        openModal(clickedDate);
      }
    }
  });
}

/* =========================================================
   DATE/TIME CHANGE LISTENERS
========================================================= */
function setupDateTimeListeners() {
  // Departure date changes
  departureDateInput.addEventListener('change', function() {
    const depDate = departureDateInput.value;
    arrivalDateInput.min = depDate;
    
    if (arrivalDateInput.value && arrivalDateInput.value < depDate) {
      arrivalDateInput.value = depDate;
    }
    
    updateArrivalTimes();
    updateDaysNightsDisplay();
  });
  
  // Departure time changes
  departureTimeInput.addEventListener('change', function() {
    updateArrivalTimes();
  });
  
  // Arrival date changes
  arrivalDateInput.addEventListener('change', function() {
    const arrDate = arrivalDateInput.value;
    returnDepartureDateInput.min = arrDate;
    
    if (returnDepartureDateInput.value && returnDepartureDateInput.value < arrDate) {
      returnDepartureDateInput.value = arrDate;
    }
    
    updateArrivalTimes();
    updateReturnArrivalTimes();
  });
  
  // Arrival time changes
  arrivalTimeInput.addEventListener('change', function() {
    updateReturnDepartureTimes();
  });
  
  // Return departure date changes
  returnDepartureDateInput.addEventListener('change', function() {
    const retDepDate = returnDepartureDateInput.value;
    returnArrivalDateInput.min = retDepDate;
    
    if (returnArrivalDateInput.value && returnArrivalDateInput.value < retDepDate) {
      returnArrivalDateInput.value = retDepDate;
    }
    
    updateReturnDepartureTimes();
    updateReturnArrivalTimes();
    updateDaysNightsDisplay();
  });
  
  // Return departure time changes
  returnDepartureTimeInput.addEventListener('change', function() {
    updateReturnArrivalTimes();
  });
  
  // Return arrival date changes
  returnArrivalDateInput.addEventListener('change', function() {
    updateDaysNightsDisplay();
  });
}

function updateArrivalTimes() {
  const depDate = departureDateInput.value;
  const arrDate = arrivalDateInput.value;
  const depTime = departureTimeInput.value;
  
  let minTime = null;
  
  if (depDate && arrDate && depDate === arrDate && depTime) {
    const [hours, minutes] = depTime.split(':').map(Number);
    let nextMin = minutes === 0 ? 30 : 0;
    let nextHour = minutes === 0 ? hours : hours + 1;
    
    if (nextHour >= 24) {
      nextHour = 0;
    }
    
    minTime = `${nextHour.toString().padStart(2, '0')}:${nextMin.toString().padStart(2, '0')}`;
  }
  
  const currentValue = arrivalTimeInput.value;
  populateTimeDropdown(arrivalTimeInput, currentValue, minTime);
  
  if (minTime && (!currentValue || currentValue <= depTime)) {
    arrivalTimeInput.value = minTime;
  }
}

function updateReturnDepartureTimes() {
  const arrDate = arrivalDateInput.value;
  const retDepDate = returnDepartureDateInput.value;
  const arrTime = arrivalTimeInput.value;
  
  let minTime = null;
  
  if (arrDate && retDepDate && arrDate === retDepDate && arrTime) {
    const [hours, minutes] = arrTime.split(':').map(Number);
    let nextMin = minutes === 0 ? 30 : 0;
    let nextHour = minutes === 0 ? hours : hours + 1;
    
    if (nextHour >= 24) {
      nextHour = 0;
    }
    
    minTime = `${nextHour.toString().padStart(2, '0')}:${nextMin.toString().padStart(2, '0')}`;
  }
  
  const currentValue = returnDepartureTimeInput.value;
  populateTimeDropdown(returnDepartureTimeInput, currentValue, minTime);
  
  if (minTime && (!currentValue || currentValue <= arrTime)) {
    returnDepartureTimeInput.value = minTime;
  }
}

function updateReturnArrivalTimes() {
  const retDepDate = returnDepartureDateInput.value;
  const retArrDate = returnArrivalDateInput.value;
  const retDepTime = returnDepartureTimeInput.value;
  
  let minTime = null;
  
  if (retDepDate && retArrDate && retDepDate === retArrDate && retDepTime) {
    const [hours, minutes] = retDepTime.split(':').map(Number);
    let nextMin = minutes === 0 ? 30 : 0;
    let nextHour = minutes === 0 ? hours : hours + 1;
    
    if (nextHour >= 24) {
      nextHour = 0;
    }
    
    minTime = `${nextHour.toString().padStart(2, '0')}:${nextMin.toString().padStart(2, '0')}`;
  }
  
  const currentValue = returnArrivalTimeInput.value;
  populateTimeDropdown(returnArrivalTimeInput, currentValue, minTime);
  
  if (minTime && (!currentValue || currentValue <= retDepTime)) {
    returnArrivalTimeInput.value = minTime;
  }
}

/* =========================================================
   NAVIGATION EVENTS
========================================================= */
document.getElementById("prevMonth").addEventListener("click", () => {
  calendar.prev();
});

document.getElementById("nextMonth").addEventListener("click", () => {
  calendar.next();
});

document.getElementById("todayBtn").addEventListener("click", () => {
  calendar.today();
  selectedDate = new Date(TODAY);
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
  openModal(selectedDate);
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
  saveEvent();
});

/* =========================================================
   KEYBOARD SHORTCUTS
========================================================= */
document.addEventListener("keydown", (e) => {
  if (e.key === "Escape" && modalOverlay.classList.contains("active")) {
    closeModal();
  }

  if (e.target.tagName === "INPUT" || e.target.tagName === "TEXTAREA" || e.target.tagName === "SELECT") {
    return;
  }

  if (e.key === "c" || e.key === "C") {
    openModal(selectedDate);
  }

  if (e.key === "t" || e.key === "T") {
    calendar.today();
    selectedDate = new Date(TODAY);
    renderTasksPanel(selectedDate);
    renderMiniCalendar();
  }

  if (e.key === "ArrowLeft") {
    selectedDate.setDate(selectedDate.getDate() - 1);
    renderTasksPanel(selectedDate);
    calendar.gotoDate(selectedDate);
  }

  if (e.key === "ArrowRight") {
    selectedDate.setDate(selectedDate.getDate() + 1);
    renderTasksPanel(selectedDate);
    calendar.gotoDate(selectedDate);
  }
});

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

  for (let i = firstDay - 1; i >= 0; i--) {
    miniDays.appendChild(createMiniDay(prevDays - i, true, year, month - 1));
  }

  for (let d = 1; d <= daysInMonth; d++) {
    miniDays.appendChild(createMiniDay(d, false, year, month));
  }

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

  if (isSameDate(cellDate, TODAY)) {
    el.classList.add("today");
  }

  const hasEvents = calendar.getEvents().some((event) => {
    return isSameDate(new Date(event.start), cellDate);
  });

  if (hasEvents) {
    el.classList.add("has-events");
  }

  el.addEventListener("click", () => {
    const newDate = new Date(year, month, day);
    selectedDate = newDate;
    calendar.gotoDate(newDate);
    renderTasksPanel(selectedDate);
    renderMiniCalendar();
  });

  return el;
}

/* =========================================================
   TASKS PANEL
========================================================= */
function renderTasksPanel(date) {
  const dayEvents = calendar
    .getEvents()
    .filter((event) => {
      const eventStart = new Date(event.start);
      const eventEnd = event.end ? new Date(event.end) : eventStart;
      
      return date >= new Date(eventStart.toDateString()) && 
             date <= new Date(eventEnd.toDateString());
    })
    .map((event) => {
      const startDate = new Date(event.start);
      const endDate = event.end ? new Date(event.end) : startDate;
      const props = event.extendedProps;
      
      const isMultiDay = !isSameDay(startDate, endDate);
      
      return {
        id: event.id,
        title: event.title,
        bookingTitle: props.bookingTitle || '',
        branchName: props.branchName || '',
        season: props.season || '',
        days: props.days || 0,
        nights: props.nights || 0,
        departureDate: props.departureDate || '',
        departureTime: props.departureTime || '',
        arrivalDate: props.arrivalDate || '',
        arrivalTime: props.arrivalTime || '',
        returnDepartureDate: props.returnDepartureDate || '',
        returnDepartureTime: props.returnDepartureTime || '',
        returnArrivalDate: props.returnArrivalDate || '',
        returnArrivalTime: props.returnArrivalTime || '',
        color: event.backgroundColor,
        isMultiDay: isMultiDay
      };
    });

  const isToday = isSameDate(date, TODAY);
  const dateStr = isToday
    ? "Today"
    : date.toLocaleDateString("en-US", {
        month: "short",
        day: "numeric",
      });
  tasksTitle.textContent = `${dateStr}'s Bookings`;

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
        <p>No bookings scheduled</p>
      </div>
    `;
    return;
  }

  dayEvents.forEach((evt) => {
    const taskItem = document.createElement("div");
    taskItem.className = "calendar-task-item";
    taskItem.style.borderLeftColor = evt.color;

    taskItem.innerHTML = `
      <div class="calendar-task-header-info">
        <div class="calendar-task-name">${evt.season} | ${evt.bookingTitle}</div>
        <div class="calendar-task-branch">${evt.branchName} • ${evt.days}D ${evt.nights}N</div>
      </div>
      <div class="calendar-task-time-info">
        <div class="calendar-task-time">
          <strong>Outbound:</strong> ${evt.departureDate} ${evt.departureTime} → ${evt.arrivalDate} ${evt.arrivalTime}
        </div>
        <div class="calendar-task-time">
          <strong>Return:</strong> ${evt.returnDepartureDate} ${evt.returnDepartureTime} → ${evt.returnArrivalDate} ${evt.returnArrivalTime}
        </div>
      </div>
      ${evt.isMultiDay ? '<div class="calendar-task-badge">Multi-day</div>' : ''}
      <div class="calendar-task-actions">
        <button class="calendar-task-delete" data-id="${evt.id}">Delete</button>
      </div>
    `;

    taskItem.addEventListener("click", (e) => {
      if (!e.target.classList.contains("calendar-task-delete")) {
        const event = calendar.getEventById(evt.id);
        if (event) {
          editingEventId = evt.id;
          openModalForEditing(event);
        }
      }
    });

    const deleteBtn = taskItem.querySelector(".calendar-task-delete");
    deleteBtn.addEventListener("click", (e) => {
      e.stopPropagation();
      if (confirm('Are you sure you want to delete this booking?')) {
        deleteEvent(evt.id);
      }
    });

    tasksList.appendChild(taskItem);
  });
}

/* =========================================================
   MODAL LOGIC
========================================================= */
function openModal(startDate = null) {
  editingEventId = null;
  
  const start = startDate || selectedDate;
  
  modalDate.textContent = "Create New Booking";
  
  // Set default departure date
  departureDateInput.value = formatDateForInput(start);
  arrivalDateInput.value = formatDateForInput(start);
  
  // Set default return dates (next day)
  const nextDay = new Date(start);
  nextDay.setDate(nextDay.getDate() + 1);
  returnDepartureDateInput.value = formatDateForInput(nextDay);
  returnArrivalDateInput.value = formatDateForInput(nextDay);
  
  // Set minimum dates
  const todayStr = formatDateForInput(TODAY);
  departureDateInput.min = todayStr;
  arrivalDateInput.min = formatDateForInput(start);
  returnDepartureDateInput.min = formatDateForInput(start);
  returnArrivalDateInput.min = formatDateForInput(nextDay);
  
  // Set default times
  departureTimeInput.value = "08:00";
  arrivalTimeInput.value = "12:00";
  returnDepartureTimeInput.value = "14:00";
  returnArrivalTimeInput.value = "18:00";
  
  // Clear form
  bookingTitleInput.value = "";
  seasonSelect.value = "";
  branchSelect.value = "";
  eventDescriptionInput.value = "";
  
  // Update days/nights display
  updateDaysNightsDisplay();
  
  // Render events for selected date
  renderModalEventsList(start);
  
  modalOverlay.classList.add("active");
  document.body.style.overflow = "hidden";
  
  setTimeout(() => {
    if (bookingTitleInput) bookingTitleInput.focus();
  }, 100);
}

function openModalForEditing(event) {
  const props = event.extendedProps;
  
  modalDate.textContent = "Edit Booking";
  
  // Set dates
  departureDateInput.value = props.departureDate;
  arrivalDateInput.value = props.arrivalDate;
  returnDepartureDateInput.value = props.returnDepartureDate;
  returnArrivalDateInput.value = props.returnArrivalDate;
  
  // Set times
  populateTimeDropdown(departureTimeInput, props.departureTime);
  populateTimeDropdown(arrivalTimeInput, props.arrivalTime);
  populateTimeDropdown(returnDepartureTimeInput, props.returnDepartureTime);
  populateTimeDropdown(returnArrivalTimeInput, props.returnArrivalTime);
  
  // Set other fields
  bookingTitleInput.value = props.bookingTitle || "";
  seasonSelect.value = props.season;
  branchSelect.value = props.branchId;
  eventDescriptionInput.value = props.description || "";
  
  // Update days/nights display
  updateDaysNightsDisplay();
  
  // Render events for the event's start date
  const eventDate = new Date(event.start);
  renderModalEventsList(eventDate);
  
  modalOverlay.classList.add("active");
  document.body.style.overflow = "hidden";
  
  setTimeout(() => {
    if (bookingTitleInput) bookingTitleInput.focus();
  }, 100);
}

function closeModal() {
  modalOverlay.classList.remove("active");
  eventForm.reset();
  editingEventId = null;
  document.body.style.overflow = "";
}

/* =========================================================
   SAVE EVENT
========================================================= */
function saveEvent() {
  const bookingTitle = bookingTitleInput.value.trim();
  const season = seasonSelect.value;
  const branchId = parseInt(branchSelect.value);
  const description = eventDescriptionInput.value.trim();
  
  const departureDate = departureDateInput.value;
  const departureTime = departureTimeInput.value;
  const arrivalDate = arrivalDateInput.value;
  const arrivalTime = arrivalTimeInput.value;
  const returnDepartureDate = returnDepartureDateInput.value;
  const returnDepartureTime = returnDepartureTimeInput.value;
  const returnArrivalDate = returnArrivalDateInput.value;
  const returnArrivalTime = returnArrivalTimeInput.value;

  if (!bookingTitle || !season || !branchId || !departureDate || !departureTime || !arrivalDate || !arrivalTime || 
      !returnDepartureDate || !returnDepartureTime || !returnArrivalDate || !returnArrivalTime) {
    alert('Please fill in all required fields');
    return;
  }

  // Find branch
  const branch = BRANCHES.find(b => b.id === branchId);
  if (!branch) {
    alert('Invalid branch selected');
    return;
  }

  // Create datetime strings
  const outboundDeparture = new Date(`${departureDate}T${departureTime}:00`);
  const outboundArrival = new Date(`${arrivalDate}T${arrivalTime}:00`);
  const returnDeparture = new Date(`${returnDepartureDate}T${returnDepartureTime}:00`);
  const returnArrival = new Date(`${returnArrivalDate}T${returnArrivalTime}:00`);
  
  // Validate times
  if (outboundArrival <= outboundDeparture) {
    alert('Outbound arrival must be after departure');
    return;
  }
  
  if (returnDeparture <= outboundArrival) {
    alert('Return departure must be after outbound arrival');
    return;
  }
  
  if (returnArrival <= returnDeparture) {
    alert('Return arrival must be after return departure');
    return;
  }

  // Calculate days and nights
  const { days, nights } = calculateDaysNights(departureDate, returnArrivalDate);

  const title = `${season} - ${branch.name}`;

  if (editingEventId) {
    // Update existing event
    const event = calendar.getEventById(editingEventId);
    if (event) {
      event.setProp('title', title);
      event.setStart(outboundDeparture);
      event.setEnd(returnArrival);
      event.setProp('backgroundColor', branch.color);
      event.setProp('borderColor', branch.color);
      event.setExtendedProp('bookingTitle', bookingTitle);
      event.setExtendedProp('season', season);
      event.setExtendedProp('branchId', branchId);
      event.setExtendedProp('branchName', branch.name);
      event.setExtendedProp('departureDate', departureDate);
      event.setExtendedProp('departureTime', departureTime);
      event.setExtendedProp('arrivalDate', arrivalDate);
      event.setExtendedProp('arrivalTime', arrivalTime);
      event.setExtendedProp('returnDepartureDate', returnDepartureDate);
      event.setExtendedProp('returnDepartureTime', returnDepartureTime);
      event.setExtendedProp('returnArrivalDate', returnArrivalDate);
      event.setExtendedProp('returnArrivalTime', returnArrivalTime);
      event.setExtendedProp('days', days);
      event.setExtendedProp('nights', nights);
      event.setExtendedProp('description', description);
    }
  } else {
    // Add new event
    calendar.addEvent({
      id: Date.now().toString(),
      title: title,
      start: outboundDeparture.toISOString(),
      end: returnArrival.toISOString(),
      backgroundColor: branch.color,
      borderColor: branch.color,
      extendedProps: {
        bookingTitle: bookingTitle,
        season: season,
        branchId: branchId,
        branchName: branch.name,
        departureDate: departureDate,
        departureTime: departureTime,
        arrivalDate: arrivalDate,
        arrivalTime: arrivalTime,
        returnDepartureDate: returnDepartureDate,
        returnDepartureTime: returnDepartureTime,
        returnArrivalDate: returnArrivalDate,
        returnArrivalTime: returnArrivalTime,
        days: days,
        nights: nights,
        description: description
      }
    });
  }

  closeModal();
  renderTasksPanel(selectedDate);
  renderMiniCalendar();
}

/* =========================================================
   RENDER EVENTS LIST IN MODAL (FOR SELECTED DATE)
========================================================= */
function renderModalEventsList(date) {
  // Get all events that overlap with the selected date
  const dateEvents = calendar
    .getEvents()
    .filter((event) => {
      const eventStart = new Date(event.start);
      const eventEnd = event.end ? new Date(event.end) : eventStart;
      
      return date >= new Date(eventStart.toDateString()) && 
             date <= new Date(eventEnd.toDateString());
    })
    .sort((a, b) => new Date(a.start) - new Date(b.start))
    .map((event) => {
      const props = event.extendedProps;
      
      return {
        id: event.id,
        title: event.title,
        bookingTitle: props.bookingTitle || '',
        branchName: props.branchName || '',
        season: props.season || '',
        days: props.days || 0,
        nights: props.nights || 0,
        departureDate: props.departureDate || '',
        departureTime: props.departureTime || '',
        arrivalDate: props.arrivalDate || '',
        arrivalTime: props.arrivalTime || '',
        returnDepartureDate: props.returnDepartureDate || '',
        returnDepartureTime: props.returnDepartureTime || '',
        returnArrivalDate: props.returnArrivalDate || '',
        returnArrivalTime: props.returnArrivalTime || '',
        description: props.description || '',
        color: event.backgroundColor,
        isPast: new Date(event.end || event.start) < new Date()
      };
    });

  if (!dateEvents.length) {
    eventsList.innerHTML = `<div class="calendar-empty-state">No bookings for this date</div>`;
    return;
  }

  eventsList.innerHTML = "";

  dateEvents.forEach((evt) => {
    const el = document.createElement("div");
    el.className = "calendar-event-item";
    if (evt.isPast) {
      el.classList.add("past-event");
    }
    el.style.borderLeftColor = evt.color;

    el.innerHTML = `
      <div class="calendar-event-title">${evt.season} | ${evt.bookingTitle}</div>
      <div class="calendar-event-details">
        <div class="calendar-event-detail-row">
          <strong>Branch:</strong> ${evt.branchName}
        </div>
        <div class="calendar-event-detail-row">
          <strong>Duration:</strong> ${evt.days}D ${evt.nights}N
        </div>
        <div class="calendar-event-detail-row">
          <strong>Outbound:</strong> ${evt.departureDate} ${evt.departureTime} → ${evt.arrivalDate} ${evt.arrivalTime}
        </div>
        <div class="calendar-event-detail-row">
          <strong>Return:</strong> ${evt.returnDepartureDate} ${evt.returnDepartureTime} → ${evt.returnArrivalDate} ${evt.returnArrivalTime}
        </div>
        ${evt.description ? `<div class="calendar-event-detail-row"><strong>Notes:</strong> ${evt.description}</div>` : ''}
      </div>
      ${evt.isPast ? '<span class="calendar-event-badge past">Past</span>' : ''}
      <button class="calendar-delete-event" data-id="${evt.id}">Delete</button>
    `;

    el.addEventListener('click', (e) => {
      if (!e.target.classList.contains('calendar-delete-event')) {
        const event = calendar.getEventById(evt.id);
        if (event) {
          editingEventId = evt.id;
          closeModal();
          setTimeout(() => openModalForEditing(event), 100);
        }
      }
    });

    el.querySelector(".calendar-delete-event").onclick = (e) => {
      e.stopPropagation();
      if (confirm('Are you sure you want to delete this booking?')) {
        deleteEvent(evt.id);
      }
    };

    eventsList.appendChild(el);
  });
}

/* =========================================================
   DELETE EVENT
========================================================= */
function deleteEvent(eventId) {
  const event = calendar.getEventById(eventId);
  if (event) {
    event.remove();
  }

  renderTasksPanel(selectedDate);
  renderMiniCalendar();
  
  if (modalOverlay.classList.contains("active")) {
    renderModalEventsList(selectedDate);
  }
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