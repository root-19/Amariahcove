<?php
session_start();

// Directory paths (compute admin root so URLs work regardless of host/subdir)
$script = str_replace('\\','/', $_SERVER['SCRIPT_NAME'] ?? '');
$pos = strpos($script, '/admin');
$adminRoot = ($pos !== false) ? substr($script, 0, $pos + 6) : '';
$projectRoot = ($pos !== false) ? substr($script, 0, $pos) : '';

// Store images in admin/gallery directory
$webPrefix = rtrim($adminRoot, '/') . '/gallery'; // URL prefix for images
$storageDir = __DIR__ . '/../gallery'; // Physical storage directory
$uploadDir = $storageDir; // <-- Set $uploadDir to the actual folder path
$uploadUrl = $webPrefix;  // <-- Set $uploadUrl for URLs

// Ensure the directory exists
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Initialize empty uploads array - will be populated from database only
$uploads = [];

// DB config - change these to your DB values
$dbHost = 'localhost';
$dbName = 'amariah';
$dbUser = 'root';
$dbPass = '';
$pdo = null;
$db_failed = [];

try {
    require_once __DIR__ . '/../database/database.php';
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    // Ensure table exists (adjust schema if you already have your own)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS gallery_images (
            id INT AUTO_INCREMENT PRIMARY KEY,
            message VARCHAR(255) NOT NULL,
            filename VARCHAR(255) NOT NULL,
            filepath VARCHAR(255) NOT NULL,
            filehash VARCHAR(64) DEFAULT NULL,
            uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
} catch (Exception $ex) {
    $pdo = null;
    $db_failed[] = 'DB connection failed: ' . $ex->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['images'])) {
    $maxFiles = 5;
    $allowedExt = ['jpg','jpeg','png','gif','webp'];
    $maxSize = 5 * 1024 * 1024; // 5MB
    $errors = $success = $already = [];
    $files = $_FILES['images'];
    $names = array_filter($files['name']);
    $total = count($names);

    $saved = 0;
    for ($i = 0; $i < $total && $saved < $maxFiles; $i++) {
        if ($files['error'][$i] !== UPLOAD_ERR_OK) {
            $errors[] = $files['name'][$i] . ' upload error';
            continue;
        }
        $origName = $files['name'][$i];
        $tmp = $files['tmp_name'][$i];
        $size = $files['size'][$i];
        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExt)) {
            $errors[] = "$origName: invalid type";
            continue;
        }
        if ($size > $maxSize) {
            $errors[] = "$origName: too large";
            continue;
        }

        $safeName = bin2hex(random_bytes(8)) . '_' . preg_replace('/[^A-Za-z0-9._-]/', '_', $origName);
        $target = $storageDir . '/' . $safeName;

        if (file_exists($target)) {
            $already[] = $origName;
            continue;
        }

        if (!move_uploaded_file($tmp, $target)) {
            $errors[] = "$origName: could not save";
            continue;
        }

        // Insert into DB if connected
if ($pdo) {
    try {
        $filehash = hash_file('sha256', $target);
        $stmt = $pdo->prepare('INSERT INTO gallery_images (message, filename, filepath, filehash, created_at) VALUES (:message,:filename, :filepath, :filehash, :created_at)');
        $stmt->execute([
            ':message' => $_POST['message'] ?? null, // <-- fix dito
            ':filename' => $safeName,
            ':filepath' => $webPrefix . '/' . $safeName,
            ':filehash' => $filehash,
            ':created_at' => date('Y-m-d H:i:s'),
        ]);
    } catch (Exception $ex) {
        $db_failed[] = $origName . ': DB insert failed - ' . $ex->getMessage();
    }
}

$success[] = $origName;
$saved++;
    }

    $_SESSION['gallery_alert'] = [
        'errors' => $errors,
        'success' => $success,
        'already' => $already,
        'db_failed' => $db_failed
    ];

    // Redirect to avoid resubmission and refresh listing (PRG). No output has been sent yet.
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

