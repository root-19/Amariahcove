<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Amariah</title>

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
            'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
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
          backgroundImage: {
            'gradient-radial': 'radial-gradient(var(--tw-gradient-stops))',
            'gradient-conic': 'conic-gradient(from 180deg at 50% 50%, var(--tw-gradient-stops))',
            'hero-pattern': "url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23014421\" fill-opacity=\"0.05\"%3E%3Ccircle cx=\"30\" cy=\"30\" r=\"2\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')",
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

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="antialiased font-din bg-gradient-to-br from-cream-50 via-white to-sage-50 text-gray-800 min-h-screen">

<?php
// compute admin root and view base so links are absolute (avoid relative path 404s)
$script = str_replace('\\','/', $_SERVER['SCRIPT_NAME'] ?? '');
$pos = strpos($script, '/admin');
$adminRoot = ($pos !== false) ? substr($script, 0, $pos + 6) : ''; // includes "/admin"
$viewBase = $adminRoot . '/view';
$projectRoot = ($pos !== false) ? substr($adminRoot, 0, $pos) : '';
?>
<header class="sticky top-0 z-50 backdrop-blur-lg bg-white/90 shadow-soft border-b border-greenDark/10">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center h-20">

      <!-- Brand -->
      <a href="<?php echo $adminRoot ?: '/'; ?>/index.php" class="flex items-center gap-3 group">
        <div class="relative">
          <!-- <img src="../../image/AmariahLogoTrnas.png' alt="Amariah"
               class="h-12 w-12 rounded-2xl shadow-medium object-cover transition-all duration-300 group-hover:scale-110 group-hover:shadow-glow" />
          <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-gold/20 to-greenDark/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div> -->
        </div>
        <div class="flex flex-col">
          <span class="font-heading text-2xl font-bold text-emerald-900 group-hover:text-gold transition-colors duration-300">Amariah</span>
          <span class="text-xs text-emerald-900 font-medium tracking-wider uppercase">cove</span>
        </div>
      </a>

      <!-- Desktop nav -->
      <nav class="hidden md:flex items-center gap-8 font-medium">
        <a class="nav-link relative px-4 py-2 text-sm font-medium text-sage-700 hover:text-greenDark transition-all duration-300 rounded-lg hover:bg-cream-100" href="<?php echo $adminRoot ?: '/'; ?>../index.php">
          <span class="relative z-10">Home</span>
        </a>
        <a class="nav-link relative px-4 py-2 text-sm font-medium text-sage-700 hover:text-greenDark transition-all duration-300 rounded-lg hover:bg-cream-100" href="<?php echo $viewBase; ?>/gallery.php">
          <span class="relative z-10">Gallery</span>
        </a>
        <a class="nav-link relative px-4 py-2 text-sm font-medium text-sage-700 hover:text-greenDark transition-all duration-300 rounded-lg hover:bg-cream-100" href="<?php echo $viewBase; ?>/accomodations.php">
          <span class="relative z-10">Accommodation</span>
        </a>
        <a class="nav-link relative px-4 py-2 text-sm font-medium text-sage-700 hover:text-greenDark transition-all duration-300 rounded-lg hover:bg-cream-100" href="<?php echo $viewBase; ?>/ameties.php">
          <span class="relative z-10">Amenities</span>
        </a>

      </nav>

      <!-- Mobile controls -->
      <div class="md:hidden flex items-center gap-2">
        <button id="mobileMenuBtn" aria-label="Open menu" class="p-3 rounded-xl hover:bg-cream-100 transition-colors duration-300 group">
          <svg id="hamburger" class="w-6 h-6 text-sage-700 group-hover:text-greenDark transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>
      </div>
    </div>
  </div>

  <!-- Mobile menu -->
  <div id="mobilePanel" class="md:hidden hidden border-t border-greenDark/10 bg-white/95 backdrop-blur-lg shadow-soft">
    <div class="px-4 py-4 space-y-2 font-medium">
      <a class="block px-4 py-3 rounded-xl text-sage-700 hover:bg-cream-100 hover:text-greenDark transition-all duration-300" href="<?php echo $adminRoot ?: '../gallery.php'; ?>">Home</a>
      <a class="block px-4 py-3 rounded-xl text-sage-700 hover:bg-cream-100 hover:text-greenDark transition-all duration-300" href="<?php echo $viewBase; ?>../gallery.php">Gallery</a>
      <a class="block px-4 py-3 rounded-xl text-sage-700 hover:bg-cream-100 hover:text-greenDark transition-all duration-300" href="<?php echo $viewBase; ?>../accomodations.php">Accommodation</a>
      <a class="block px-4 py-3 rounded-xl text-sage-700 hover:bg-cream-100 hover:text-greenDark transition-all duration-300" href="<?php echo $viewBase; ?>../ameties.php">Amenities</a>
      <a class="block px-4 py-3 rounded-xl text-sage-700 hover:bg-cream-100 hover:text-greenDark transition-all duration-300" href="<?php echo $viewBase; ?>/feedback.php">Feedback</a>
    </div>
  </div>
</header>


<script>
  $(function(){
    $('#mobileMenuBtn').on('click', function(){
      $('#mobilePanel').slideToggle(200);
    });

    $('#moreBtn').on('click', function(e){
      e.stopPropagation();
      $('#moreMenu').toggleClass('hidden');
    });
    $(document).on('click', function(){ $('#moreMenu').addClass('hidden'); });

    // normalize to last path segment (works with relative links like view/*.php)
    const currentSegment = (window.location.pathname.replace(/\/+$/,'').split('/').pop() || 'index.php').toLowerCase();
    $('a.nav-link, #mobilePanel a').each(function(){
      const href = ($(this).attr('href')||'').toLowerCase();
      const hrefSegment = href.replace(/\/+$/,'').split('/').pop();
      if (hrefSegment && hrefSegment.toLowerCase() === currentSegment) {
        $(this).addClass('text-greenDark font-semibold bg-gold/10 shadow-glow-green');
        $(this).find('span').addClass('text-greenDark');
      }
    });
  });
</script>
