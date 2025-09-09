<?php
session_start();
include_once __DIR__ . '/layout/header.php';
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Guest Feedback - Amariah Resort</title>
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<!-- DIN Font -->
<link href="https://fonts.cdnfonts.com/css/din" rel="stylesheet">
<!-- Tailwind CSS -->
<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = {
    theme: {
      extend: {
        fontFamily: {
          'heading': ['Playfair Display', 'serif'],
          'body': ['Poppins', 'sans-serif'],
          'din': ['DIN', 'Arial', 'sans-serif'],
        },
        colors: {
          'greenDark': {
            50: '#f0f9f4',
            100: '#dcf4e6',
            200: '#bce8d1',
            300: '#8dd5b4',
            400: '#56bc91',
            500: '#2d9b6f',
            600: '#1f7a5a',
            700: '#1a6249',
            800: '#164e3b',
            900: '#014421',
            950: '#012a15',
          },
          'gold': {
            50: '#fefce8',
            100: '#fef9c3',
            200: '#fef08a',
            300: '#fde047',
            400: '#facc15',
            500: '#C9A227',
            600: '#ca8a04',
            700: '#a16207',
            800: '#854d0e',
            900: '#713f12',
            950: '#422006',
          },
          'brown': {
            50: '#fdf8f6',
            100: '#f2e8e5',
            200: '#eaddd7',
            300: '#e0cec7',
            400: '#d2bab0',
            500: '#bfa094',
            600: '#a18072',
            700: '#6F4E37',
            800: '#5a3e2d',
            900: '#4a3224',
            950: '#2d1e16',
          },
          'sage': {
            50: '#f6f7f4',
            100: '#e8ebe4',
            200: '#d2d8c8',
            300: '#b5c0a7',
            400: '#9aa585',
            500: '#7f8a6b',
            600: '#6b7558',
            700: '#565e48',
            800: '#474d3c',
            900: '#3d4234',
            950: '#1f2219',
          },
          'cream': {
            50: '#fefdfb',
            100: '#fdf9f3',
            200: '#faf2e6',
            300: '#f6e8d1',
            400: '#f0d9b5',
            500: '#e8c896',
            600: '#deb575',
            700: '#d4a155',
            800: '#b88a47',
            900: '#9a723c',
            950: '#5c441f',
          }
        },
        animation: {
          'fade-in': 'fadeIn 1s ease-in-out',
          'fade-in-delay': 'fadeIn 1.5s ease-in-out',
          'slide-down': 'slideDown 1s ease-out',
          'slide-up': 'slideUp 1s ease-out',
          'pop': 'pop 0.8s ease-out',
          'float': 'float 3s ease-in-out infinite',
        },
        keyframes: {
          fadeIn: {
            '0%': { opacity: '0' },
            '100%': { opacity: '1' },
          },
          slideDown: {
            '0%': { transform: 'translateY(-20px)', opacity: '0' },
            '100%': { transform: 'translateY(0)', opacity: '1' },
          },
          slideUp: {
            '0%': { transform: 'translateY(20px)', opacity: '0' },
            '100%': { transform: 'translateY(0)', opacity: '1' },
          },
          pop: {
            '0%': { transform: 'scale(0.9)', opacity: '0' },
            '100%': { transform: 'scale(1)', opacity: '1' },
          },
          float: {
            '0%, 100%': { transform: 'translateY(0px)' },
            '50%': { transform: 'translateY(-10px)' },
          },
        },
        boxShadow: {
          'soft': '0 2px 15px -3px rgba(0, 0, 0, 0.07), 0 10px 20px -2px rgba(0, 0, 0, 0.04)',
          'medium': '0 4px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)',
          'strong': '0 10px 40px -10px rgba(0, 0, 0, 0.15), 0 2px 10px -2px rgba(0, 0, 0, 0.05)',
          'glow': '0 0 20px rgba(201, 162, 39, 0.3)',
          'glow-green': '0 0 20px rgba(1, 68, 33, 0.3)',
        }
      }
    }
  }
