<?php
session_start();

// Initialize controller
require_once __DIR__ . '/../controller/AccommodationController.php';
$controller = new AccommodationController();

// Handle actions
$action = $_GET['action'] ?? $_POST['action'] ?? 'index';
$id = $_GET['id'] ?? null;

// Process actions without including view
switch ($action) {
    case 'create':
        $controller->create();
        break;
    case 'edit':
        if ($id) {
            $controller->edit($id);
        }
        break;
    case 'delete':
        if ($id) {
            $controller->delete($id);
        }
        break;
}

// Get data for display
$accommodations = $controller->model->getAllAccommodations();
$statusTypes = $controller->model->getStatusTypes();

include_once __DIR__ . '/layout/header.php';
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Accommodation Management - Amariah Resort</title>
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<!-- DIN Font -->
<link href="https://fonts.cdnfonts.com/css/din" rel="stylesheet">
<!-- Tailwind CSS -->
<script src="https://cdn.tailwindcss.com"></script>
<!-- SweetAlert2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css" rel="stylesheet">
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
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-12 animate-fade-in">
        <div>
            <h1 class="text-4xl font-heading font-bold text-greenDark mb-2 animate-slide-down">Accommodation Management</h1>
            <p class="text-lg text-sage-600 animate-fade-in-delay">Manage your resort accommodations and properties</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-4 mt-6 sm:mt-0">
            <button onclick="showCreateForm()" class="inline-flex items-center gap-2 bg-green-800 text-white px-6 py-3 rounded-xl font-medium hover:shadow-glow-green transition-all duration-300 transform hover:scale-105">
                <span class="text-lg">➕</span>
                <span >Add New Accommodation</span>
            </button>
        </div>
    </div>



    <!-- Success/Error Messages -->
    <?php if (isset($_SESSION['accommodation_alert'])): ?>
        <?php $alert = $_SESSION['accommodation_alert']; unset($_SESSION['accommodation_alert']); ?>
        <?php if (!empty($alert['success'])): ?>
            <div class="alert alert-success">
                <?php foreach ($alert['success'] as $message): ?>
                    <div><?= htmlspecialchars($message) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($alert['errors'])): ?>
            <div class="alert alert-error">
                <?php foreach ($alert['errors'] as $message): ?>
                    <div><?= htmlspecialchars($message) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Create/Edit Form -->
    <div id="accommodationForm" class="w-full max-w-2xl mx-auto bg-white rounded-2xl shadow-lg p-8 mb-10 border border-green-200 animate-fade-in" style="display: none;">
    <h2 id="formTitle" class="text-2xl font-bold text-green-800 mb-6">Add New Accommodation</h2>
    <form method="POST" enctype="multipart/form-data" id="accommodationFormElement" class="space-y-6">
        <input type="hidden" name="action" value="create">

        <!-- Title & Type -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="flex flex-col gap-2">
                <label for="title" class="font-medium text-green-800">Title <span class="text-red-500">*</span></label>
                <input type="text" id="title" name="title" required 
                    class="border border-gray-300 rounded-lg px-4 py-2 focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none transition">
            </div>
            <div class="flex flex-col gap-2">
                <label for="status_type" class="font-medium text-green-800">Type <span class="text-red-500">*</span></label>
                <select id="status_type" name="status_type" required 
                    class="border border-gray-300 rounded-lg px-4 py-2 focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none transition">
                    <option value="">Select Type</option>
                    <?php foreach ($statusTypes as $key => $label): ?>
                        <option value="<?= $key ?>"><?= htmlspecialchars($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Description -->
        <div class="flex flex-col gap-2">
            <label for="description" class="font-medium text-green-800">Description <span class="text-red-500">*</span></label>
            <textarea id="description" name="description" rows="4" required 
                class="border border-gray-300 rounded-lg px-4 py-2 focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none transition"></textarea>
        </div>

        <!-- Images -->
        <div class="flex flex-col gap-2">
            <label class="font-medium text-green-800">Images (at least 1, up to 10) <span class="text-red-500">*</span></label>
            <input type="file" id="images" name="images[]" multiple accept="image/*" required 
                class="border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none transition">
            <div class="flex flex-wrap gap-3 mt-2" id="imagePreview"></div>
        </div>

        <!-- Buttons -->
        <div class="flex gap-4 justify-end mt-6">
            <button type="submit" class="bg-gradient-to-r from-green-500 to-green-700 text-white px-6 py-2 rounded-xl font-medium hover:shadow-lg transition transform hover:scale-105">
                Save Accommodation
            </button>
            <button type="button" class="bg-gray-100 text-green-700 px-6 py-2 rounded-xl font-medium hover:bg-gray-200 transition" onclick="hideForm()">
                Cancel
            </button>
        </div>
    </form>
</div>


    <!-- Accommodations List -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 mt-10">
  <?php foreach ($accommodations as $accommodation): ?>
    <div class="bg-white rounded-2xl shadow-medium border border-sage-100 flex flex-col overflow-hidden hover:shadow-strong transition-shadow duration-300 animate-fade-in">

        <!-- Image Carousel -->
        <div class="relative w-full h-56 bg-sage-50 flex items-center justify-center overflow-hidden">
            <?php if (!empty($accommodation['images'])): ?>
                <img 
                    src="/accommodations/<?= htmlspecialchars($accommodation['images'][0]['filename']) ?>" 
                    alt="<?= htmlspecialchars($accommodation['title']) ?>" 
                    class="w-full h-56 object-cover object-center" 
                    id="accommodation-img-<?= $accommodation['id'] ?>"
                    data-index="0"
                >
                
                <!-- Left/Right Buttons -->
                <button onclick="prevImage(<?= $accommodation['id'] ?>, <?= count($accommodation['images']) ?>)" 
                        class="absolute left-2 top-1/2 transform -translate-y-1/2 bg-black/30 text-white px-2 py-1 rounded-full hover:bg-black/50">&larr;</button>
                <button onclick="nextImage(<?= $accommodation['id'] ?>, <?= count($accommodation['images']) ?>)" 
                        class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-black/30 text-white px-2 py-1 rounded-full hover:bg-black/50">&rarr;</button>

                <!-- Store all filenames in data attribute -->
                <div id="accommodation-images-<?= $accommodation['id'] ?>" class="hidden">
                    <?php foreach ($accommodation['images'] as $u): ?>
                        <span><?= htmlspecialchars($u['filename']) ?></span>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="w-full h-56 flex items-center justify-center bg-sage-50 text-sage-400 text-lg">No Image</div>
            <?php endif; ?>
        </div>

        <!-- Accommodation Info -->
        <div class="flex-1 flex flex-col p-6 gap-2">
            <div class="flex items-center justify-between mb-1">
                <div class="text-lg font-heading font-semibold text-greenDark"><?= htmlspecialchars($accommodation['title']) ?></div>
                <div class="px-3 py-1 rounded-full text-xs font-semibold bg-greenDark-50 text-greenDark-700 border border-greenDark-100">
                    <?= htmlspecialchars($statusTypes[$accommodation['status_type']]) ?>
                </div>
            </div>
            <div class="text-sage-700 text-sm mb-2">
                <?= htmlspecialchars(substr($accommodation['description'], 0, 100)) ?><?= strlen($accommodation['description']) > 100 ? '...' : '' ?>
            </div>
            <div class="flex flex-wrap gap-2 text-xs text-sage-600 mb-2">
                <?php if ($accommodation['price_per_night']): ?>
                    <div><strong>Price:</strong> $<?= number_format($accommodation['price_per_night'], 2) ?>/night</div>
                <?php endif; ?>
                <?php if ($accommodation['max_guests']): ?>
                    <div><strong>Max Guests:</strong> <?= $accommodation['max_guests'] ?></div>
                <?php endif; ?>
                <?php if ($accommodation['location']): ?>
                    <div><strong>Location:</strong> <?= htmlspecialchars($accommodation['location']) ?></div>
                <?php endif; ?>
                <div><strong>Images:</strong> <?= count($accommodation['images']) ?></div>
            </div>
        </div>
    </div>
<?php endforeach; ?>
</div>

<!-- JS for carousel -->
<script>
function prevImage(id, total) {
    const img = document.getElementById(`accommodation-img-${id}`);
    let index = parseInt(img.dataset.index);
    index = (index - 1 + total) % total;
    const filenames = Array.from(document.getElementById(`accommodation-images-${id}`).children).map(el => el.textContent);
    img.src = `/accommodations/${filenames[index]}`;
    img.dataset.index = index;
}

function nextImage(id, total) {
    const img = document.getElementById(`accommodation-img-${id}`);
    let index = parseInt(img.dataset.index);
    index = (index + 1) % total;
    const filenames = Array.from(document.getElementById(`accommodation-images-${id}`).children).map(el => el.textContent);
    img.src = `/accommodations/${filenames[index]}`;
    img.dataset.index = index;
}
</script>


<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>

<script>
// Show SweetAlert based on session data
<?php if (isset($_SESSION['accommodation_alert'])): ?>
    <?php $alert = $_SESSION['accommodation_alert']; unset($_SESSION['accommodation_alert']); ?>
    <?php if (!empty($alert['success'])): ?>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '<?= implode(', ', $alert['success']) ?>',
            confirmButtonColor: '#10b981'
        });
    <?php endif; ?>
    <?php if (!empty($alert['errors'])): ?>
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: '<?= implode(', ', $alert['errors']) ?>',
            confirmButtonColor: '#ef4444'
        });
    <?php endif; ?>
