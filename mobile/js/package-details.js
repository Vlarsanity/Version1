// ================================
// PACKAGE DETAILS PAGE JAVASCRIPT
// ================================

// Get package ID from URL or localStorage
function getPackageId() {
  // Check URL hash first (e.g., #package/seoul-city-explorer)
  const hash = window.location.hash;
  if (hash.includes('package/')) {
    return hash.split('package/')[1];
  }
  
  // Check localStorage as fallback
  return localStorage.getItem('currentPackageId') || 'seoul-city-explorer';
}

// Load package data
function loadPackageData() {
  const packageId = getPackageId();
  const packageData = packagesData[packageId];
  
  if (!packageData) {
    console.error('Package not found:', packageId);
    window.location.href = 'index.html'; // Redirect to home
    return;
  }
  
  // Populate page with package data
  populatePackageDetails(packageData);
  renderCalendar(packageData);
  renderReviews(packageData);
  renderRelatedPackages(packageData);
}

// Populate package details
function populatePackageDetails(data) {
  // Hero section
  document.getElementById('heroImage').src = data.images.hero;
  document.getElementById('heroImage').alt = data.title;
  document.getElementById('packageTitle').textContent = data.title;
  document.getElementById('packageSubtitle').textContent = data.subtitle;
  document.getElementById('packageDuration').textContent = `${data.duration.days} Days / ${data.duration.nights} Nights`;
  document.getElementById('packageRating').textContent = `${data.rating.average} (${data.rating.total} reviews)`;
  
  // Badge
  if (data.featured) {
    document.querySelector('.package-badge-hero').style.display = 'inline-block';
  } else {
    document.querySelector('.package-badge-hero').style.display = 'none';
  }
  
  // Quick info cards
  document.getElementById('infoDuration').textContent = `${data.duration.days} Days / ${data.duration.nights} Nights`;
  document.getElementById('infoGroupSize').textContent = `${data.requirements.minPeople}-${data.requirements.maxPeople} people`;
  document.getElementById('infoLocation').textContent = data.category.charAt(0).toUpperCase() + data.category.slice(1);
  
  // Overview
  document.getElementById('overviewText').textContent = data.overview.description;
  
  // Highlights
  const highlightsList = document.getElementById('highlightsList');
  highlightsList.innerHTML = data.overview.highlights.map(highlight => 
    `<li><i class="fas fa-check-circle"></i> ${highlight}</li>`
  ).join('');
  
  // Itinerary
  const itineraryList = document.getElementById('itineraryList');
  itineraryList.innerHTML = data.itinerary.map(day => `
    <div class="itinerary-day">
      <div class="day-header">
        <div class="day-number">Day ${day.day}</div>
        <h3>${day.title}</h3>
      </div>
      <ul class="day-activities">
        ${day.activities.map(activity => `<li>${activity}</li>`).join('')}
      </ul>
      <div class="day-footer">
        <span class="day-meals"><i class="fas fa-utensils"></i> ${day.meals.join(', ')}</span>
        ${day.accommodation ? `<span class="day-accommodation"><i class="fas fa-bed"></i> ${day.accommodation}</span>` : ''}
      </div>
    </div>
  `).join('');
  
  // Inclusions
  const inclusionsList = document.getElementById('inclusionsList');
  inclusionsList.innerHTML = data.inclusions.map(item => `<li>${item}</li>`).join('');
  
  // Exclusions
  const exclusionsList = document.getElementById('exclusionsList');
  exclusionsList.innerHTML = data.exclusions.map(item => `<li>${item}</li>`).join('');
  
  // Rating summary
  document.getElementById('ratingScore').textContent = data.rating.average;
  document.getElementById('ratingCount').textContent = `Based on ${data.rating.total} reviews`;
  
  // Sticky price
  document.getElementById('stickyPrice').textContent = `₱${data.price.amount.toLocaleString()}`;
  
  // Booking summary
  document.getElementById('minDeparture').textContent = `${data.requirements.minPeople} people`;
}

// ================================
// CALENDAR FUNCTIONALITY
// ================================

let currentMonth = 4; // May (0-indexed)
let currentYear = 2026;
let selectedDate = null;

