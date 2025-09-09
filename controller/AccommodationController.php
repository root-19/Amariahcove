<?php
class AccommodationController
{
    public $model;
    protected $uploadDir;

    public function __construct()
    {
        require_once __DIR__ . '/../model/AccommodationModel.php';
        $this->model = new AccommodationModel();

        // Use admin/accommodations directory for accommodation images
        $this->uploadDir = __DIR__ . '/../accommodations/';
        
        // Ensure directory exists and is writable
        if (!is_dir($this->uploadDir)) {
            if (!mkdir($this->uploadDir, 0755, true) && !is_dir($this->uploadDir)) {
                error_log("AccommodationController::__construct - failed to create upload dir: {$this->uploadDir}");
            }
        }
        if (!is_writable($this->uploadDir)) {
            @chmod($this->uploadDir, 0755);
            if (!is_writable($this->uploadDir)) {
                error_log("AccommodationController::__construct - upload dir not writable: {$this->uploadDir}");
            }
        }
    }

    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Just return data, don't include view
        return [
            'accommodations' => $this->model->getAllAccommodations(),
            'statusTypes' => $this->model->getStatusTypes()
        ];
    }

    public function create()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Debug: Log that create method is called
        error_log("AccommodationController::create - Method called");
        
        $errors = [];
        $success = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Debug: Log the received data
            error_log("AccommodationController::create - POST data: " . print_r($_POST, true));
            error_log("AccommodationController::create - FILES data: " . print_r($_FILES, true));
            
            // Validate form data
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $statusType = $_POST['status_type'] ?? '';

            // Validation
            if (empty($title)) {
                $errors[] = 'Title is required';
            }
            if (empty($description)) {
                $errors[] = 'Description is required';
            }
            if (!in_array($statusType, ['vip_pools', 'hotel_rooms', 'glamping'])) {
                $errors[] = 'Invalid status type';
            }
            if (empty($_FILES['images']['name'][0])) {
                $errors[] = 'At least one image is required';
            }

            if (empty($errors)) {
                // Process accommodation data
                $accommodationData = [
                    'title' => $title,
                    'description' => $description,
                    'status_type' => $statusType,
                ];

                // Debug: Log accommodation data
                error_log("AccommodationController::create - Accommodation data: " . print_r($accommodationData, true));

                // Create accommodation
                $accommodationId = $this->model->createAccommodation($accommodationData);
                
                // Debug: Log accommodation ID
                error_log("AccommodationController::create - Accommodation ID: " . $accommodationId);

                if ($accommodationId) {
                    // Process uploaded images
                    $images = $this->processImages($_FILES['images'], $accommodationId);
                    
                    // Debug: Log processed images
                    error_log("AccommodationController::create - Processed images: " . print_r($images, true));
                    
                    if (!empty($images)) {
                        if ($this->model->addImages($accommodationId, $images)) {
                            $success[] = 'Accommodation created successfully with ' . count($images) . ' images';
                        } else {
                            $errors[] = 'Accommodation created but failed to save images';
                        }
                    } else {
                        $errors[] = 'Accommodation created but no valid images were uploaded';
                    }
                } else {
                    $errors[] = 'Failed to create accommodation';
                }
            }
        }

        // Check if this is an AJAX request
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            // Return JSON response for AJAX
            header('Content-Type: application/json');
            echo json_encode([
                'success' => empty($errors),
                'errors' => $errors,
                'messages' => $success
            ]);
            exit;
        }

        // Store results in session for display
        $_SESSION['accommodation_alert'] = [
            'errors' => $errors,
            'success' => $success
        ];

        // Don't redirect - let the page display the results
        // The form will show success/error messages via SweetAlert
    }

    private function processImages($files, $accommodationId)
    {
        $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        $maxFiles = 10;
        $processedImages = [];
        $errors = [];

        // Debug: Log upload directory info
        error_log("AccommodationController::processImages - Upload dir: " . $this->uploadDir);
        error_log("AccommodationController::processImages - Upload dir exists: " . (is_dir($this->uploadDir) ? 'YES' : 'NO'));
        error_log("AccommodationController::processImages - Upload dir writable: " . (is_writable($this->uploadDir) ? 'YES' : 'NO'));

        if (!is_array($files['name'])) {
            $files = [
                'name' => [$files['name']],
                'type' => [$files['type']],
                'tmp_name' => [$files['tmp_name']],
                'error' => [$files['error']],
                'size' => [$files['size']]
            ];
        }

        $fileCount = count($files['name']);
        $saved = 0;

        for ($i = 0; $i < $fileCount && $saved < $maxFiles; $i++) {
            if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                $errors[] = "File " . ($i + 1) . " upload error: " . $this->getUploadErrorMessage($files['error'][$i]);
                continue;
            }

            $origName = $files['name'][$i];
            $tmpName = $files['tmp_name'][$i];
            $size = $files['size'][$i];
            $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

            if (!in_array($ext, $allowedExt)) {
                $errors[] = "File " . ($i + 1) . " ($origName): Invalid file type. Allowed: " . implode(', ', $allowedExt);
                continue;
            }
            if ($size > $maxSize) {
                $errors[] = "File " . ($i + 1) . " ($origName): File too large. Max size: " . ($maxSize / 1024 / 1024) . "MB";
                continue;
            }

            // Generate unique filename
            $finalName = bin2hex(random_bytes(8)) . '_' . preg_replace('/[^A-Za-z0-9._-]/', '_', $origName);
            $targetPath = $this->uploadDir . $finalName;

            if (move_uploaded_file($tmpName, $targetPath)) {
                $filehash = hash_file('sha256', $targetPath);
                $relativePath = 'accommodations/' . $finalName;
                
                $processedImages[] = [
                    'filename' => $finalName,
                    'filepath' => $relativePath,
                    'filehash' => $filehash,
                    'description' => $_POST['image_descriptions'][$i] ?? ''
                ];
                $saved++;
                
                // Debug: Log successful image save
                error_log("AccommodationController::processImages - Image saved: $finalName to $targetPath");
            } else {
                $errors[] = "File " . ($i + 1) . " ($origName): Failed to move uploaded file";
                error_log("AccommodationController::processImages - Failed to move file: $origName to $targetPath");
            }
        }

        // Log any errors
        if (!empty($errors)) {
            error_log("AccommodationController::processImages - Errors: " . implode('; ', $errors));
        }

        return $processedImages;
    }

    private function getUploadErrorMessage($errorCode)
    {
        switch ($errorCode) {
            case UPLOAD_ERR_INI_SIZE:
                return 'File exceeds upload_max_filesize directive';
            case UPLOAD_ERR_FORM_SIZE:
                return 'File exceeds MAX_FILE_SIZE directive';
            case UPLOAD_ERR_PARTIAL:
                return 'File was only partially uploaded';
            case UPLOAD_ERR_NO_FILE:
                return 'No file was uploaded';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Missing temporary folder';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Failed to write file to disk';
            case UPLOAD_ERR_EXTENSION:
                return 'File upload stopped by extension';
            default:
                return 'Unknown upload error';
        }
    }

    public function edit($id)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $accommodation = $this->model->getAccommodationById($id);
        if (!$accommodation) {
            $_SESSION['accommodation_alert'] = [
                'errors' => ['Accommodation not found'],
                'success' => []
            ];
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }

        $errors = [];
        $success = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Similar to create but with update logic
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $statusType = $_POST['status_type'] ?? '';
    

            if (empty($title)) {
                $errors[] = 'Title is required';
            }
            if (empty($description)) {
                $errors[] = 'Description is required';
            }
            if (!in_array($statusType, ['vip_pools', 'hotel_rooms', 'glamping'])) {
                $errors[] = 'Invalid status type';
            }

            if (empty($errors)) {
                $accommodationData = [
                    'title' => $title,
                    'description' => $description,
                    'status_type' => $statusType,
               
                ];

                if ($this->model->updateAccommodation($id, $accommodationData)) {
                    $success[] = 'Accommodation updated successfully';
                } else {
                    $errors[] = 'Failed to update accommodation';
                }
            }
        }

        $_SESSION['accommodation_alert'] = [
            'errors' => $errors,
            'success' => $success
        ];

        // Redirect to prevent form resubmission
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }

    public function delete($id)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $accommodation = $this->model->getAccommodationById($id);
        if (!$accommodation) {
            $_SESSION['accommodation_alert'] = [
                'errors' => ['Accommodation not found'],
                'success' => []
            ];
        } else {
            // Delete image files
            foreach ($accommodation['images'] as $image) {
                $filePath = __DIR__ . '/../accommodations/' . $image['filename'];
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }

            if ($this->model->deleteAccommodation($id)) {
                $_SESSION['accommodation_alert'] = [
                    'errors' => [],
                    'success' => ['Accommodation deleted successfully']
                ];
            } else {
                $_SESSION['accommodation_alert'] = [
                    'errors' => ['Failed to delete accommodation'],
                    'success' => []
                ];
            }
        }

        // Redirect to prevent form resubmission
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }
}