</script>
</head>
<body class="antialiased font-din bg-gradient-to-br from-cream-50 via-white to-sage-50 text-gray-800 min-h-screen">

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Header Section -->
    <div class="text-center mb-16 animate-fade-in">
        <h1 class="text-5xl font-heading font-bold text-greenDark mb-6 animate-slide-down">
            Guest Feedback
        </h1>
        <p class="text-xl text-sage-600 max-w-3xl mx-auto leading-relaxed animate-fade-in-delay">
            Share your experience and help us improve our services
        </p>
    </div>

    <!-- Feedback Form -->
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-2xl shadow-strong p-8 border border-greenDark/10 animate-slide-up">
            <h2 class="text-2xl font-heading font-semibold text-greenDark mb-8 text-center">Share Your Experience</h2>
            
            <form class="space-y-6">
                <!-- Rating Section -->
                <div class="text-center">
                    <label class="block text-lg font-medium text-sage-700 mb-4">Overall Rating</label>
                    <div class="flex justify-center space-x-2">
                        <button type="button" class="rating-star text-4xl text-gray-300 hover:text-gold transition-colors duration-200" data-rating="1">★</button>
                        <button type="button" class="rating-star text-4xl text-gray-300 hover:text-gold transition-colors duration-200" data-rating="2">★</button>
                        <button type="button" class="rating-star text-4xl text-gray-300 hover:text-gold transition-colors duration-200" data-rating="3">★</button>
                        <button type="button" class="rating-star text-4xl text-gray-300 hover:text-gold transition-colors duration-200" data-rating="4">★</button>
                        <button type="button" class="rating-star text-4xl text-gray-300 hover:text-gold transition-colors duration-200" data-rating="5">★</button>
                    </div>
                    <input type="hidden" id="rating" name="rating" value="0">
                </div>

                <!-- Form Fields -->
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-sage-700 mb-2">Full Name *</label>
                        <input type="text" id="name" name="name" required 
                               class="w-full px-4 py-3 border border-sage-300 rounded-xl focus:ring-2 focus:ring-greenDark focus:border-transparent transition-all duration-300">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-sage-700 mb-2">Email Address *</label>
                        <input type="email" id="email" name="email" required 
                               class="w-full px-4 py-3 border border-sage-300 rounded-xl focus:ring-2 focus:ring-greenDark focus:border-transparent transition-all duration-300">
                    </div>
                </div>

                <div>
                    <label for="stay-date" class="block text-sm font-medium text-sage-700 mb-2">Stay Date</label>
                    <input type="date" id="stay-date" name="stay_date" 
                           class="w-full px-4 py-3 border border-sage-300 rounded-xl focus:ring-2 focus:ring-greenDark focus:border-transparent transition-all duration-300">
                </div>

                <div>
                    <label for="accommodation" class="block text-sm font-medium text-sage-700 mb-2">Accommodation Type</label>
                    <select id="accommodation" name="accommodation" 
                            class="w-full px-4 py-3 border border-sage-300 rounded-xl focus:ring-2 focus:ring-greenDark focus:border-transparent transition-all duration-300">
                        <option value="">Select accommodation type</option>
                        <option value="vip_pools">VIP Pools</option>
                        <option value="hotel_rooms">Hotel Rooms</option>
                        <option value="glamping">Glamping</option>
                    </select>
                </div>

                <div>
                    <label for="feedback" class="block text-sm font-medium text-sage-700 mb-2">Your Feedback *</label>
                    <textarea id="feedback" name="feedback" rows="6" required 
                              placeholder="Tell us about your experience, what you loved, and how we can improve..."
                              class="w-full px-4 py-3 border border-sage-300 rounded-xl focus:ring-2 focus:ring-greenDark focus:border-transparent transition-all duration-300 resize-none"></textarea>
                </div>

                <!-- Categories -->
                <div>
                    <label class="block text-sm font-medium text-sage-700 mb-3">Rate specific aspects (optional)</label>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sage-600">Service Quality</span>
                            <div class="flex space-x-1">
                                <button type="button" class="aspect-star text-lg text-gray-300 hover:text-gold transition-colors duration-200" data-aspect="service" data-rating="1">★</button>
                                <button type="button" class="aspect-star text-lg text-gray-300 hover:text-gold transition-colors duration-200" data-aspect="service" data-rating="2">★</button>
                                <button type="button" class="aspect-star text-lg text-gray-300 hover:text-gold transition-colors duration-200" data-aspect="service" data-rating="3">★</button>
                                <button type="button" class="aspect-star text-lg text-gray-300 hover:text-gold transition-colors duration-200" data-aspect="service" data-rating="4">★</button>
                                <button type="button" class="aspect-star text-lg text-gray-300 hover:text-gold transition-colors duration-200" data-aspect="service" data-rating="5">★</button>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sage-600">Cleanliness</span>
                            <div class="flex space-x-1">
                                <button type="button" class="aspect-star text-lg text-gray-300 hover:text-gold transition-colors duration-200" data-aspect="cleanliness" data-rating="1">★</button>
                                <button type="button" class="aspect-star text-lg text-gray-300 hover:text-gold transition-colors duration-200" data-aspect="cleanliness" data-rating="2">★</button>
                                <button type="button" class="aspect-star text-lg text-gray-300 hover:text-gold transition-colors duration-200" data-aspect="cleanliness" data-rating="3">★</button>
                                <button type="button" class="aspect-star text-lg text-gray-300 hover:text-gold transition-colors duration-200" data-aspect="cleanliness" data-rating="4">★</button>
                                <button type="button" class="aspect-star text-lg text-gray-300 hover:text-gold transition-colors duration-200" data-aspect="cleanliness" data-rating="5">★</button>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sage-600">Amenities</span>
                            <div class="flex space-x-1">
                                <button type="button" class="aspect-star text-lg text-gray-300 hover:text-gold transition-colors duration-200" data-aspect="amenities" data-rating="1">★</button>
                                <button type="button" class="aspect-star text-lg text-gray-300 hover:text-gold transition-colors duration-200" data-aspect="amenities" data-rating="2">★</button>
                                <button type="button" class="aspect-star text-lg text-gray-300 hover:text-gold transition-colors duration-200" data-aspect="amenities" data-rating="3">★</button>
                                <button type="button" class="aspect-star text-lg text-gray-300 hover:text-gold transition-colors duration-200" data-aspect="amenities" data-rating="4">★</button>
                                <button type="button" class="aspect-star text-lg text-gray-300 hover:text-gold transition-colors duration-200" data-aspect="amenities" data-rating="5">★</button>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sage-600">Value for Money</span>
                            <div class="flex space-x-1">
                                <button type="button" class="aspect-star text-lg text-gray-300 hover:text-gold transition-colors duration-200" data-aspect="value" data-rating="1">★</button>
                                <button type="button" class="aspect-star text-lg text-gray-300 hover:text-gold transition-colors duration-200" data-aspect="value" data-rating="2">★</button>
                                <button type="button" class="aspect-star text-lg text-gray-300 hover:text-gold transition-colors duration-200" data-aspect="value" data-rating="3">★</button>
                                <button type="button" class="aspect-star text-lg text-gray-300 hover:text-gold transition-colors duration-200" data-aspect="value" data-rating="4">★</button>
                                <button type="button" class="aspect-star text-lg text-gray-300 hover:text-gold transition-colors duration-200" data-aspect="value" data-rating="5">★</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="text-center pt-6">
                    <button type="submit" class="inline-flex items-center gap-2 bg-gradient-to-r from-greenDark to-greenDark-800 text-white px-8 py-4 rounded-xl font-bold text-lg hover:shadow-glow-green transition-all duration-300 transform hover:scale-105">
                        <span>Submit Feedback</span>
                        <span class="text-xl">📝</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Thank You Message -->
    <div class="text-center mt-12 animate-fade-in">
        <div class="bg-gradient-to-r from-gold/10 to-cream/10 rounded-2xl p-8 border border-gold/20">
            <div class="text-4xl mb-4">🙏</div>
            <h3 class="text-2xl font-heading font-semibold text-greenDark mb-2">Thank You for Your Feedback!</h3>
            <p class="text-sage-600">Your input helps us provide the best possible experience for all our guests.</p>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/layout/footer.php'; ?>

