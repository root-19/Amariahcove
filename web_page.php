<?php
require_once __DIR__ . '/controller/FeedbackController.php';
require_once __DIR__ . '/controller/GalleryController.php';


$action = $_GET['action'] ?? 'list';
$controller = new FeedbackController();

if ($action === 'create') {
    $controller->create();
}

$feedbacks = $controller->list();


$controller = new GalleryController();
$uploads = $controller->index();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Amariah Resort - Luxury Beach Resort</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.cdnfonts.com/css/din" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    * { font-family:  'serif'; }

    :root{
      --gold: #C9A227;
      --green-dark: #014421;
      --brown: #6F4E37;
    }

    /* Map Tailwind yellow accents to solid brand gold, no gradients */
    [class^="text-yellow-"], [class*=" text-yellow-"] { color: var(--gold) !important; }
    [class^="bg-yellow-"], [class*=" bg-yellow-"] { background-color: var(--gold) !important; }
    [class^="border-yellow-"], [class*=" border-yellow-"] { border-color: var(--gold) !important; }

    /* Disable all Tailwind gradient backgrounds sitewide */
    [class*="bg-gradient-to-"] { background-image: none !important; }

    /* Ensure gradient text becomes solid gold text */
    [class*="bg-clip-text"][class*="bg-gradient-to-"] {
      background: none !important;
      -webkit-text-fill-color: var(--gold) !important;
      color: var(--gold) !important;
    }
    
    .gradient-bg { background: var(--gold); }
    
    .hero-gradient { background: var(--gold); }
    
    .card-hover {
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .card-hover:hover {
      transform: translateY(-8px);
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }
    
    .fade-in {
      opacity: 0;
      transform: translateY(30px);
      transition: all 0.6s ease-out;
    }
    
    .fade-in.visible {
      opacity: 1;
      transform: translateY(0);
    }
    
    .dropdown {
      position: relative;
    }
    
    .dropdown-content {
      position: absolute;
      top: 100%;
      left: 0;
      background: white;
      min-width: 200px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
      border-radius: 12px;
      opacity: 0;
      visibility: hidden;
      transform: translateY(-10px);
      transition: all 0.3s ease;
      z-index: 1000;
    }
    
    .dropdown.active .dropdown-content {
      opacity: 1;
      visibility: visible;
      transform: translateY(0);
    }
    
    .dropdown-item {
      display: block;
      padding: 12px 20px;
      color: #374151;
      text-decoration: none;
      transition: all 0.2s ease;
      border-bottom: 1px solid #f3f4f6;
    }
    
    .dropdown-item:last-child {
      border-bottom: none;
    }
    
    .dropdown-item:hover {
      background: #f8fafc;
      color: #D4AF37;
      padding-left: 24px;
    }
    
    .social-icon {
      transition: all 0.3s ease;
    }
    
    .social-icon:hover {
      transform: translateY(-3px) scale(1.1);
    }
    
    .floating {
      animation: floating 3s ease-in-out infinite;
    }
    
    @keyframes floating {
      0%, 100% { transform: translateY(0px); }
      50% { transform: translateY(-10px); }
    }
    
    .pulse-slow {
      animation: pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    
    .modal {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.8);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 9999;
      opacity: 0;
      visibility: hidden;
      transition: all 0.3s ease;
    }
    
    .modal.active {
      opacity: 1;
      visibility: visible;
    }
    
    .modal-content {
      background: white;
      border-radius: 20px;
      max-width: 90vw;
      max-height: 90vh;
      overflow-y: auto;
      transform: scale(0.8);
      transition: transform 0.3s ease;
    }
    
    .modal.active .modal-content {
      transform: scale(1);
    }
    
    .close-btn {
      position: absolute;
      top: 20px;
      right: 20px;
      background: rgba(0, 0, 0, 0.5);
      color: white;
      border: none;
      border-radius: 50%;
      width: 40px;
      height: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      font-size: 20px;
      transition: all 0.3s ease;
    }
    
    .close-btn:hover {
      background: rgba(0, 0, 0, 0.8);
      transform: scale(1.1);
    }
    
    /* Enhanced Animations */
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(50px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    @keyframes scaleIn {
      from {
        opacity: 0;
        transform: scale(0.8);
      }
      to {
        opacity: 1;
        transform: scale(1);
      }
    }
    
    @keyframes slideInLeft {
      from {
        opacity: 0;
        transform: translateX(-50px);
      }
      to {
        opacity: 1;
        transform: translateX(0);
      }
    }
    
    @keyframes slideInRight {
      from {
        opacity: 0;
        transform: translateX(50px);
      }
      to {
        opacity: 1;
        transform: translateX(0);
      }
    }
    
    @keyframes glow {
      0%, 100% {
        box-shadow: 0 0 20px rgba(255, 215, 0, 0.3);
      }
      50% {
        box-shadow: 0 0 40px rgba(255, 215, 0, 0.6);
      }
    }
    
    .animate-fade-in-up {
      animation: fadeInUp 1s ease-out forwards;
    }
    
    .animate-scale-in {
      animation: scaleIn 0.8s ease-out forwards;
    }
    
    .animate-slide-in-left {
      animation: slideInLeft 0.8s ease-out forwards;
    }
    
    .animate-slide-in-right {
      animation: slideInRight 0.8s ease-out forwards;
    }
    
    .animate-glow {
      animation: glow 2s ease-in-out infinite;
    }
    
    /* Enhanced Card Hover Effects */
    .card-hover {
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
    }
    
    .card-hover::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
      transition: left 0.6s;
    }
    
    .card-hover:hover::before {
      left: 100%;
    }
    
    .card-hover:hover {
      transform: translateY(-12px) scale(1.02);
      box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.3);
    }
    
    /* Enhanced Button Effects */
    .btn-enhanced {
      position: relative;
      overflow: hidden;
      transition: all 0.3s ease;
    }
    
    .btn-enhanced::before {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      width: 0;
      height: 0;
      background: rgba(255, 255, 255, 0.2);
      border-radius: 50%;
      transform: translate(-50%, -50%);
      transition: width 0.6s, height 0.6s;
    }
    
    .btn-enhanced:hover::before {
      width: 300px;
      height: 300px;
    }
    
    /* Text Reveal Animation */
    .text-reveal {
      overflow: hidden;
    }
    
    .text-reveal span {
      display: inline-block;
      transform: translateY(100%);
      animation: revealText 0.8s ease-out forwards;
    }
    
    @keyframes revealText {
      to {
        transform: translateY(0);
      }
    }
    
    /* Enhanced Section Transitions */
    .section-fade-in {
      opacity: 0;
      transform: translateY(30px);
      transition: all 0.8s ease-out;
    }
    
    .section-fade-in.visible {
      opacity: 1;
      transform: translateY(0);
    }

    /* Hero Ken Burns effect: 6s cycle (4s zoom-out, 2s zoom-in) */
    .kenburns {
      animation: kenburnsCycle 6s ease-in-out infinite;
      will-change: transform;
      transform-origin: center;
    }
    @keyframes kenburnsCycle {
      0% { transform: scale(1.2); }
      66.666% { transform: scale(1.0); }
      100% { transform: scale(1.3); }
    }
    </style>
