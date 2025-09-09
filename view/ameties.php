<?php
session_start();

// Initialize controller
require_once __DIR__ . '/../controller/AmentiesController.php';
$controller = new AmentiesController();

// Handle actions
$action = $_GET['action'] ?? $_POST['action'] ?? 'index';
$id = $_GET['id'] ?? null;

// Process actions without including view
switch ($action) {
    case 'create':
        $controller->create();
        break;
    case 'edit':
        if ($id) $controller->edit($id);
        break;
    case 'delete':
        if ($id) $controller->delete($id);
        break;
}

// Get data for display
$amenties = $controller->model->getAllAmenties();
$statusTypes = $controller->model->getStatusTypes();

include_once __DIR__ . '/layout/header.php';
?>
<!doctype html>
<html lang="en">
<head>
<!-- head content unchanged -->
</head>
<body class="antialiased font-din bg-gray-50 text-gray-800 min-h-screen">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

  <!-- Header Section -->
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-12">
    <div>
      <h1 class="text-4xl font-bold text-gray-900 mb-2">Amenties Management</h1>
      <p class="text-gray-600">Manage your resort amenties and services</p>
    </div>
    <button onclick="showCreateForm()" 
            class="mt-4 sm:mt-0 inline-flex items-center gap-2 bg-green-600 text-white px-6 py-3 rounded-xl font-medium shadow-md hover:bg-green-700 hover:shadow-lg transition">
      <span class="text-lg">➕</span>
      <span>Add New Amenties</span>
    </button>
  </div>

  <!-- Messages -->
  <?php if (isset($_SESSION['amenties_alert'])): ?>
      <?php $alert = $_SESSION['amenties_alert']; unset($_SESSION['amenties_alert']); ?>
      <?php if (!empty($alert['success'])): ?>
          <div class="mb-6 p-4 bg-green-100 text-green-800 rounded-lg">
              <?php foreach ($alert['success'] as $message): ?>
                  <p><?= htmlspecialchars($message) ?></p>
              <?php endforeach; ?>
          </div>
      <?php endif; ?>
      <?php if (!empty($alert['errors'])): ?>
          <div class="mb-6 p-4 bg-red-100 text-red-800 rounded-lg">
              <?php foreach ($alert['errors'] as $message): ?>
                  <p><?= htmlspecialchars($message) ?></p>
              <?php endforeach; ?>
          </div>
      <?php endif; ?>
  <?php endif; ?>

  <!-- Create/Edit Form -->
  <div id="amentiesForm" class="hidden w-full max-w-3xl mx-auto bg-white rounded-2xl shadow-lg p-8 mb-12 border border-gray-200">
    <h2 id="formTitle" class="text-2xl font-bold text-gray-900 mb-6">Add New Amenties</h2>
    <form method="POST" enctype="multipart/form-data" id="amentiesFormElement" class="space-y-6">
      <input type="hidden" name="action" value="create">
      
      <!-- Title & Type -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="font-semibold text-gray-700">Title <span class="text-red-500">*</span></label>
          <input type="text" name="title" required
                 class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-200 focus:border-green-500">
        </div>
        <div>
          <label class="font-semibold text-gray-700">Type <span class="text-red-500">*</span></label>
          <select name="status_type" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-200 focus:border-green-500">
            <option value="">Select Type</option>
            <?php foreach ($statusTypes as $key => $label): ?>
              <option value="<?= $key ?>"><?= htmlspecialchars($label) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <!-- Description -->
      <div>
        <label class="font-semibold text-gray-700">Description <span class="text-red-500">*</span></label>
        <textarea name="description" rows="4" required
                  class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-200 focus:border-green-500"></textarea>
      </div>

      <!-- Images -->
      <div>
        <label class="font-semibold text-gray-700">Images (1-10) <span class="text-red-500">*</span></label>
        <input type="file" name="images[]" multiple accept="image/*" required
               id="images"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-200 focus:border-green-500">
        <div id="imagePreview" class="flex flex-wrap gap-3 mt-2"></div>
      </div>

      <!-- Buttons -->
      <div class="flex gap-4 justify-end mt-6">
        <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-xl font-medium shadow hover:bg-green-700 transition">Save</button>
        <button type="button" onclick="hideForm()" class="bg-gray-100 text-gray-800 px-6 py-2 rounded-xl font-medium hover:bg-gray-200 transition">Cancel</button>
      </div>
    </form>
  </div>

  <!-- Amenties Cards -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
    <?php foreach ($amenties as $a): ?>
      <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-200 flex flex-col transition transform hover:-translate-y-1 hover:shadow-2xl">
        
        <!-- Image Carousel -->
        <div class="relative w-full h-64 bg-gray-100 flex items-center justify-center overflow-hidden">
          <?php if (!empty($a['images'])): ?>
            <img id="amenties-img-<?= $a['id'] ?>" 
                 src="/amenties/<?= htmlspecialchars($a['images'][0]['filename']) ?>" 
                 alt="<?= htmlspecialchars($a['title']) ?>" 
                 class="w-full h-64 object-cover">
            
            <button onclick="prevImage(<?= $a['id'] ?>, <?= count($a['images']) ?>)" 
                    class="absolute left-2 top-1/2 transform -translate-y-1/2 bg-black/30 text-white px-3 py-1 rounded-full hover:bg-black/50">&larr;</button>
            <button onclick="nextImage(<?= $a['id'] ?>, <?= count($a['images']) ?>)" 
                    class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-black/30 text-white px-3 py-1 rounded-full hover:bg-black/50">&rarr;</button>

            <div id="amenties-images-<?= $a['id'] ?>" class="hidden">
              <?php foreach ($a['images'] as $img): ?>
                <span><?= htmlspecialchars($img['filename']) ?></span>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <div class="w-full h-64 flex items-center justify-center text-gray-400 text-lg">No Image</div>
          <?php endif; ?>
        </div>

        <!-- Info -->
        <div class="p-6 flex-1 flex flex-col gap-2">
          <div class="flex items-center justify-between mb-2">
            <h3 class="text-lg font-semibold text-gray-900"><?= htmlspecialchars($a['title']) ?></h3>
            <span class="px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700"><?= htmlspecialchars($statusTypes[$a['status_type']]) ?></span>
          </div>
          <p class="text-gray-600 text-sm mb-2"><?= htmlspecialchars(substr($a['description'], 0, 100)) ?><?= strlen($a['description']) > 100 ? '...' : '' ?></p>
        </div>

      </div>
    <?php endforeach; ?>
  </div>

</div>

<script>
function prevImage(id, total) {
    const img = document.getElementById(`amenties-img-${id}`);
    let index = parseInt(img.dataset.index || 0);
    index = (index - 1 + total) % total;
    const filenames = Array.from(document.getElementById(`amenties-images-${id}`).children).map(el => el.textContent);
    img.src = `/amenties/${filenames[index]}`;
    img.dataset.index = index;
}

function nextImage(id, total) {
    const img = document.getElementById(`amenties-img-${id}`);
    let index = parseInt(img.dataset.index || 0);
    index = (index + 1) % total;
    const filenames = Array.from(document.getElementById(`amenties-images-${id}`).children).map(el => el.textContent);
    img.src = `/amenties/${filenames[index]}`;
    img.dataset.index = index;
}

function showCreateForm() {
    document.getElementById('amentiesForm').style.display = 'block';
    document.getElementById('formTitle').textContent = 'Add New Amenties';
    document.getElementById('amentiesFormElement').reset();
    document.getElementById('imagePreview').innerHTML = '';
}

function hideForm() {
    document.getElementById('amentiesForm').style.display = 'none';
}
</script>
</body>
</html>