<?php endif; ?>

function showCreateForm() {
    document.getElementById('accommodationForm').style.display = 'block';
    document.getElementById('formTitle').textContent = 'Add New Accommodation';
    document.getElementById('accommodationFormElement').reset();
    document.getElementById('imagePreview').innerHTML = '';
}

function hideForm() {
    document.getElementById('accommodationForm').style.display = 'none';
}

// Show loading when form is submitted
function showLoading() {
    Swal.fire({
        title: 'Processing...',
        text: 'Please wait while we save your accommodation',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
}

// Image preview functionality
document.getElementById('images').addEventListener('change', function(e) {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    
    Array.from(e.target.files).forEach((file, index) => {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const imageItem = document.createElement('div');
                imageItem.className = 'image-item';
                imageItem.innerHTML = `
                    <img src="${e.target.result}" alt="Preview">
                    <input type="text" name="image_descriptions[]" placeholder="Image description" value="">
                `;
                preview.appendChild(imageItem);
            };
            reader.readAsDataURL(file);
        }
    });
});

// Enhanced delete confirmation
function confirmDelete(url) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
}

// Show form if there are validation errors
<?php if ($action === 'create' && !empty($alert['errors'])): ?>
    showCreateForm();
<?php endif; ?>
</script>
</body>
</html>