function renderCalendar(packageData) {
  const calendarGrid = document.querySelector('.calendar-grid');
  
  // Update month display
  const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 
                     'July', 'August', 'September', 'October', 'November', 'December'];
  document.getElementById('calendarMonth').textContent = `${monthNames[currentMonth]} ${currentYear}`;
  
  // Clear existing days (keep headers)
  const dayHeaders = calendarGrid.querySelectorAll('.calendar-day-header');
  calendarGrid.innerHTML = '';
  dayHeaders.forEach(header => calendarGrid.appendChild(header));
  
  // Get first day of month and number of days
  const firstDay = new Date(currentYear, currentMonth, 1).getDay();
  const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
  
  // Add empty cells for days before month starts
  for (let i = 0; i < firstDay; i++) {
    const emptyDay = document.createElement('div');
    emptyDay.classList.add('calendar-day', 'disabled');
    calendarGrid.appendChild(emptyDay);
  }
  
  // Add days of the month
  for (let day = 1; day <= daysInMonth; day++) {
    const dateStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
    const availability = packageData.availability[dateStr];
    
    const dayElement = document.createElement('div');
    dayElement.classList.add('calendar-day');
    
    if (availability && availability.available) {
      dayElement.innerHTML = `
        <div class="calendar-day-number">${day}</div>
        <div class="calendar-day-price">₱${(availability.price / 1000).toFixed(0)}K</div>
      `;
      dayElement.addEventListener('click', () => selectDate(dateStr, availability, packageData));
    } else {
      dayElement.classList.add('sold-out');
      dayElement.innerHTML = `
        <div class="calendar-day-number">${day}</div>
        <div class="calendar-day-label">Sold Out</div>
      `;
    }
    
    calendarGrid.appendChild(dayElement);
  }
}

function selectDate(dateStr, availability, packageData) {
  selectedDate = dateStr;
  
  // Update UI
  document.querySelectorAll('.calendar-day').forEach(day => {
    day.classList.remove('selected');
  });
  event.currentTarget.classList.add('selected');
  
  // Update booking slots
  document.getElementById('bookingSlots').textContent = availability.slots;
  
  // Show selected date info
  const selectedDateInfo = document.getElementById('selectedDateInfo');
  selectedDateInfo.style.display = 'block';
  
  // Format date display (simplified - in real app, calculate based on duration)
  const date = new Date(dateStr);
  const returnDate = new Date(date);
  returnDate.setDate(returnDate.getDate() + packageData.duration.days);
  
  document.getElementById('departureDate').textContent = 
    `${date.toLocaleDateString('en-US', {month: 'short', day: 'numeric'})} - Flight Info TBA`;
  document.getElementById('returnDate').textContent = 
    `${returnDate.toLocaleDateString('en-US', {month: 'short', day: 'numeric'})} - Flight Info TBA`;
}

// Calendar navigation
document.getElementById('prevMonth')?.addEventListener('click', () => {
  currentMonth--;
  if (currentMonth < 0) {
    currentMonth = 11;
    currentYear--;
  }
  const packageData = packagesData[getPackageId()];
  renderCalendar(packageData);
});

document.getElementById('nextMonth')?.addEventListener('click', () => {
  currentMonth++;
  if (currentMonth > 11) {
    currentMonth = 0;
    currentYear++;
  }
  const packageData = packagesData[getPackageId()];
  renderCalendar(packageData);
});

// ================================
// REVIEWS FUNCTIONALITY
// ================================

function renderReviews(data) {
  const reviewsList = document.getElementById('reviewsList');
  
  // Render first 3 reviews
  const reviewsToShow = data.reviews.slice(0, 3);
  reviewsList.innerHTML = reviewsToShow.map(review => `
    <div class="review-card">
      <div class="review-header">
        <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(review.userName)}&background=4f46e5&color=fff" 
             alt="${review.userName}" 
             class="review-avatar">
        <div class="review-user-info">
          <h4>
            ${review.userName} 
            ${review.verified ? '<i class="fas fa-check-circle verified"></i>' : ''}
          </h4>
          <div class="review-stars">
            ${generateStars(review.rating)}
          </div>
        </div>
        <span class="review-date">${formatDate(review.date)}</span>
      </div>
      <h5 class="review-title">${review.title}</h5>
      <p class="review-text">${review.comment}</p>
      <div class="review-footer">
        <button class="review-helpful-btn">
          <i class="far fa-thumbs-up"></i>
          Helpful (${review.helpful})
        </button>
      </div>
    </div>
  `).join('');
}