</head>
<body class="font-sans text-gray-800">

  

  <!-- Front Hero Image Section -->
  <section id="home" role="main" class="relative h-screen w-full overflow-hidden">
    <!-- Full-bleed background using local image -->
    <img id="heroImage" src="./image/1.JPG" alt="Amariah Resort Front View" class="absolute inset-0 w-full h-full object-cover kenburns">
    <div class="absolute inset-0" style="background: rgba(0,0,0,0.5);"></div>

    <!-- Enhanced Content Overlay -->
    <div class="absolute inset-0 flex items-center justify-center text-center text-white z-10">
      <div class="max-w-5xl px-4">
        <div class="fade-in">
          
          <!-- Enhanced Description -->
          <p class="text-2xl md:text-4xl mb-6 text-yellow-100 font-bold max-w-4xl mx-auto leading-relaxed animate-fade-in-up" style="animation-delay: 0.3s;">
            LUXURY REDEFINED
          </p>
          <p class="text-lg md:text-xl font-playfair mb-10 max-w-3xl mx-auto leading-relaxed animate-fade-in-up" style="animation-delay: 0.6s;">
          Elegance meets tranquility at Amariah Resort, where every moment is a masterpiece of comfort and style.
          </p>
         
      </div>
    </div>

    <!-- Enhanced Scroll Indicator -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 text-white/80 animate-bounce">
      <div class="flex flex-col items-center">
        <span class="text-sm mb-2 font-ligh text-white">Scroll to explore</span>
        <i class="fas fa-chevron-down text-2xl text-white"></i>
      </div>
    </div>
  </section>

  <!-- Header -->
  <header class="fixed top-0 w-full bg-white/95 backdrop-blur-lg shadow-lg z-50 border-b border-gray-100" id="main-header">
    <nav class="max-w-7xl mx-auto flex items-center justify-between px-6 py-4">
      <div class="flex items-center space-x-3 group relative">
      <button id="summaryToggle" aria-controls="summaryPanel" aria-expanded="false" aria-label="Open summary"
                class="mr-5 text-2xl rounded-md text-white ">
        <i class="fas fa-bars text-black"></i>
        </button>
        <a href="#" class="flex items-center space-x-3">
          <img src="image/AmariahLogoTrnas.png" alt="Amariah Resort" class="h-12 w-auto group-hover:scale-105 transition-transform duration-300">
        </a>
      
        <!-- Side Drawer Summary Panel -->
        <div id="summaryBackdrop" class="hidden fixed inset-0 bg-black/40 z-40"></div>
        <aside id="summaryPanel" class="hidden fixed top-0 left-0 h-screen w-[340px] max-w-[85vw] bg-white z-50 shadow-2xl border-r border-gray-200 overflow-y-auto">
          <div class="p-5">
            <div class="flex items-center justify-between mb-4">
              <div class="flex items-center">
                <span class="inline-block w-1.5 h-6 bg-yellow-500 mr-3"></span>
                <h3 class="text-3xl font-semibold text-gray-900">About us</h3>
              </div>
              <button id="summaryClose" aria-label="Close summary" class="text-yellow-500 text-xl hover:text-yellow-600">
                   <i class="fas fa-close"></i>
              </button>
            </div>
            <p class="text-gray-600 leading-relaxed text-2xl mb-8">
              Set against the magnificent golden sunset of Amariah, discover a tranquil escape where luxury meets nature. Minutes from the city, yet a world away.
            </p>
            <div class="flex items-center mb-3">
              <span class="inline-block w-1.5 h-6 bg-yellow-500 mr-3"></span>
              <h4 class="text-3xl font-semibold text-gray-900">Our pages</h4>
            </div>
            <nav class="space-y-2 text-2xl">
              <a href="#home" class="block text-gray-700 hover:text-yellow-600">Home</a>
              <a href="#gallery" class="block text-gray-700 hover:text-yellow-600">Gallery</a>
              <a href="#amenities" class="block text-gray-700 hover:text-yellow-600">Amenities</a>
              <a href="#accommodations" class="block text-gray-700 hover:text-yellow-600">Accommodations</a>
              <a href="#feedback" class="block text-gray-700 hover:text-yellow-600">Guest Reviews</a>
            </nav>
            </p>
            <br>
            
            <div class="flex items-center mb-3">
              <span class="inline-block w-1.5 h-6 bg-yellow-500 mr-3"></span>
              <h4 class="text-3xl font-semibold text-gray-900">Our contact</h4>
            </div>
                <div class="flex space-x-4">
            <a href="#" class="social-icon w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center text-black hover:from-yellow-400 hover:to-yellow-500">
              <i class="fab fa-facebook-f text-white text-lg"></i>
            </a>
            <a href="#" class="social-icon w-12 h-12 bg-rose-500 rounded-full flex items-center justify-center text-black hover:from-yellow-400 hover:to-yellow-500">
              <i class="fab fa-instagram text-white text-lg"></i>
            </a>
            <a href="#" class="social-icon w-12 h-12 bg-gray-950 rounded-full flex items-center justify-center text-black hover:from-yellow-400 hover:to-yellow-500">
              <i class="fab fa-x text-lg text-white"></i>
            </a>
          </div>
            </div>

          </div>
        </aside>
      </div>
      <button id="menuToggle" class="md:hidden text-2xl text-gray-700 hover:text-yellow-600 transition-colors" aria-label="Open menu" aria-controls="navMenu" aria-expanded="false">
        <i class="fas fa-bars"></i>
      </button>
      <ul id="navMenu" class="hidden md:flex space-x-8 font-medium">
        <li><a href="#home" class="nav-link text-white hover:text-yellow-300 transition-colors duration-300 relative group">
          Home
          <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-gradient-to-r from-yellow-400 to-yellow-500 transition-all duration-300 group-hover:w-full"></span>
        </a></li>
        <li><a href="#gallery" class="nav-link text-white hover:text-yellow-300 transition-colors duration-300 relative group">
          Gallery
          <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-gradient-to-r from-yellow-400 to-yellow-500 transition-all duration-300 group-hover:w-full"></span>
        </a></li>
        <li class="dropdown">
          <a href="#accommodations" class="nav-link text-white hover:text-yellow-300 transition-colors duration-300 relative group flex items-center">
            Accommodations
            <i class="fas fa-chevron-down ml-1 text-xs transition-transform duration-300 group-hover:rotate-180"></i>
            <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-gradient-to-r from-yellow-400 to-yellow-500 transition-all duration-300 group-hover:w-full"></span>
          </a>
          <div class="dropdown-content">
            <a href="#vip-pool" class="dropdown-item">
              <i class="fas fa-swimming-pool mr-2 text-yellow-500"></i>VIP Pool
            </a>
            <a href="#hotel-rooms" class="dropdown-item">
              <i class="fas fa-bed mr-2 text-yellow-500"></i>Hotel Rooms
            </a>
            <a href="#glamping" class="dropdown-item">
              <i class="fas fa-campground mr-2 text-yellow-500"></i>Glamping
            </a>
          </div>
        </li>
        <li class="dropdown">
          <a href="#amenities" class="nav-link text-white hover:text-yellow-300 transition-colors duration-300 relative group flex items-center">
            Amenities
            <i class="fas fa-chevron-down ml-1 text-xs transition-transform duration-300 group-hover:rotate-180"></i>
            <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-gradient-to-r from-yellow-400 to-yellow-500 transition-all duration-300 group-hover:w-full"></span>
          </a>
          <div class="dropdown-content">
            <a href="#atv" class="dropdown-item">
              <i class="fas fa-motorcycle mr-2 text-yellow-500"></i>ATV Adventure
            </a>
            <a href="#banana-boat" class="dropdown-item">
              <i class="fas fa-ship mr-2 text-yellow-500"></i>Banana Boat
            </a>
            <a href="#jetski" class="dropdown-item">
              <i class="fas fa-water mr-2 text-yellow-500"></i>Jetski
            </a>
          </div>
        </li>
        <li><a href="#feedback" class="nav-link text-white hover:text-yellow-300 transition-colors duration-300 relative group">
          Feedback
          <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-gradient-to-r from-yellow-400 to-yellow-500 transition-all duration-300 group-hover:w-full"></span>
        </a></li>
        <li><a href="#contact" class="nav-link text-white hover:text-yellow-300 transition-colors duration-300 relative group">
          Contact
          <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-gradient-to-r from-yellow-400 to-yellow-500 transition-all duration-300 group-hover:w-full"></span>
        </a></li>
        <li><a href="https://hotels.cloudbeds.com/en/reservation/Bou4jK?widget=1&currency=php" class="bg-yellow-400 text-white px-6 py-2.5 rounded-full shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 font-semibold">
          <i class="fas fa-calendar-check mr-2 text-white"></i>Book Now
        </a></li>
      </ul>
    </nav>
  </header>



  <!-- Adventure Amenities -->
  <section id="amenities" class="py-24 bg-emerald-900 relative overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-5">
      <div class="absolute top-10 left-10 w-32 h-32 bg-yellow-500 rounded-full animate-pulse"></div>
      <div class="absolute top-40 right-20 w-24 h-24 bg-yellow-400 rounded-full animate-pulse" style="animation-delay: 1s;"></div>
      <div class="absolute bottom-20 left-1/4 w-20 h-20 bg-yellow-600 rounded-full animate-pulse" style="animation-delay: 2s;"></div>
    </div>
    
    <div class="max-w-7xl mx-auto px-6 relative z-10">
      <div class="text-center mb-20 section-fade-in">
        <div class="inline-block mb-6">
          <span class="text-sm font-semibold bg-yellow-500 text-white uppercase tracking-wider  px-4 py-2 rounded-full">
            Adventure Activities
          </span>
        </div>
        <h2 class="text-5xl md:text-7xl font-bold mb-8 bg-yellow-400 bg-clip-text text-transparent leading-tight">
          Thrilling Experiences
        </h2>
        <p class="text-xl md:text-2xl text-white max-w-4xl mx-auto leading-relaxed">
          Dive into excitement with our world-class adventure amenities designed for unforgettable memories
        </p>
      </div>
      
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- ATV Adventure -->
        <div id="atv" class="bg-white rounded-2xl shadow-lg hover:shadow-2xl overflow-hidden card-hover group">
          <div class="relative overflow-hidden">
            <img src="./image/ATV.jpg" alt="ATV Adventure" class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-500">
            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
            <div class="absolute top-4 right-4 bg-rose-950 text-white px-4 py-2 rounded-full text-sm font-semibold">
              Adventure
            </div>
          </div>
          <div class="p-8">
            <div class="flex items-center mb-4">
              <i class="fas fa-motorcycle text-3xl text-yellow-600 mr-3"></i>
              <h3 class="text-2xl font-bold text-gray-800">ATV Adventure</h3>
            </div>
            <p class="text-gray-600 mb-6 leading-relaxed">
              Explore rugged terrains and scenic trails with our powerful ATV vehicles. Perfect for adrenaline seekers and nature lovers.
            </p>
            <div class="flex items-center justify-between mb-6">
            </div>
            <a href="#" class="w-full bg-rose-950 text-white py-3 px-6 rounded-full font-semibold text-center block hover:shadow-lg transition-all duration-300 hover:scale-105 view-details-btn" data-target="atv-details">
              <i class="fas fa-eye mr-2 text-white"></i>View Details
            </a>
          </div>
        </div>

        <!-- Banana Boat -->
        <div id="banana-boat" class="bg-white rounded-2xl shadow-lg hover:shadow-2xl overflow-hidden card-hover group">
          <div class="relative overflow-hidden">
            <video src="./video/BananaBoat.mp4" autoplay muted loop playsinlin alt="Banana Boat" class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-500">
            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
            <div class="absolute top-4 right-4 bg-rose-950 text-black px-4 py-2 rounded-full text-sm font-semibold">
              Water Fun
            </div>
          </div>
          <div class="p-8">
            <div class="flex items-center mb-4">
              <i class="fas fa-ship text-3xl text-yellow-600 mr-3"></i>
              <h3 class="text-2xl font-bold text-gray-800">Banana Boat</h3>
            </div>
            <p class="text-gray-600 mb-6 leading-relaxed">
              Hold on tight for anexhilarating ride on our banana boat! Fun for groups and families looking for water excitement.
            </p>
            <a href="#" class="w-full bg-rose-950 text-white py-3 px-6 rounded-full font-semibold text-center block hover:shadow-lg transition-all duration-300 hover:scale-105 view-details-btn" data-target="banana-boat-details">
              <i class="fas fa-eye mr-2 text-white"></i>View Details
            </a>
          </div>
        </div>

        <!-- Jetski -->
        <div id="jetski" class="bg-white rounded-2xl shadow-lg hover:shadow-2xl overflow-hidden card-hover group">
          <div class="relative overflow-hidden">
             <video src="./video/jetski.mp4" autoplay muted loop playsinlin alt="Jetski" class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-500">
            <div class="absolute inset-0 b-rose-950 from-black/50 to-transparent"></div>
            <div class="absolute top-4 right-4 bg-rose-950 text-black px-4 py-2 rounded-full text-sm font-semibold">
              High Speed
            </div>
          </div>
          <div class="p-8">
            <div class="flex items-center mb-4">
              <i class="fas fa-water text-3xl text-yellow-600 mr-3"></i>
              <h3 class="text-2xl font-bold text-gray-800">Jetski</h3>
            </div>
            <p class="text-gray-600 mb-6 leading-relaxed">
              Feel the rush of speed on crystal clear waters with our high-performance jetskis. Experience the ultimate water adventure.
            </p>
            <a href="#" class="w-full bg-rose-950 text-white py-3 px-6 rounded-full font-semibold text-center block hover:shadow-lg transition-all duration-300 hover:scale-105 view-details-btn" data-target="jetski-details">
              <i class="fas fa-eye mr-2 text-white"></i>View Details
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Accommodations -->
  <section id="accommodations" class="py-24 bg-emerald-900 relative overflow-hidden">
    <!-- Background Elements -->
    <div class="absolute inset-0 opacity-5">
      <div class="absolute top-20 right-10 w-40 h-40 bg-yellow-500 rounded-full animate-pulse"></div>
      <div class="absolute bottom-20 left-10 w-32 h-32 bg-yellow-400 rounded-full animate-pulse" style="animation-delay: 1.5s;"></div>
      <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-24 h-24 bg-yellow-600 rounded-full animate-pulse" style="animation-delay: 3s;"></div>
    </div>
    
    <div class="max-w-7xl mx-auto px-6 relative z-10">
      <div class="text-center mb-20 section-fade-in">
        <div class="inline-block mb-6">
          <span class="text-sm font-semibold text-white uppercase tracking-wider bg-yellow-400 px-4 py-2 rounded-full">
            Premium Stays
          </span>
        </div>
        <h2 class="text-5xl md:text-7xl font-bold mb-8 bg-yellow-400 bg-clip-text text-transparent leading-tight">
          Luxury Accommodations
        </h2>
        <p class="text-xl md:text-2xl text-white max-w-4xl mx-auto leading-relaxed">
          Indulge in our exquisite accommodations where comfort meets luxury in paradise
        </p>
      </div>
      
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- VIP Pool -->
        <div id="vip-pool" class="bg-white rounded-2xl shadow-lg hover:shadow-2xl overflow-hidden card-hover group">
          <div class="relative overflow-hidden">
            <img src="./image/h.jpg" alt="VIP Pool" class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-500">
            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
            <div class="absolute top-4 right-4 bg-rose-950 text-white px-4 py-2 rounded-full text-sm font-semibold">
              VIP Access
            </div>
          </div>
          <div class="p-8">
            <div class="flex items-center mb-4">
              <i class="fas fa-swimming-pool text-3xl text-yellow-600 mr-3"></i>
              <h3 class="text-2xl font-bold text-gray-800">VIP Pool Area</h3>
            </div>
            <p class="text-gray-600 mb-6 leading-relaxed">
              Exclusive infinity pool with panoramic ocean views, private cabanas, and premium service. Perfect for relaxation and luxury.
            </p>
            <a href="#" class="w-full bg-rose-950 text-white py-3 px-6 rounded-full font-semibold text-center block hover:shadow-lg transition-all duration-300 hover:scale-105 view-details-btn" data-target="vip-pool-details">
              <i class="fas fa-eye mr-2 text-white"></i>View Details
            </a>
          </div>
        </div>

        <!-- Hotel Rooms -->
        <div id="hotel-rooms" class="bg-white rounded-2xl shadow-lg hover:shadow-2xl overflow-hidden card-hover group">
          <div class="relative overflow-hidden">
            <img src="./image/DSC09052 (1).jpg" alt="Hotel Rooms" class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-500">
            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
            <div class="absolute top-4 right-4 bg-rose-950  text-white px-4 py-2 rounded-full text-sm font-semibold">
              Premium
            </div>
          </div>
          <div class="p-8">
            <div class="flex items-center mb-4">
              <i class="fas fa-bed text-3xl text-yellow-600 mr-3"></i>
              <h3 class="text-2xl font-bold text-gray-800">Hotel Rooms</h3>
            </div>
            <p class="text-gray-600 mb-6 leading-relaxed">
              Elegantly designed rooms with modern amenities, ocean views, and premium comfort. Your perfect beach getaway starts here.
            </p>
         
            <a href="#" class="w-full bg-rose-950 text-white py-3 px-6 rounded-full font-semibold text-center block hover:shadow-lg transition-all duration-300 hover:scale-105 view-details-btn" data-target="hotel-rooms-details">
              <i class="fas fa-eye mr-2 text-white"></i>View Details
            </a>
          </div>
        </div>

        <!-- Glamping -->
        <div id="glamping" class="bg-white rounded-2xl shadow-lg hover:shadow-2xl overflow-hidden card-hover group">
          <div class="relative overflow-hidden">
            <img src="./image/c.jpg" alt="Glamping" class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-500">
            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
            <div class="absolute top-4 right-4 bg-rose-950 text-white px-4 py-2 rounded-full text-sm font-semibold">
              Nature
            </div>
          </div>
          <div class="p-8">
            <div class="flex items-center mb-4">
              <i class="fas fa-campground text-3xl text-yellow-600 mr-3"></i>
              <h3 class="text-2xl font-bold text-gray-800">Glamping Tents</h3>
            </div>
            <p class="text-gray-600 mb-6 leading-relaxed">
              Luxury camping experience with premium tents, comfortable beds, and nature immersion. Perfect for adventure seekers.
            </p>
            <a href="#" class="w-full bg-rose-950 text-white py-3 px-6 rounded-full font-semibold text-center block hover:shadow-lg transition-all duration-300 hover:scale-105 view-details-btn" data-target="glamping-details">
              <i class="fas fa-eye mr-2 text-white"></i>View Details
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Detailed Content Modals -->
  
  <!-- VIP Pool Details Modal -->
  <div id="vip-pool-details" class="modal">
    <div class="modal-content relative">
      <button class="close-btn" onclick="closeModal('vip-pool-details')">
        <i class="fas fa-times"></i>
      </button>
      <div class="p-8">
        <div class="text-center mb-8">
          <h2 class="text-4xl font-bold bg-gradient-to-r from-yellow-500 to-yellow-600 bg-clip-text text-transparent mb-4">
            VIP Pool Area
          </h2>
          <p class="text-xl text-gray-600">Exclusive luxury pool experience</p>
        </div>
        
        <!-- Image Gallery -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
          <div class="rounded-2xl overflow-hidden shadow-lg">
            <img src="./image/h.jpg" alt="Infinity Pool" class="w-full h-64 object-cover">
          </div>
          <div class="rounded-2xl overflow-hidden shadow-lg">
            <img src="https://source.unsplash.com/600x400/?pool,cabana,luxury" alt="Private Cabanas" class="w-full h-64 object-cover">
          </div>
          <div class="rounded-2xl overflow-hidden shadow-lg">
            <img src="https://source.unsplash.com/600x400/?pool,ocean,view" alt="Ocean View" class="w-full h-64 object-cover">
          </div>
          <div class="rounded-2xl overflow-hidden shadow-lg">
            <img src="https://source.unsplash.com/600x400/?pool,bar,luxury" alt="Pool Bar" class="w-full h-64 object-cover">
          </div>
          <div class="rounded-2xl overflow-hidden shadow-lg">
            <img src="https://source.unsplash.com/600x400/?pool,spa,relaxation" alt="Pool Spa" class="w-full h-64 object-cover">
          </div>
          <div class="rounded-2xl overflow-hidden shadow-lg">
            <img src="https://source.unsplash.com/600x400/?pool,night,lights" alt="Night Lights" class="w-full h-64 object-cover">
          </div>
        </div>
        
        <!-- Features -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
          <div>
            <h3 class="text-2xl font-bold text-gray-800 mb-4">Premium Features</h3>
            <ul class="space-y-3">
              <li class="flex items-center">
                <i class="fas fa-check-circle text-yellow-500 mr-3"></i>
                <span>Infinity pool with panoramic ocean views</span>
              </li>
              <li class="flex items-center">
                <i class="fas fa-check-circle text-yellow-500 mr-3"></i>
                <span>Private cabanas with butler service</span>
              </li>
              <li class="flex items-center">
                <i class="fas fa-check-circle text-yellow-500 mr-3"></i>
                <span>Poolside bar with premium cocktails</span>
              </li>
              <li class="flex items-center">
                <i class="fas fa-check-circle text-yellow-500 mr-3"></i>
                <span>Heated pool with jacuzzi sections</span>
              </li>
              <li class="flex items-center">
                <i class="fas fa-check-circle text-yellow-500 mr-3"></i>
                <span>Exclusive access for VIP guests only</span>
              </li>
            </ul>
          </div>
          <div>
            <h3 class="text-2xl font-bold text-gray-800 mb-4">Services Included</h3>
            <ul class="space-y-3">
              <li class="flex items-center">
                <i class="fas fa-concierge-bell text-yellow-500 mr-3"></i>
                <span>Personal concierge service</span>
              </li>
              <li class="flex items-center">
                <i class="fas fa-umbrella text-yellow-500 mr-3"></i>
                <span>Premium towels and amenities</span>
              </li>
              <li class="flex items-center">
                <i class="fas fa-cocktail text-yellow-500 mr-3"></i>
                <span>Complimentary welcome drinks</span>
              </li>
              <li class="flex items-center">
                <i class="fas fa-spa text-yellow-500 mr-3"></i>
                <span>Poolside massage services</span>
              </li>
              <li class="flex items-center">
                <i class="fas fa-wifi text-yellow-500 mr-3"></i>
                <span>High-speed WiFi throughout area</span>
              </li>
            </ul>
          </div>
        </div>
        
        <!-- Pricing -->
        <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 rounded-2xl p-6 text-center">
          <h3 class="text-2xl font-bold text-gray-800 mb-4">VIP Pool Access</h3>
          <div class="text-4xl font-bold text-yellow-600 mb-2">₱5,000</div>
          <div class="text-gray-600 mb-4">per day (8:00 AM - 10:00 PM)</div>
          <div class="text-sm text-gray-500 mb-6">
            Includes: Pool access, cabana rental, welcome drinks, towels, and concierge service
          </div>
          <a href="#" class="bg-gradient-to-r from-yellow-500 to-yellow-600 text-black px-8 py-3 rounded-full font-semibold hover:shadow-lg transition-all duration-300 hover:scale-105">
            <i class="fas fa-calendar-check mr-2"></i>Reserve VIP Access
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- ATV Adventure Details Modal -->
  <div id="atv-details" class="modal">
    <div class="modal-content relative">
      <button class="close-btn" onclick="closeModal('atv-details')">
        <i class="fas fa-times"></i>
      </button>
      <div class="p-8">
        <div class="text-center mb-8">
          <h2 class="text-4xl font-bold bg-gradient-to-r from-yellow-500 to-yellow-600 bg-clip-text text-transparent mb-4">
            ATV Adventure
          </h2>
          <p class="text-xl text-gray-600">Thrilling off-road adventure experience</p>
        </div>
        
        <!-- Image Gallery -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
          <div class="rounded-2xl overflow-hidden shadow-lg">
            <img src="https://source.unsplash.com/600x400/?atv,adventure,mountain" alt="Mountain Trail" class="w-full h-64 object-cover">
          </div>
          <div class="rounded-2xl overflow-hidden shadow-lg">
            <img src="https://source.unsplash.com/600x400/?atv,forest,trail" alt="Forest Trail" class="w-full h-64 object-cover">
          </div>
          <div class="rounded-2xl overflow-hidden shadow-lg">
            <img src="https://source.unsplash.com/600x400/?atv,beach,sand" alt="Beach Trail" class="w-full h-64 object-cover">
          </div>
          <div class="rounded-2xl overflow-hidden shadow-lg">
            <img src="https://source.unsplash.com/600x400/?atv,group,adventure" alt="Group Adventure" class="w-full h-64 object-cover">
          </div>
          <div class="rounded-2xl overflow-hidden shadow-lg">
            <img src="https://source.unsplash.com/600x400/?atv,water,mud" alt="Water Crossing" class="w-full h-64 object-cover">
          </div>
          <div class="rounded-2xl overflow-hidden shadow-lg">
            <img src="https://source.unsplash.com/600x400/?atv,sunset,trail" alt="Sunset Trail" class="w-full h-64 object-cover">
          </div>
        </div>
        
        <!-- Features -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
          <div>
            <h3 class="text-2xl font-bold text-gray-800 mb-4">Adventure Routes</h3>
            <ul class="space-y-3">
              <li class="flex items-center">
                <i class="fas fa-mountain text-yellow-500 mr-3"></i>
                <span>Mountain trail with scenic viewpoints</span>
              </li>
              <li class="flex items-center">
                <i class="fas fa-tree text-yellow-500 mr-3"></i>
                <span>Forest path through tropical jungle</span>
              </li>
              <li class="flex items-center">
                <i class="fas fa-water text-yellow-500 mr-3"></i>
                <span>Beach route along coastline</span>
              </li>
              <li class="flex items-center">
                <i class="fas fa-route text-yellow-500 mr-3"></i>
                <span>Custom routes based on experience level</span>
              </li>
            </ul>
          </div>
          <div>
            <h3 class="text-2xl font-bold text-gray-800 mb-4">Safety & Equipment</h3>
            <ul class="space-y-3">
              <li class="flex items-center">
                <i class="fas fa-helmet-safety text-yellow-500 mr-3"></i>
                <span>Professional safety gear provided</span>
              </li>
              <li class="flex items-center">
                <i class="fas fa-user-tie text-yellow-500 mr-3"></i>
                <span>Experienced guide for each group</span>
              </li>
              <li class="flex items-center">
                <i class="fas fa-tools text-yellow-500 mr-3"></i>
                <span>Well-maintained ATV vehicles</span>
              </li>
              <li class="flex items-center">
                <i class="fas fa-first-aid text-yellow-500 mr-3"></i>
                <span>First aid and emergency support</span>
              </li>
            </ul>
          </div>
        </div>
        
        <!-- Pricing -->
        <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 rounded-2xl p-6 text-center">
          <h3 class="text-2xl font-bold text-gray-800 mb-4">ATV Adventure Pricing</h3>
          <div class="text-4xl font-bold text-yellow-600 mb-2">₱2,500</div>
          <div class="text-gray-600 mb-4">per hour (minimum 2 hours)</div>
          <div class="text-sm text-gray-500 mb-6">
            Includes: ATV rental, safety gear, guide, and basic insurance
          </div>
          <a href="#" class="bg-gradient-to-r from-yellow-500 to-yellow-600 text-black px-8 py-3 rounded-full font-semibold hover:shadow-lg transition-all duration-300 hover:scale-105">
            <i class="fas fa-calendar-check mr-2"></i>Book Adventure
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Promotions Section -->
  <section id="promotions" class="py-24 bg-emerald-900 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-6 relative z-10">
      <div class="text-center mb-16">
        <div class="inline-block mb-4">
          <span class="text-sm font-semibold text-white uppercase tracking-wider bg-yellow-400 px-4 py-2 rounded-full">Latest Offers</span>
        </div>
        <h2 class="text-4xl md:text-6xl font-bold mb-4 bg-yellow-400 bg-clip-text text-transparent">Promotions</h2>
        <p class="text-lg md:text-xl text-white/90 max-w-3xl mx-auto">Grab our limited-time deals for stays, activities, and dining. Book early and save.</p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <article class="bg-white rounded-2xl shadow-lg overflow-hidden card-hover">
          <div class="relative h-48 overflow-hidden">
            <img src="./image/DSC09052.jpg" alt="Stay & Save" class="w-full h-full object-cover">
          </div>
          <div class="p-6">
            <h3 class="text-2xl font-semibold text-gray-800 mb-2">Stay 3, Pay 2</h3>
            <p class="text-gray-600 mb-4">Enjoy a complimentary night when you book three consecutive nights.</p>
            <div class="flex items-center justify-between">
              <span class="text-yellow-600 font-semibold">Limited time</span>
              <a href="#contact" class="px-4 py-2 rounded-full bg-rose-950 text-white text-sm">Claim Offer</a>
            </div>
          </div>
        </article>

        <article class="bg-white rounded-2xl shadow-lg overflow-hidden card-hover">
          <div class="relative h-48 overflow-hidden">
            <img src="./image/h.jpg" alt="Adventure Bundle" class="w-full h-full object-cover">
          </div>
          <div class="p-6">
            <h3 class="text-2xl font-semibold text-gray-800 mb-2">Adventure Bundle</h3>
            <p class="text-gray-600 mb-4">ATV + Banana Boat combo with 10% off and welcome drinks.</p>
            <div class="flex items-center justify-between">
              <span class="text-yellow-600 font-semibold">Weekends</span>
              <a href="#amenities" class="px-4 py-2 rounded-full bg-rose-950 text-white text-sm">View Details</a>
            </div>
          </div>
        </article>

        <article class="bg-white rounded-2xl shadow-lg overflow-hidden card-hover">
          <div class="relative h-48 overflow-hidden">
            <img src="./image/c.jpg" alt="Dining Credit" class="w-full h-full object-cover">
          </div>
          <div class="p-6">
            <h3 class="text-2xl font-semibold text-gray-800 mb-2">Dining Credit</h3>
            <p class="text-gray-600 mb-4">Receive ₱1,000 dining credit for stays booked this month.</p>
            <div class="flex items-center justify-between">
              <span class="text-yellow-600 font-semibold">This month</span>
              <a href="#contact" class="px-4 py-2 rounded-full bg-rose-950 text-white text-sm">Book Now</a>
            </div>
          </div>
        </article>
      </div>
    </div>
  </section>

  <!-- Gallery Section -->
  <section id="gallery" class="py-24 bg-emerald-900 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-6 relative z-10">
      <div class="text-center mb-12">
        <div class="inline-block mb-4">
          <span class="text-sm font-semibold text-white uppercase tracking-wider bg-yellow-400 px-4 py-2 rounded-full">Explore</span>
        </div>
        <h2 class="text-4xl md:text-6xl font-bold mb-4 text-yellow-400">Gallery</h2>
        <p class="text-lg text-white max-w-3xl mx-auto">view our stunning gallery showcasing the beauty of Amariah.</p>
      </div>

    <?php if (!empty($uploads)): ?>
    <article class="bg-white rounded-2xl shadow-2xl overflow-hidden border border-gray-100">
      <div class="grid grid-cols-1 lg:grid-cols-2">
        <!-- Left: Image -->
        <figure class="relative">
          <img id="galleryImage" 
               src="<?= htmlspecialchars($uploads[0]['filepath']) ?>" 
               alt="<?= htmlspecialchars($uploads[0]['filename']) ?>" 
               class="w-full h-[28rem] object-cover">

          <!-- Arrows -->
          <button id="galleryPrev" aria-label="Previous" 
                  class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/90 text-gray-800 rounded-full w-10 h-10 flex items-center justify-center shadow hover:bg-white">
            <i class="fas fa-chevron-left"></i>
          </button>
          <button id="galleryNext" aria-label="Next" 
                  class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/90 text-gray-800 rounded-full w-10 h-10 flex items-center justify-center shadow hover:bg-white">
            <i class="fas fa-chevron-right"></i>
          </button>
        </figure>

        <!-- Right: Text -->
        <div class="p-8 lg:p-10">
          <h3 id="galleryTitle" class="hidden">
            <?= htmlspecialchars($uploads[0]['filename']) ?>
          </h3>
          <p id="galleryDesc" class="text-gray-700 text-center mt-20 leading-relaxed mb-4">
            <?= !empty($uploads[0]['message']) ? htmlspecialchars($uploads[0]['message']) : "No description available." ?>
          </p>
          <p id="galleryLong" class="text-gray-600 mt-40 leading-relaxed mb-6">
            Uploaded on <?= date("F j, Y g:i A", strtotime($uploads[0]['created_at'])) ?>
          </p>
        </div>
      </div>
    </article>

    <!-- JS to handle next/prev -->
    <script>
      // Pass PHP uploads to JS
      const uploads = <?= json_encode($uploads) ?>;
      let currentIndex = 0;

      const imgEl = document.getElementById("galleryImage");
      const titleEl = document.getElementById("galleryTitle");
      const descEl = document.getElementById("galleryDesc");
      const longEl = document.getElementById("galleryLong");

      function updateGallery(index) {
        if (!uploads || uploads.length === 0) return;

        const item = uploads[index];
        imgEl.src = item.filepath;
        imgEl.alt = item.filename;
        titleEl.textContent = item.filename;
        descEl.textContent = item.message && item.message.trim() !== "" 
          ? item.message 
          : "No description available.";
        longEl.textContent = "Uploaded on " + new Date(item.created_at).toLocaleString();
      }

      // Event listeners for buttons
      document.getElementById("galleryPrev").addEventListener("click", () => {
        currentIndex = (currentIndex - 1 + uploads.length) % uploads.length;
        updateGallery(currentIndex);
      });

      document.getElementById("galleryNext").addEventListener("click", () => {
        currentIndex = (currentIndex + 1) % uploads.length;
        updateGallery(currentIndex);
      });
    </script>

    <?php else: ?>
      <div class="text-center text-white py-20">
        <p class="text-lg">No images uploaded yet. Please add some from the admin panel.</p>
      </div>
    <?php endif; ?>
  </div>