// Delete image handling
if (isset($_POST['delete_image']) && !empty($_POST['image_id'])) {
    $imageId = (int)$_POST['image_id'];
    
    if ($pdo) {
        try {
            // Get the filename first
            $stmt = $pdo->prepare("SELECT filename FROM gallery_images WHERE id = ?");
            $stmt->execute([$imageId]);
            $image = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($image) {
                // Delete from database
                $stmt = $pdo->prepare("DELETE FROM gallery_images WHERE id = ?");
                $stmt->execute([$imageId]);
                
                // Delete file from storage (admin/gallery only)
                $filename = $image['filename'];
                $filePath = $storageDir . '/' . $filename;
                
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
                
                $_SESSION['gallery_alert'] = [
                    'success' => ['Image deleted successfully'],
                    'errors' => [],
                    'already' => [],
                    'db_failed' => []
                ];
            } else {
                $_SESSION['gallery_alert'] = [
                    'success' => [],
                    'errors' => ['Image not found in database'],
                    'already' => [],
                    'db_failed' => []
                ];
            }
        } catch (Exception $ex) {
            $_SESSION['gallery_alert'] = [
                'success' => [],
                'errors' => ['Failed to delete image: ' . $ex->getMessage()],
                'already' => [],
                'db_failed' => []
            ];
        }
    } else {
        // Fallback: delete from filesystem only
        $uploads = array_filter($uploads, function($u) use ($imageId) {
            return $u['id'] != $imageId;
        });
        
        $_SESSION['gallery_alert'] = [
            'success' => ['Image removed from display'],
            'errors' => [],
            'already' => [],
            'db_failed' => []
        ];
    }
    
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

// Populate $uploads: ONLY from database
if ($pdo) {
    try {
        $stmt = $pdo->query('SELECT id, filename, filepath FROM gallery_images ORDER BY created_at DESC LIMIT 100');
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $r) {
            $uploads[] = [
                'id' => $r['id'],
                'filename' => $r['filename'],
                'filepath' => rtrim($adminRoot, '/') . '/view/image.php?p=' . rawurlencode($r['filename'])
            ];
        }
    } catch (Exception $ex) {
        $db_failed[] = 'Could not read gallery from DB: ' . $ex->getMessage();
        $pdo = null;
    }
}

// If no database connection, show empty gallery
if (!$pdo) {
    $uploads = [];
}


include_once __DIR__ . '/layout/header.php';

?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gallery Management - Amariah Resort</title>
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
    <div class="text-center mb-12 animate-fade-in">
        <h1 class="text-4xl font-heading font-bold text-greenDark mb-4 animate-slide-down">
            Gallery Management
        </h1>
        <p class="text-lg text-sage-600 max-w-2xl mx-auto leading-relaxed animate-fade-in-delay">
            Upload and manage your resort images with ease
        </p>
    </div>

    <!-- Upload Section -->
    <div class="bg-white rounded-2xl shadow-strong p-8 mb-12 border border-greenDark/10 animate-slide-up">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
            <div>
                <h2 class="text-2xl font-heading font-semibold text-greenDark mb-2">Upload Images</h2>
                <p class="text-sage-600">Allowed: jpg, png, gif, webp • Max 5MB each • Up to 5 images</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gold/10 text-gold-700">
                    📸 Gallery Manager
                </span>
            </div>
        </div>

        <form id="uploadForm" method="post" enctype="multipart/form-data">
            <label class="group relative block w-full border-2 border-dashed border-sage-300 rounded-2xl p-12 text-center hover:border-greenDark hover:bg-greenDark/5 transition-all duration-300 cursor-pointer" id="dropZone">
                <input type="file" id="images" name="images[]" multiple accept="image/*" class="hidden">
                <div class="space-y-4">
                    <div class="text-6xl group-hover:scale-110 transition-transform duration-300">📁</div>
                    <div>
                        <p class="text-xl font-semibold text-greenDark mb-2">Drag & drop images here</p>
                        <p class="text-sage-600">or click to select files (max 5)</p>
                    </div>
                </div>
            </label>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4 mt-8" id="preview"></div>

             <!-- ✅ Message / Description Textarea -->
    <div class="mt-6">
        <label for="galleryMessage" class="block text-sm font-medium text-greenDark mb-2">Message / Description</label>
        <textarea id="galleryMessage" name="message" rows="3" 
            class="w-full p-3 border border-sage-300 rounded-xl focus:ring-2 focus:ring-greenDark focus:border-greenDark text-gray-700 resize-none" 
            placeholder="Write a short message or description for your upload..."></textarea>
    </div>


            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mt-8">
                <button type="submit" class="inline-flex items-center gap-2 bg-gradient-to-r from-greenDark to-greenDark-800 text-white px-8 py-3 rounded-xl font-medium hover:shadow-glow-green transition-all duration-300 transform hover:scale-105">
                    
                    <span class="text-lg text-black">Upload Images</span>
                </button>
                <div class="text-sm text-sage-600 font-medium" id="countInfo">0 / 5 selected</div>
            </div>
        </form>
    </div>

    <!-- Gallery Section -->
    <div class="bg-white rounded-2xl shadow-strong p-8 border border-greenDark/10 animate-fade-in">
        <div class="flex items-center justify-between mb-8">
            <h3 class="text-2xl font-heading font-semibold text-greenDark">Recent Uploads</h3>
            <div class="text-sm text-sage-600">
                <?= count($uploads) ?> image<?= count($uploads) !== 1 ? 's' : '' ?> total
            </div>
        </div>
        
        <?php if (empty($uploads)): ?>
            <div class="text-center py-12">
                <div class="text-6xl mb-4">🖼️</div>
                <h4 class="text-xl font-semibold text-sage-600 mb-2">No images uploaded yet</h4>
                <p class="text-sage-500">Upload some images to get started!</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <?php foreach ($uploads as $u): ?>
                    <div class="group relative bg-sage-50 rounded-xl overflow-hidden hover:shadow-medium transition-all duration-300 transform hover:-translate-y-1">
                        <img src="<?= $u['filepath'] ?>" 
                             alt="<?= $u['filename'] ?>" 
                             class="w-full h-32 object-cover group-hover:scale-110 transition-transform duration-300">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        
                        <!-- Delete Button -->
                        <form method="post" class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300" 
                              onsubmit="return confirm('Are you sure you want to delete this image?');">
                            <input type="hidden" name="image_id" value="<?php echo htmlspecialchars($u['id']); ?>">
                            <button type="submit" name="delete_image" 
                                    class="w-8 h-8 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center text-sm font-bold transition-colors duration-200 shadow-medium"
                                    title="Delete image">
                                ×
                            </button>
                        </form>
                        
                  
                        <!-- Image Info -->