<script>
// Rating functionality
document.querySelectorAll('.rating-star').forEach(star => {
    star.addEventListener('click', function() {
        const rating = parseInt(this.dataset.rating);
        const stars = document.querySelectorAll('.rating-star');
        
        stars.forEach((s, index) => {
            if (index < rating) {
                s.classList.remove('text-gray-300');
                s.classList.add('text-gold');
            } else {
                s.classList.remove('text-gold');
                s.classList.add('text-gray-300');
            }
        });
        
        document.getElementById('rating').value = rating;
    });
});

// Aspect rating functionality
document.querySelectorAll('.aspect-star').forEach(star => {
    star.addEventListener('click', function() {
        const aspect = this.dataset.aspect;
        const rating = parseInt(this.dataset.rating);
        const aspectStars = document.querySelectorAll(`[data-aspect="${aspect}"]`);
        
        aspectStars.forEach((s, index) => {
            if (index < rating) {
                s.classList.remove('text-gray-300');
                s.classList.add('text-gold');
            } else {
                s.classList.remove('text-gold');
                s.classList.add('text-gray-300');
            }
        });
    });
});

// Form submission
document.querySelector('form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Simple validation
    const rating = document.getElementById('rating').value;
    if (rating === '0') {
        alert('Please provide an overall rating.');
        return;
    }
    
    // Show success message
    alert('Thank you for your feedback! We appreciate your input.');
    this.reset();
    
    // Reset all stars
    document.querySelectorAll('.rating-star, .aspect-star').forEach(star => {
        star.classList.remove('text-gold');
        star.classList.add('text-gray-300');
    });
});
</script>

</body>
</html>