</section>


  <!-- Feedback Section -->
  <section id="feedback" class="py-24 bg-emerald-900 relative overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-5">
      <div class="absolute top-20 left-20 w-32 h-32 bg-yellow-500 rounded-full animate-pulse"></div>
      <div class="absolute bottom-20 right-20 w-24 h-24 bg-yellow-400 rounded-full animate-pulse" style="animation-delay: 1s;"></div>
      <div class="absolute top-1/2 left-1/4 w-20 h-20 bg-yellow-400 rounded-full animate-pulse" style="animation-delay: 2s;"></div>
    </div>
    
    <div class="max-w-7xl mx-auto px-6 relative z-10">
      <div class="text-center mb-20 section-fade-in">
        <div class="inline-block mb-6">
          <span class="text-sm font-semibold text-white uppercase tracking-wider bg-yellow-400 px-4 py-2 rounded-full">
            Guest Feedback
          </span>
        </div>
        <h2 class="text-5xl md:text-7xl font-bold mb-8 bg-yellow-400 bg-clip-text text-transparent leading-tight">
          Share Your Experience
        </h2>
        <p class="text-xl md:text-2xl text-white max-w-4xl mx-auto leading-relaxed">
          We value your feedback and would love to hear about your stay at Amariah Resort
        </p>
      </div>
      
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
        <!-- Feedback Form -->
        <div class="bg-white rounded-2xl shadow-lg p-8 card-hover">
          <h3 class="text-3xl font-bold text-gray-800 mb-6 flex items-center">
            <i class="fas fa-comment-dots text-yellow-600 mr-3"></i>
            Leave Your Feedback
          </h3>
          
           <?php if (!empty($error)): ?>
      <p class="text-red-500 mb-4"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" action="?action=create" class="space-y-6">
      <div>
        <label for="feedbackName">Full Name</label>
        <input type="text" id="feedbackName" name="name" required class="border w-full p-2 rounded">
      </div>

      <div>
        <label for="feedbackEmail">Email </label>
        <input type="email" id="feedbackEmail" name="email" required class="border w-full p-2 rounded">
      </div>

