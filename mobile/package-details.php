<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Package Details - Smart Escape</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Styles -->
    <link rel="stylesheet" href="../mobile/assets/css/root.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="../mobile/assets/css/mobile.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="../mobile/assets/css/package-details.css?v=<?= time(); ?>">
</head>
<body>

    <!-- Package Details Container -->
    <div class="package-details-page">
        
        <!-- Hero Section with Image -->
        <div class="package-hero">
            <img src="https://images.unsplash.com/photo-1517154421773-0529f29ea451?w=1200&h=600&fit=crop" 
                 alt="Seoul City Explorer" 
                 class="hero-image" 
                 id="heroImage">
            
            <!-- Dark Gradient Overlay -->
            <div class="hero-overlay"></div>
            
            <!-- Back Button -->
            <button class="hero-back-btn" onclick="goBack()" aria-label="Go back">
                <i class="fas fa-arrow-left"></i>
            </button>
            
            <!-- Favorite Button -->
            <button class="hero-favorite-btn" aria-label="Add to favorites">
                <i class="far fa-heart"></i>
            </button>
            
            <!-- Package Title (Offset at bottom) -->
            <div class="hero-content">
                <div class="package-badge-hero">Featured</div>
                <h1 class="package-title-hero" id="packageTitle">Seoul City Explorer</h1>
                <p class="package-subtitle-hero" id="packageSubtitle">Discover the heart of South Korea</p>
                
                <div class="package-meta-hero">
                    <span class="meta-item">
                        <i class="far fa-clock"></i>
                        <span id="packageDuration">5 Days / 4 Nights</span>
                    </span>
                    <span class="meta-item">
                        <i class="fas fa-star"></i>
                        <span id="packageRating">4.8 (156 reviews)</span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="package-content">
            
            <!-- Quick Info Cards -->
            <div class="quick-info-grid">
                <div class="info-card">
                    <i class="fas fa-calendar-alt"></i>
                    <div>
                        <h4>Duration</h4>
                        <p id="infoDuration">5 Days / 4 Nights</p>
                    </div>
                </div>
                
                <div class="info-card">
                    <i class="fas fa-users"></i>
                    <div>
                        <h4>Group Size</h4>
                        <p id="infoGroupSize">2-40 people</p>
                    </div>
                </div>
                
                <div class="info-card">
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <h4>Location</h4>
                        <p id="infoLocation">Seoul, Korea</p>
                    </div>
                </div>
            </div>

            <!-- Overview Section -->
            <section class="content-section">
                <h2 class="section-title">Overview</h2>
                <p class="overview-text" id="overviewText">
                    Experience the vibrant culture and modern marvels of Seoul. From ancient palaces to cutting-edge technology, this 5-day journey will immerse you in the perfect blend of tradition and innovation that makes Seoul one of Asia's most exciting destinations.
                </p>
                
                <h3 class="subsection-title">Highlights</h3>
                <ul class="highlights-list" id="highlightsList">
                    <li><i class="fas fa-check-circle"></i> Visit Gyeongbokgung Palace and witness the changing of the guard</li>
                    <li><i class="fas fa-check-circle"></i> Explore trendy Gangnam district and K-pop culture</li>
                    <li><i class="fas fa-check-circle"></i> Experience traditional Korean BBQ and street food tours</li>
                    <li><i class="fas fa-check-circle"></i> Shopping spree in Myeongdong and Dongdaemun</li>
                    <li><i class="fas fa-check-circle"></i> Scenic views from N Seoul Tower</li>
                    <li><i class="fas fa-check-circle"></i> Traditional tea ceremony experience</li>
                </ul>
            </section>

            <!-- Itinerary Section -->
            <section class="content-section">
                <h2 class="section-title">Itinerary</h2>
                
                <div class="itinerary-list" id="itineraryList">
                    <!-- Day 1 -->
                    <div class="itinerary-day">
                        <div class="day-header">
                            <div class="day-number">Day 1</div>
                            <h3>Arrival & City Orientation</h3>
                        </div>
                        <ul class="day-activities">
                            <li>Airport pickup and hotel check-in</li>
                            <li>Welcome dinner at traditional Korean restaurant</li>
                            <li>Evening stroll in Hongdae district</li>
                            <li>Hotel rest</li>
                        </ul>
                        <div class="day-footer">
                            <span class="day-meals"><i class="fas fa-utensils"></i> Dinner</span>
                            <span class="day-accommodation"><i class="fas fa-bed"></i> 4-star hotel</span>
                        </div>
                    </div>

                    <!-- More days will be dynamically added -->
                </div>
            </section>

            <!-- Inclusions & Exclusions -->
            <section class="content-section">
                <h2 class="section-title">What's Included</h2>
                
                <div class="inclusion-grid">
                    <div class="inclusion-card">
                        <h3><i class="fas fa-check-circle"></i> Inclusions</h3>
                        <ul id="inclusionsList">
                            <li>Round-trip airport transfers</li>
                            <li>4 nights accommodation in 4-star hotel</li>
                            <li>Daily breakfast</li>
                            <li>Selected meals as per itinerary</li>
                            <li>English-speaking tour guide</li>
                            <li>All entrance fees and activities</li>
                        </ul>
                    </div>
                    
                    <div class="inclusion-card exclusions">
                        <h3><i class="fas fa-times-circle"></i> Exclusions</h3>
                        <ul id="exclusionsList">
                            <li>International airfare</li>
                            <li>Personal expenses</li>
                            <li>Meals not mentioned in itinerary</li>
                            <li>Optional tours and activities</li>
                            <li>Tips and gratuities</li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Booking Calendar Section -->
            <section class="content-section">
                <h2 class="section-title">Available for Booking</h2>
                
                <div class="booking-calendar-wrapper">
                    <!-- Calendar Header -->
                    <div class="calendar-header">
                        <button class="calendar-nav-btn" id="prevMonth" aria-label="Previous month">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <h3 class="calendar-month" id="calendarMonth">May 2026</h3>
                        <button class="calendar-nav-btn" id="nextMonth" aria-label="Next month">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                    
                    <!-- Calendar Grid -->
                    <div class="calendar-grid">
                        <!-- Day headers -->
                        <div class="calendar-day-header">Sun</div>
                        <div class="calendar-day-header">Mon</div>
                        <div class="calendar-day-header">Tue</div>
                        <div class="calendar-day-header">Wed</div>
                        <div class="calendar-day-header">Thu</div>
                        <div class="calendar-day-header">Fri</div>
                        <div class="calendar-day-header">Sat</div>
                        
                        <!-- Calendar days will be dynamically generated -->
                    </div>
                    
                    <!-- Booking Summary -->
                    <div class="booking-summary" id="bookingSummary">
                        <div class="summary-item">
                            <i class="fas fa-users"></i>
                            <span>Booking <strong id="bookingSlots">16</strong> / 40</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Minimum Departure:</span>
                            <span><strong id="minDeparture">5 people</strong></span>
                        </div>
                    </div>
                    
                    <!-- Selected Date Info -->
                    <div class="selected-date-info" id="selectedDateInfo" style="display: none;">
                        <div class="date-info-header">
                            <i class="fas fa-plane-departure"></i>
                            <div>
                                <p class="info-label">Departure Date</p>
                                <p class="info-value" id="departureDate">19:35 - 5J188 (Manila (MNL) → Incheon (ICN))</p>
                            </div>
                        </div>
                        <div class="date-info-header">
                            <i class="fas fa-plane-arrival"></i>
                            <div>
                                <p class="info-label">Return Date</p>
                                <p class="info-value" id="returnDate">02:20 - 5J187 (Incheon (ICN) → Manila (MNL))</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Reviews Section -->
            <section class="content-section">
                <div class="section-header-with-action">
                    <h2 class="section-title">Reviews</h2>
                    <a href="#" class="view-all-link">View all reviews</a>
                </div>
                
                <!-- Rating Summary -->
                <div class="rating-summary">
                    <div class="rating-score">
                        <div class="score-big" id="ratingScore">4.8</div>
                        <div class="rating-stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                        <p class="rating-count" id="ratingCount">Based on 156 reviews</p>
                    </div>
                    
                    <div class="rating-breakdown">
                        <div class="rating-bar-item">
                            <span>5★</span>
                            <div class="rating-bar">
                                <div class="rating-bar-fill" style="width: 63%"></div>
                            </div>
                            <span>98</span>
                        </div>
                        <div class="rating-bar-item">
                            <span>4★</span>
                            <div class="rating-bar">
                                <div class="rating-bar-fill" style="width: 29%"></div>
                            </div>
                            <span>45</span>
                        </div>
                        <div class="rating-bar-item">
                            <span>3★</span>
                            <div class="rating-bar">
                                <div class="rating-bar-fill" style="width: 6%"></div>
                            </div>
                            <span>10</span>
                        </div>
                        <div class="rating-bar-item">
                            <span>2★</span>
                            <div class="rating-bar">
                                <div class="rating-bar-fill" style="width: 1%"></div>
                            </div>
                            <span>2</span>
                        </div>
                        <div class="rating-bar-item">
                            <span>1★</span>
                            <div class="rating-bar">
                                <div class="rating-bar-fill" style="width: 1%"></div>
                            </div>
                            <span>1</span>
                        </div>
                    </div>
                </div>
                
                <!-- Review Cards -->
                <div class="reviews-list" id="reviewsList">
                    <!-- Review 1 -->
                    <div class="review-card">
                        <div class="review-header">
                            <img src="https://ui-avatars.com/api/?name=Maria+Santos&background=4f46e5&color=fff" alt="Maria Santos" class="review-avatar">
                            <div class="review-user-info">
                                <h4>Maria Santos <i class="fas fa-check-circle verified"></i></h4>
                                <div class="review-stars">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                            <span class="review-date">March 15, 2024</span>
                        </div>
                        <h5 class="review-title">Amazing experience!</h5>
                        <p class="review-text">Seoul exceeded all my expectations. The tour was well-organized and our guide was incredibly knowledgeable. The blend of modern and traditional culture was fascinating.</p>
                        <div class="review-footer">
                            <button class="review-helpful-btn">
                                <i class="far fa-thumbs-up"></i>
                                Helpful (24)
                            </button>
                        </div>
                    </div>
                    
                    <!-- More reviews dynamically added -->
                </div>
            </section>

            <!-- Related Packages -->
            <section class="content-section">
                <h2 class="section-title">You might also like</h2>
                
                <div class="related-packages-grid" id="relatedPackages">
                    <!-- Related package cards -->
                    <div class="related-package-card">
                        <img src="https://images.unsplash.com/photo-1509023464722-18d996393ca8?w=400&h=300&fit=crop" alt="Busan Beach">
                        <div class="related-package-info">
                            <h4>Busan Beach</h4>
                            <div class="related-package-meta">
                                <span><i class="far fa-clock"></i> 4D/3N</span>
                                <span class="related-package-price">₱24,999</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- More cards dynamically added -->
                </div>
            </section>

        </div>

        <!-- Sticky Bottom Booking Bar (Mobile) -->
        <div class="sticky-booking-bar">
            <div class="booking-bar-content">
                <div class="booking-price">
                    <span class="price-label">From</span>
                    <span class="price-amount" id="stickyPrice">₱29,999</span>
                    <span class="price-per">per person</span>
                </div>
                <button class="btn-book-now" id="bookNowBtn">
                    <span>Book Now</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </div>

    </div>

    <!-- Scripts -->
    <script src="../../mobile/js/JSON/package-data.js"></script>
    <script src="../mobile/jspackage-details.js"></script>
    
</body>
</html>