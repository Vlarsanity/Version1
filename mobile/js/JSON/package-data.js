// ================================
// PACKAGE DATA STRUCTURE
// ================================

// Sample JSON structure for packages
// Future: Replace with API endpoint fetch

const packagesData = {
  // Package ID as key for easy lookup
  "seoul-city-explorer": {
    id: "seoul-city-explorer",
    title: "Seoul City Explorer",
    subtitle: "Discover the heart of South Korea",
    category: "korea",
    featured: true,
    
    // Basic Info
    duration: {
      days: 5,
      nights: 4
    },
    price: {
      currency: "PHP",
      amount: 29999,
      originalPrice: 34999, // For showing discounts
      pricePerPerson: true
    },
    rating: {
      average: 4.8,
      total: 156,
      breakdown: {
        5: 98,
        4: 45,
        3: 10,
        2: 2,
        1: 1
      }
    },
    
    // Images
    images: {
      hero: "https://images.unsplash.com/photo-1517154421773-0529f29ea451?w=1200&h=600&fit=crop",
      gallery: [
        "https://images.unsplash.com/photo-1517154421773-0529f29ea451?w=800&h=600&fit=crop",
        "https://images.unsplash.com/photo-1583074801503-b2c7d0c8e8e7?w=800&h=600&fit=crop",
        "https://images.unsplash.com/photo-1534274988757-a28bf1a57c17?w=800&h=600&fit=crop",
        "https://images.unsplash.com/photo-1526295112451-f368e1ae6d63?w=800&h=600&fit=crop"
      ]
    },
    
    // Overview
    overview: {
      description: "Experience the vibrant culture and modern marvels of Seoul. From ancient palaces to cutting-edge technology, this 5-day journey will immerse you in the perfect blend of tradition and innovation that makes Seoul one of Asia's most exciting destinations.",
      highlights: [
        "Visit Gyeongbokgung Palace and witness the changing of the guard",
        "Explore trendy Gangnam district and K-pop culture",
        "Experience traditional Korean BBQ and street food tours",
        "Shopping spree in Myeongdong and Dongdaemun",
        "Scenic views from N Seoul Tower",
        "Traditional tea ceremony experience"
      ]
    },
    
    // Detailed Itinerary
    itinerary: [
      {
        day: 1,
        title: "Arrival & City Orientation",
        activities: [
          "Airport pickup and hotel check-in",
          "Welcome dinner at traditional Korean restaurant",
          "Evening stroll in Hongdae district",
          "Hotel rest"
        ],
        meals: ["Dinner"],
        accommodation: "4-star hotel in Myeongdong"
      },
      {
        day: 2,
        title: "Historical Seoul",
        activities: [
          "Visit Gyeongbokgung Palace",
          "Changing of the Guard ceremony",
          "Bukchon Hanok Village tour",
          "Insadong traditional market",
          "N Seoul Tower for panoramic views"
        ],
        meals: ["Breakfast", "Lunch"],
        accommodation: "4-star hotel in Myeongdong"
      },
      {
        day: 3,
        title: "Modern Seoul & Shopping",
        activities: [
          "Gangnam district tour",
          "COEX Mall and Starfield Library",
          "K-pop entertainment district",
          "Myeongdong shopping district",
          "Free time for personal exploration"
        ],
        meals: ["Breakfast"],
        accommodation: "4-star hotel in Myeongdong"
      },
      {
        day: 4,
        title: "Cultural Immersion",
        activities: [
          "Traditional tea ceremony",
          "Korean cooking class",
          "Changdeokgung Palace and Secret Garden",
          "Dongdaemun Design Plaza",
          "Night market food tour"
        ],
        meals: ["Breakfast", "Lunch", "Dinner"],
        accommodation: "4-star hotel in Myeongdong"
      },
      {
        day: 5,
        title: "Departure",
        activities: [
          "Free time for last-minute shopping",
          "Hotel check-out",
          "Airport transfer"
        ],
        meals: ["Breakfast"],
        accommodation: null
      }
    ],
    
    // Inclusions & Exclusions
    inclusions: [
      "Round-trip airport transfers",
      "4 nights accommodation in 4-star hotel",
      "Daily breakfast",
      "Selected meals as per itinerary",
      "English-speaking tour guide",
      "All entrance fees and activities",
      "Transportation during tours",
      "Travel insurance"
    ],
    exclusions: [
      "International airfare",
      "Personal expenses",
      "Meals not mentioned in itinerary",
      "Optional tours and activities",
      "Tips and gratuities",
      "Visa fees (if applicable)"
    ],
    
    // Requirements
    requirements: {
      minPeople: 2,
      maxPeople: 40,
      minAge: null,
      visa: "Check visa requirements for South Korea",
      fitness: "Moderate walking required"
    },
    
    // Booking availability (for calendar)
    availability: {
      // Format: "YYYY-MM-DD": { available: boolean, price: number, slots: number }
      "2026-05-07": { available: true, price: 35000, slots: 12 },
      "2026-05-08": { available: true, price: 32000, slots: 8 },
      "2026-05-15": { available: true, price: 34000, slots: 15 },
      "2026-05-16": { available: true, price: 33000, slots: 20 },
      "2026-05-21": { available: true, price: 34000, slots: 18 },
      "2026-05-27": { available: true, price: 36000, slots: 10 },
      "2026-05-28": { available: true, price: 36000, slots: 14 },
      "2026-05-29": { available: true, price: 34000, slots: 22 },
      "2026-05-30": { available: true, price: 34000, slots: 16 },
      "2026-05-31": { available: true, price: 32000, slots: 25 }
    },
    
    // Reviews (sample)
    reviews: [
      {
        id: 1,
        userName: "Maria Santos",
        userAvatar: "",
        rating: 5,
        date: "2024-03-15",
        title: "Amazing experience!",
        comment: "Seoul exceeded all my expectations. The tour was well-organized and our guide was incredibly knowledgeable. The blend of modern and traditional culture was fascinating.",
        verified: true,
        helpful: 24
      },
      {
        id: 2,
        userName: "John Reyes",
        userAvatar: "",
        rating: 5,
        date: "2024-02-28",
        title: "Highly recommended",
        comment: "Perfect itinerary! Not too rushed but we got to see all the main attractions. The food tours were a highlight. Already planning to come back!",
        verified: true,
        helpful: 18
      },
      {
        id: 3,
        userName: "Lisa Chen",
        userAvatar: "",
        rating: 4,
        date: "2024-02-10",
        title: "Great value for money",
        comment: "Overall great experience. Hotels were clean and well-located. Only wish we had more free time for shopping.",
        verified: true,
        helpful: 12
      }
    ],
    
    // Related packages
    relatedPackages: ["busan-beach", "tokyo-explorer", "kyoto-osaka"],
    
    // SEO & Meta (for future)
    seo: {
      metaTitle: "Seoul City Explorer - 5 Days Korea Tour Package",
      metaDescription: "Discover Seoul's perfect blend of tradition and modernity. 5-day guided tour including palaces, shopping, food tours, and cultural experiences.",
      keywords: ["seoul tour", "korea package", "seoul travel", "korea vacation"]
    },
    
    // Status
    status: "active", // active, soldout, coming-soon
    tags: ["cultural", "shopping", "foodie", "city-tour"],
    createdAt: "2024-01-01",
    updatedAt: "2024-03-20"
  },
  
  // Add more packages...
  "tokyo-explorer": {
    id: "tokyo-explorer",
    title: "Tokyo Explorer",
    subtitle: "Urban adventures in Japan's capital",
    category: "japan",
    featured: false,
    duration: { days: 7, nights: 6 },
    price: { currency: "PHP", amount: 49999, originalPrice: null, pricePerPerson: true },
    rating: { average: 5.0, total: 89, breakdown: { 5: 85, 4: 3, 3: 1, 2: 0, 1: 0 } },
    images: {
      hero: "https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?w=1200&h=600&fit=crop",
      gallery: [
        "https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?w=800&h=600&fit=crop",
        "https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=800&h=600&fit=crop",
        "https://images.unsplash.com/photo-1503899036084-c55cdd92da26?w=800&h=600&fit=crop",
        "https://images.unsplash.com/photo-1536098561742-ca998e48cbcc?w=800&h=600&fit=crop"
      ]
    },
    overview: {
      description: "Dive into the electric energy of Tokyo, where ancient temples stand alongside neon-lit skyscrapers. Experience cutting-edge technology, world-class cuisine, and timeless traditions in this unforgettable 7-day journey.",
      highlights: [
        "Visit iconic Senso-ji Temple in Asakusa",
        "Experience the famous Shibuya Crossing",
        "Explore traditional Meiji Shrine",
        "Day trip to Mt. Fuji and Hakone",
        "Discover Akihabara electronics district",
        "Enjoy authentic sushi at Tsukiji Market"
      ]
    },
    // ... similar structure to seoul-city-explorer
    status: "active",
    tags: ["technology", "culture", "urban", "foodie"]
  }
};

// ================================
// FUTURE API IMPLEMENTATION
// ================================

/*
// Replace the static data above with API calls:

async function getPackageById(packageId) {
  try {
    const response = await fetch(`/api/packages/${packageId}`);
    const data = await response.json();
    return data;
  } catch (error) {
    console.error('Error fetching package:', error);
    return null;
  }
}

async function getPackageAvailability(packageId, month, year) {
  try {
    const response = await fetch(`/api/packages/${packageId}/availability?month=${month}&year=${year}`);
    const data = await response.json();
    return data;
  } catch (error) {
    console.error('Error fetching availability:', error);
    return {};
  }
}

async function getAllPackages() {
  try {
    const response = await fetch('/api/packages');
    const data = await response.json();
    return data;
  } catch (error) {
    console.error('Error fetching packages:', error);
    return [];
  }
}

// Usage:
const package = await getPackageById('seoul-city-explorer');
const availability = await getPackageAvailability('seoul-city-explorer', 5, 2026);
*/

// Export for use
if (typeof module !== 'undefined' && module.exports) {
  module.exports = packagesData;
}