<div class="flex space-x-1 text-3xl text-gray-400">
  <?php for ($i = 1; $i <= 5; $i++): ?>
    <input type="radio" id="star<?= $i ?>" name="rating" value="<?= $i ?>" class="hidden peer/star<?= $i ?>">
    <label for="star<?= $i ?>" class="cursor-pointer">
      <i class="fas fa-star"></i>
    </label>
  <?php endfor; ?>
</div>

<!-- Script to highlight stars dynamically -->
<script>
  const stars = document.querySelectorAll('label i');
  const radios = document.querySelectorAll('input[name="rating"]');

  radios.forEach((radio, index) => {
    radio.addEventListener('change', () => {
      stars.forEach((star, i) => {
        if (i <= index) {
          star.classList.add('text-yellow-400'); 
        } else {
          star.classList.remove('text-yellow-400'); 
        }
      });
    });
  });
</script>

      <div>
        <label for="feedbackMessage">Your Feedback *</label>
        <textarea id="feedbackMessage" name="message" rows="4" required class="border w-full p-2 rounded"></textarea>
      </div>

      <button type="submit" class="bg-rose-950 text-white px-6 py-3 rounded-full font-semibold hover:shadow-lg transition-all duration-300 hover:scale-105">
        Submit Feedback
      </button>
    </form>
  </div>

        <!-- Feedback Display -->
        <div class="bg-white rounded-2xl shadow-lg p-8 card-hover">
          <h3 class="text-3xl font-bold text-gray-800 mb-6 flex items-center">
            <i class="fas fa-star text-yellow-600 mr-3"></i>
            Guest Reviews
          </h3>
          
   <div id="feedbackList" class="space-y-6 max-h-96 overflow-y-auto">
  <?php if (empty($feedbacks)): ?>
    <p class="text-gray-500">No feedback yet. Be the first!</p>
  <?php else: ?>
    <?php foreach ($feedbacks as $fb): ?>
      <div class="feedback-item border-b border-gray-200 pb-6">
        <div class="flex items-center justify-between mb-3">
          <div class="flex items-center">
            <!-- Avatar first letter -->
            <div class="w-12 h-12 bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-full flex items-center justify-center text-black font-bold text-lg mr-4">
              <?= strtoupper(substr($fb['name'], 0, 1)) ?>
            </div>
            <div>
              <h4 class="font-semibold text-gray-800"><?= htmlspecialchars($fb['name']) ?></h4>
              <div class="flex text-yellow-500">
                <?php for ($i=1; $i<=5; $i++): ?>
                  <?php if ($i <= (int)$fb['rate']): ?>
                    <i class="fas fa-star"></i>
                  <?php else: ?>
                    <i class="far fa-star"></i>
                  <?php endif; ?>
                <?php endfor; ?>
              </div>
            </div>
          </div>
          <span class="text-sm text-gray-500">
            <?= date("M d, Y", strtotime($fb['created_at'])) ?>
          </span>
        </div>
        <p class="text-gray-600 leading-relaxed">
          "<?= htmlspecialchars($fb['message']) ?>"
        </p>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer id="contact" class="bg-emerald-900 text-white py-16">
    <div class="max-w-7xl mx-auto px-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
        <!-- Brand Section -->
        <div class="md:col-span-1">
          <div class="flex items-center space-x-3 mb-6">
            <img src="image/AmariahLogoTrnas (1).png" alt="Amariah Resort" class="h-12 w-auto">
            <span class="text-2xl font-bold bg-gradient-to-r from-yellow-400 to-yellow-500 bg-clip-text text-transparent">Amariah Resort</span>
          </div>
          <p class="text-gray-400 leading-relaxed mb-6">
            Experience paradise at our luxury beach resort with world-class amenities, stunning accommodations, and unforgettable adventures.
          </p>
          <!-- Social Media Icons -->
          <div class="flex space-x-4">
            <a href="#" class="social-icon w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center text-black hover:from-yellow-400 hover:to-yellow-500">
              <i class="fab fa-facebook-f text-white text-lg"></i>
            </a>
            <a href="#" class="social-icon w-12 h-12 bg-rose-500 rounded-full flex items-center justify-center text-black hover:from-yellow-400 hover:to-yellow-500">
              <i class="fab fa-instagram text-white text-lg"></i>
            </a>
            <a href="#" class="social-icon w-12 h-12 bg-gray-950 rounded-full flex items-center justify-center text-black hover:from-yellow-400 hover:to-yellow-500">
              <i class="fab fa-x text-lg text-white"></i>
            </a>
          </div>
        </div>

        <!-- Quick Links -->
        <div>
          <h3 class="text-white font-bold text-lg mb-6">Quick Links</h3>
          <ul class="space-y-3">
            <li><a href="#home" class="text-gray-400 hover:text-yellow-400 transition-colors duration-300 flex items-center">
              <i class="fas fa-home mr-2 text-yellow-500"></i>Home
            </a></li>
            <li><a href="#accommodations" class="text-gray-400 hover:text-yellow-400 transition-colors duration-300 flex items-center">
              <i class="fas fa-bed mr-2 text-yellow-500"></i>Accommodations
            </a></li>
            <li><a href="#amenities" class="text-gray-400 hover:text-yellow-400 transition-colors duration-300 flex items-center">
              <i class="fas fa-star mr-2 text-yellow-500"></i>Amenities
            </a></li>
            <li><a href="#gallery" class="text-gray-400 hover:text-yellow-400 transition-colors duration-300 flex items-center">
              <i class="fas fa-images mr-2 text-yellow-500"></i>Gallery
            </a></li>
            <li><a href="#feedback" class="text-gray-400 hover:text-yellow-400 transition-colors duration-300 flex items-center">
              <i class="fas fa-comment-dots mr-2 text-yellow-500"></i>Feedback
            </a></li>
          </ul>
        </div>

        <!-- Contact Info -->
        <div>
          <h3 class="text-white font-bold text-lg mb-6">Contact Info</h3>
          <div class="space-y-4">
            <div class="flex items-start space-x-3">
              <i class="fas fa-map-marker-alt text-yellow-500 mt-1"></i>
      <div>
                <p class="text-gray-400">Beachfront Paradise</p>
                <p class="text-gray-400">Tropical Island Resort</p>
              </div>
            </div>
            <div class="flex items-center space-x-3">
              <i class="fas fa-phone text-yellow-500"></i>
              <p class="text-gray-400">+63 917 123 4567</p>
            </div>
            <div class="flex items-center space-x-3">
              <i class="fas fa-envelope text-yellow-500"></i>
              <p class="text-gray-400">reservations@amariahresort.com</p>
            </div>
          </div>
      </div>

        <!-- Newsletter -->
      <div>
          <h3 class="text-white font-bold text-lg mb-6">Stay Updated</h3>
          <p class="text-gray-400 mb-4">Subscribe to our newsletter for exclusive offers and updates.</p>
          <div class="flex">
            <input type="email" placeholder="Your email" class="flex-1 px-4 py-3 bg-gray-800 border border-gray-700 rounded-l-lg text-white placeholder-gray-400 focus:outline-none focus:border-yellow-500">
            <button class="bg-gradient-to-r from-yellow-500 to-yellow-600 text-black px-6 py-3 rounded-r-lg hover:from-yellow-400 hover:to-yellow-500 transition-all duration-300">
              <i class="fas fa-paper-plane"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Bottom Bar -->
      <div class="border-t border-white pt-8">
        <div class="flex flex-col md:flex-row justify-between items-center">
          <p class="text-gray-400 text-sm mb-4 md:mb-0">
            &copy; 2025 Amariah Resort. All rights reserved.
          </p>
          <div class="flex space-x-6 text-sm">
            <a href="#" class="text-gray-400 hover:text-yellow-400 transition-colors duration-300">Privacy Policy</a>
            <a href="#" class="text-gray-400 hover:text-yellow-400 transition-colors duration-300">Terms of Service</a>
            <a href="#" class="text-gray-400 hover:text-yellow-400 transition-colors duration-300">Cookie Policy</a>
          </div>
        </div>
      </div>
    </div>
  </footer>

  <script>

    // Mobile menu toggle
    const menuToggle = document.getElementById('menuToggle');
    const navMenu = document.getElementById('navMenu');
    const summaryToggle = document.getElementById('summaryToggle');
    const summaryPanel = document.getElementById('summaryPanel');
    const summaryBackdrop = document.getElementById('summaryBackdrop');
    const summaryClose = document.getElementById('summaryClose');
    
    menuToggle.addEventListener('click', () => {
      navMenu.classList.toggle('hidden');
      navMenu.classList.toggle('flex');
      navMenu.classList.toggle('flex-col');
      navMenu.classList.toggle('absolute');
      navMenu.classList.toggle('top-16');
      navMenu.classList.toggle('left-0');
      navMenu.classList.toggle('bg-white');
      navMenu.classList.toggle('w-full');
      navMenu.classList.toggle('space-y-6');
      navMenu.classList.toggle('p-6');
      navMenu.classList.toggle('shadow-lg');
      const expanded = menuToggle.getAttribute('aria-expanded') === 'true' || false;
      menuToggle.setAttribute('aria-expanded', (!expanded).toString());
    });

    function openSummary(){
      summaryPanel.classList.remove('hidden');
      summaryBackdrop.classList.remove('hidden');
      summaryToggle.setAttribute('aria-expanded', 'true');
      document.body.style.overflow = 'hidden';
    }
    function closeSummary(){
      summaryPanel.classList.add('hidden');
      summaryBackdrop.classList.add('hidden');
      summaryToggle.setAttribute('aria-expanded', 'false');
      document.body.style.overflow = '';
    }

    summaryToggle.addEventListener('click', (e) => {
      e.stopPropagation();
      if (summaryPanel.classList.contains('hidden')) openSummary(); else closeSummary();
    });
    summaryClose.addEventListener('click', closeSummary);
    summaryBackdrop.addEventListener('click', closeSummary);

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
          target.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
          });
        }
      });
    });

    // Fade in animation on scroll
    const observerOptions = {
      threshold: 0.1,
      rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
        }
      });
    }, observerOptions);

    // Observe all fade-in elements
    document.querySelectorAll('.fade-in').forEach(el => {
      observer.observe(el);
    });

    // Add fade-in class to cards and sections
    document.querySelectorAll('.card-hover').forEach((card, index) => {
      card.classList.add('fade-in');
      card.style.animationDelay = `${index * 0.2}s`;
      observer.observe(card);
    });
    
    // Observe section fade-ins
    document.querySelectorAll('.section-fade-in').forEach(section => {
      observer.observe(section);
    });
    
    // Enhanced scroll animations
    const enhancedObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          
          // Add staggered animations for child elements
          const children = entry.target.querySelectorAll('.animate-stagger');
          children.forEach((child, index) => {
            setTimeout(() => {
              child.classList.add('animate-fade-in-up');
            }, index * 100);
          });
        }
      });
    }, {
      threshold: 0.1,
      rootMargin: '0px 0px -50px 0px'
    });
    
    // Observe all sections for enhanced animations
    document.querySelectorAll('section').forEach(section => {
      enhancedObserver.observe(section);
    });

    // Header background change on scroll
    window.addEventListener('scroll', () => {
      const header = document.getElementById('main-header');
      const heroSection = document.querySelector('section');
      const heroHeight = heroSection.offsetHeight;
      const navLinks = document.querySelectorAll('.nav-link');
      
      if (window.scrollY > heroHeight - 100) {
        // Over content sections - solid background
        header.classList.remove('bg-transparent', 'text-white');
        header.classList.add('bg-white/98', 'text-gray-700');
        header.style.borderBottom = '1px solid #e5e7eb';
        
        // Change nav links to dark
        navLinks.forEach(link => {
          link.classList.remove('text-white', 'hover:text-yellow-300');
          link.classList.add('text-gray-700', 'hover:text-yellow-600');
        });
      } else {
        // Over hero image - transparent background
        header.classList.remove('bg-white/98', 'text-gray-700');
        header.classList.add('bg-transparent', 'text-white');
        header.style.borderBottom = 'none';
        
        // Change nav links to white
        navLinks.forEach(link => {
          link.classList.remove('text-gray-700', 'hover:text-yellow-600');
          link.classList.add('text-white', 'hover:text-yellow-300');
        });
      }
    });

    // Remove parallax transform to keep hero fixed & clean during scroll

    // Add loading animation
    window.addEventListener('load', () => {
      document.body.classList.add('loaded');
    });

    // Hero image Ken Burns + slideshow (6s per image)
    (function(){
      const heroImage = document.getElementById('heroImage');
      if (!heroImage) return;
      // Add your image file names here (found in ./image)
      const images = [
        '1.jPG',
  '2.jPG',
  '3.jpg',
  '4.jpg',
  '5.jpg',
  '6.jpg',
  '7.jpg',
  '8.jpg',
  '9.jPG',
  '10.jpg',
  '11.jpg',
  '12.jpg',
  '13.jpg',
  '14.jpg'
      ].map(name => `./image/${name}`);
      let idx = 0;
      setInterval(() => {
        idx = (idx + 1) % images.length;
        // Crossfade by temporary opacity swap
        heroImage.style.transition = 'opacity 400ms ease';
        heroImage.style.opacity = '0';
        setTimeout(() => {
          heroImage.src = images[idx];
          heroImage.style.opacity = '1';
        }, 400);
      }, 8000);
    })();

    // Dropdown click functionality
    document.querySelectorAll('.dropdown > a').forEach(dropdown => {
      dropdown.addEventListener('click', (e) => {
        e.preventDefault();
        const dropdownParent = dropdown.parentElement;
        const isActive = dropdownParent.classList.contains('active');
        
        // Close all other dropdowns
        document.querySelectorAll('.dropdown').forEach(d => {
          d.classList.remove('active');
        });
        
        // Toggle current dropdown
        if (!isActive) {
          dropdownParent.classList.add('active');
        }
      });
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', (e) => {
      if (!e.target.closest('.dropdown')) {
        document.querySelectorAll('.dropdown').forEach(d => {
          d.classList.remove('active');
        });
      }
    });

    // View Details button functionality
    document.querySelectorAll('.view-details-btn').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        const target = btn.getAttribute('data-target');
        const modal = document.getElementById(target);
        if (modal) {
          modal.classList.add('active');
          document.body.style.overflow = 'hidden';
        }
      });
    });

    // Close modal function
    window.closeModal = function(modalId) {
      const modal = document.getElementById(modalId);
      if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = 'auto';
      }
    };

    // Close modal when clicking outside
    document.querySelectorAll('.modal').forEach(modal => {
      modal.addEventListener('click', (e) => {
        if (e.target === modal) {
          modal.classList.remove('active');
          document.body.style.overflow = 'auto';
        }
      });
    });

    // Close modal with Escape key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        document.querySelectorAll('.modal.active').forEach(modal => {
          modal.classList.remove('active');
          document.body.style.overflow = 'auto';
        });
      }
    });

   
    // Star rating functionality
    document.querySelectorAll('.star-rating').forEach(star => {
      star.addEventListener('click', function() {
        const rating = parseInt(this.getAttribute('data-rating'));
        currentRating = rating;
        document.getElementById('feedbackRatingValue').value = rating;
        
        // Update star display
        document.querySelectorAll('.star-rating').forEach((s, index) => {
          if (index < rating) {
            s.classList.remove('text-gray-300');
            s.classList.add('text-yellow-500');
          } else {
            s.classList.remove('text-yellow-500');
            s.classList.add('text-gray-300');
          }
        });
      });

      star.addEventListener('mouseenter', function() {
        const rating = parseInt(this.getAttribute('data-rating'));
        document.querySelectorAll('.star-rating').forEach((s, index) => {
          if (index < rating) {
            s.classList.remove('text-gray-300');
            s.classList.add('text-yellow-400');
          }
        });
      });
    });

    // Reset stars on mouse leave
    document.getElementById('ratingStars').addEventListener('mouseleave', function() {
      document.querySelectorAll('.star-rating').forEach((s, index) => {
        if (index < currentRating) {
          s.classList.remove('text-gray-300', 'text-yellow-400');
          s.classList.add('text-yellow-500');
        } else {
          s.classList.remove('text-yellow-500', 'text-yellow-400');
          s.classList.add('text-gray-300');
        }
      });
    });

    // Feedback form submission -> backend
    document.getElementById('feedbackForm').addEventListener('submit', async function(e) {
      e.preventDefault();
      const formData = new FormData(this);
      const email = formData.get('email');
      const rating = parseInt(formData.get('rating'));
      const message = formData.get('message');
      if (rating === 0) { alert('Please select a rating.'); return; }

      try {
        const res = await fetch('controller/FeedbackController.php?action=create', {
          method: 'POST',
          body: new URLSearchParams({ email, rate: String(rating), message })
        });
        const data = await res.json();
        if (!data.ok) { alert(data.error || 'Failed to submit'); return; }
        // Prepend to UI
        feedbacks.unshift({
          id: data.id,
          name: formData.get('name') || 'Guest',
          email,
          rating,
          message,
          date: 'Just now',
          initial: (formData.get('name') || 'G').toString().charAt(0).toUpperCase()
        });
        updateFeedbackDisplay();
        this.reset();
        currentRating = 0;
        document.getElementById('feedbackRatingValue').value = 0;
        document.querySelectorAll('.star-rating').forEach(star => { star.classList.remove('text-yellow-500'); star.classList.add('text-gray-300'); });
        showSuccessMessage();
      } catch (err) {
        alert('Network error');
      }
    });

    // Update feedback display
    function updateFeedbackDisplay() {
      const feedbackList = document.getElementById('feedbackList');
      feedbackList.innerHTML = '';
      
      feedbacks.forEach(feedback => {
        const feedbackItem = document.createElement('div');
        feedbackItem.className = 'feedback-item border-b border-gray-200 pb-6';
        
        const stars = Array(feedback.rating).fill('<i class="fas fa-star"></i>').join('') + 
                     Array(5 - feedback.rating).fill('<i class="far fa-star"></i>').join('');
        
        feedbackItem.innerHTML = `
          <div class="flex items-center justify-between mb-3">
            <div class="flex items-center">
              <div class="w-12 h-12 bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-full flex items-center justify-center text-black font-bold text-lg mr-4">
                ${feedback.initial}
              </div>
              <div>
                <h4 class="font-semibold text-gray-800">${feedback.name}</h4>
                <div class="flex text-yellow-500">
                  ${stars}
                </div>
              </div>
            </div>
            <span class="text-sm text-gray-500">${feedback.date}</span>
          </div>
          <p class="text-gray-600 leading-relaxed">
            "${feedback.message}"
          </p>
        `;
        
        feedbackList.appendChild(feedbackItem);
      });
    }

    // Show success message
    function showSuccessMessage() {
      const successMessage = document.createElement('div');
      successMessage.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300';
      successMessage.innerHTML = `
        <div class="flex items-center">
          <i class="fas fa-check-circle mr-2"></i>
          <span>Thank you for your feedback!</span>
        </div>
      `;
      
      document.body.appendChild(successMessage);
      
      // Animate in
      setTimeout(() => {
        successMessage.classList.remove('translate-x-full');
      }, 100);
      
      // Remove after 3 seconds
      setTimeout(() => {
        successMessage.classList.add('translate-x-full');
        setTimeout(() => {
          document.body.removeChild(successMessage);
        }, 300);
      }, 3000);
    }

    // Load more feedbacks functionality
  

    // Simple gallery slider (image left, text right)
    (function(){
      const items = [
        { src: './image/DSC09052 (1).jpg', title: 'Resort View', desc: 'Sun-kissed vistas and tranquil shores welcome you to Amariah.', long: 'Wake up to the sound of gentle waves and golden light spilling over the horizon. Each frame captures the calm, the craft, and the coastal elegance that define our destination.' },
        { src: './image/ATV.jpg', title: 'ATV Adventure', desc: 'Conquer scenic trails with our guided off-road rides.', long: 'Our expert-led routes balance thrill and safety, taking you across sands, forest paths, and scenic ridges. Perfect for groups and first-time riders.' },
        { src: './image/h.jpg', title: 'VIP Pool', desc: 'Lounge by the infinity pool with breathtaking ocean views.', long: 'Private cabanas, crafted cocktails, and a warm ocean breeze: unwind while the horizon melts into shades of gold.' },
        { src: './image/c.jpg', title: 'Glamping', desc: 'Luxury tents nestled close to nature for serene nights.', long: 'Experience nature with comfort—premium bedding, soft lighting, and starry skies just outside your door.' },
        { src: './image/DSC09052.jpg', title: 'Elegant Rooms', desc: 'Thoughtfully designed rooms combining comfort and style.', long: 'Spacious layouts, refined finishes, and restful tones create a sanctuary designed for deep relaxation.' }
      ];
      const img = document.getElementById('galleryImage');
      const title = document.getElementById('galleryTitle');
      const desc = document.getElementById('galleryDesc');
      const long = document.getElementById('galleryLong');
      const prev = document.getElementById('galleryPrev');
      const next = document.getElementById('galleryNext');
      if (!img || !title || !desc || !long || !prev || !next) return;
      let i = 0;

      function updateDots(index){
        for (let d = 0; d < items.length; d++){
          const el = document.getElementById('dot'+d);
          if (!el) continue;
          el.classList.toggle('bg-yellow-400', d === index);
          el.classList.toggle('bg-gray-300', d !== index);
        }
      }

      function render(index){
        img.style.transition = 'opacity 250ms ease';
        img.style.opacity = '0';
        setTimeout(() => {
          img.src = items[index].src;
          title.textContent = items[index].title;
          desc.textContent = items[index].desc;
          long.textContent = items[index].long;
          img.style.opacity = '1';
          updateDots(index);
        }, 250);
      }

      prev.addEventListener('click', () => { i = (i - 1 + items.length) % items.length; render(i); });
      next.addEventListener('click', () => { i = (i + 1) % items.length; render(i); });
    })();
  </script>
</body>
</html>