function generateStars(rating) {
  const fullStars = Math.floor(rating);
  const hasHalfStar = rating % 1 !== 0;
  let stars = '';
  
  for (let i = 0; i < fullStars; i++) {
    stars += '<i class="fas fa-star"></i>';
  }
  if (hasHalfStar) {
    stars += '<i class="fas fa-star-half-alt"></i>';
  }
  const emptyStars = 5 - Math.ceil(rating);
  for (let i = 0; i < emptyStars; i++) {
    stars += '<i class="far fa-star"></i>';
  }
  
  return stars;
}

function formatDate(dateStr) {
  const date = new Date(dateStr);
  return date.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
}

// ================================
// RELATED PACKAGES
// ================================

function renderRelatedPackages(data) {
  const relatedGrid = document.getElementById('relatedPackages');
  
  const relatedPackagesData = data.relatedPackages
    .map(id => packagesData[id])
    .filter(pkg => pkg); // Filter out undefined packages
  
  relatedGrid.innerHTML = relatedPackagesData.map(pkg => `
    <div class="related-package-card" onclick="viewPackage('${pkg.id}')">
      <img src="${pkg.images.hero}" alt="${pkg.title}">
      <div class="related-package-info">
        <h4>${pkg.title}</h4>
        <div class="related-package-meta">
          <span><i class="far fa-clock"></i> ${pkg.duration.days}D/${pkg.duration.nights}N</span>
          <span class="related-package-price">₱${pkg.price.amount.toLocaleString()}</span>
        </div>
      </div>
    </div>
  `).join('');
}

function viewPackage(packageId) {
  localStorage.setItem('currentPackageId', packageId);
  window.location.hash = `package/${packageId}`;
  window.location.reload(); // Reload to load new package data
}

// ================================
// NAVIGATION & ACTIONS
// ================================

function goBack() {
  if (window.history.length > 1) {
    window.history.back();
  } else {
    window.location.href = 'index.html';
  }
}

// Favorite toggle
document.querySelector('.hero-favorite-btn')?.addEventListener('click', function() {
  this.classList.toggle('active');
  
  // Save to localStorage (in real app, save to database)
  const packageId = getPackageId();
  const favorites = JSON.parse(localStorage.getItem('favorites') || '[]');
  
  if (this.classList.contains('active')) {
    if (!favorites.includes(packageId)) {
      favorites.push(packageId);
    }
  } else {
    const index = favorites.indexOf(packageId);
    if (index > -1) {
      favorites.splice(index, 1);
    }
  }
  
  localStorage.setItem('favorites', JSON.stringify(favorites));
});

// Check if package is already favorited
function checkFavoriteStatus() {
  const packageId = getPackageId();
  const favorites = JSON.parse(localStorage.getItem('favorites') || '[]');
  
  if (favorites.includes(packageId)) {
    document.querySelector('.hero-favorite-btn')?.classList.add('active');
  }
}

// Book Now button
document.getElementById('bookNowBtn')?.addEventListener('click', () => {
  if (selectedDate) {
    // In real app, proceed to booking form
    alert(`Booking for ${selectedDate}. Proceeding to checkout...`);
    // window.location.href = `booking.html?package=${getPackageId()}&date=${selectedDate}`;
  } else {
    alert('Please select a date from the calendar first.');
  }
});

// ================================
// SMOOTH SCROLL FOR ANCHOR LINKS
// ================================

document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', function (e) {
    e.preventDefault();
    const target = document.querySelector(this.getAttribute('href'));
    if (target) {
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  });
});

// ================================
// INITIALIZE PAGE
// ================================

document.addEventListener('DOMContentLoaded', () => {
  loadPackageData();
  checkFavoriteStatus();
  
  // Update sticky bar on scroll
  let lastScroll = 0;
  window.addEventListener('scroll', () => {
    const currentScroll = window.pageYOffset;
    const stickyBar = document.querySelector('.sticky-booking-bar');
    
    if (currentScroll > lastScroll && currentScroll > 100) {
      // Scrolling down
      stickyBar.style.transform = 'translateY(100%)';
    } else {
      // Scrolling up
      stickyBar.style.transform = 'translateY(0)';
    }
    
    lastScroll = currentScroll;
  });
});

// ================================
// INTEGRATION WITH LANDING PAGE
// ================================

// Function to be called from landing page when clicking "View Details"
function openPackageDetails(packageId) {
  localStorage.setItem('currentPackageId', packageId);
  window.location.href = `package-details.html#package/${packageId}`;
}

// Export for use in other files
if (typeof module !== 'undefined' && module.exports) {
  module.exports = { openPackageDetails, viewPackage };
}