<div class="absolute bottom-0 left-0 right-0 p-2 bg-gradient-to-t from-black/80 to-transparent text-white text-xs opacity-0 group-hover:opacity-100 transition-opacity duration-300">
    <p class="truncate font-semibold"><?= htmlspecialchars($u['filename']) ?></p>
    <?php if (!empty($u['message'])): ?>
        <p class="truncate italic text-sage-200">“<?= htmlspecialchars($u['message']) ?>”</p>
    <?php endif; ?>
</div>

                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php
// ✅ Handle session alerts here (SweetAlert only)
if (!empty($_SESSION['gallery_alert'])) {
    $a = $_SESSION['gallery_alert'];
    unset($_SESSION['gallery_alert']);

    $errors = $a['errors'] ?? [];
    $success = $a['success'] ?? [];
    $already = $a['already'] ?? [];
    $db_failed = $a['db_failed'] ?? [];
    $msg = '';
    $type = 'info';

    if ($errors) {
        $msg = '<strong>Errors:</strong><ul><li>' . implode('</li><li>', $errors) . '</li></ul>';
        $type = 'error';
    } elseif ($db_failed) {
        $msg = '<strong>Database Errors:</strong><ul><li>' . implode('</li><li>', $db_failed) . '</li></ul>';
        $type = 'error';
    } elseif ($already) {
        $msg = '<strong>Already Uploaded:</strong><ul><li>' . implode('</li><li>', $already) . '</li></ul>';
        $type = 'warning';
    } elseif ($success) {
        $msg = '<strong>Success:</strong><ul><li>' . implode('</li><li>', $success) . '</li></ul>';
        $type = 'success';
    }

    echo "<script>Swal.fire({html: " . json_encode($msg) . ", icon: '{$type}'});</script>";
}
?>

<script>

const drop = document.getElementById('dropZone');
const input = document.getElementById('images');
const preview = document.getElementById('preview');
const countInfo = document.getElementById('countInfo');
let filesList = [];

// click dropzone to open file picker
drop.addEventListener('click', () => input.click());

// drag events
drop.addEventListener('dragover', e => {
    e.preventDefault();
    drop.classList.add('border-greenDark', 'bg-greenDark/5');
});
drop.addEventListener('dragleave', () => {
    drop.classList.remove('border-greenDark', 'bg-greenDark/5');
});
drop.addEventListener('drop', e => {
    e.preventDefault();
    drop.classList.remove('border-greenDark', 'bg-greenDark/5');
    handleFiles(e.dataTransfer.files);
});

// file input change
input.addEventListener('change', e => handleFiles(e.target.files));

function handleFiles(fileList) {
    const max = 5;
    for (let f of fileList) {
        if (filesList.length >= max) break;
        if (!f.type.startsWith('image/')) continue;
        filesList.push(f);
    }
    syncInputFiles();
    renderPreview();
}

function renderPreview() {
    preview.innerHTML = '';
    filesList.forEach((f, idx) => {
        const div = document.createElement('div');
        div.className = 'relative bg-sage-50 rounded-xl overflow-hidden group';

        const img = document.createElement('img');
        img.className = 'w-full h-24 object-cover';
        const reader = new FileReader();
        reader.onload = e => img.src = e.target.result;
        reader.readAsDataURL(f);

        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'absolute top-2 right-2 w-6 h-6 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center text-xs font-bold transition-colors duration-200 opacity-0 group-hover:opacity-100';
        btn.textContent = '×';
        btn.onclick = () => {
            filesList.splice(idx, 1);
            syncInputFiles();
            renderPreview();
        };

        div.appendChild(img);
        div.appendChild(btn);
        preview.appendChild(div);
    });

    countInfo.textContent = `${filesList.length} / 5 selected`;
}

// ✅ always keep input.files in sync with filesList
function syncInputFiles() {
    const dt = new DataTransfer();
    filesList.forEach(f => dt.items.add(f));
    input.files = dt.files;
}

// prevent empty submit
document.getElementById('uploadForm').addEventListener('submit', function(e) {
    if (filesList.length === 0) {
        e.preventDefault();
        Swal.fire({
            icon: 'warning',
            title: 'No Images Selected',
            text: 'Please select at least one image to upload.',
            confirmButtonColor: '#014421'
        });
    }
});

</script>
</body>
</html>
