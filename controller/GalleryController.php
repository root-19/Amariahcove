<?php
class GalleryController
{
    protected $model;
    protected $uploadDir;

    public function __construct()
    {
        require_once __DIR__ . '/../model/GalleryModel.php';
        $this->model = new GalleryModel();

        // Use admin/gallery directory for consistency with gallery.php
        $adminGallery = __DIR__ . '/../gallery/'; // filesystem absolute path
        $this->uploadDir = str_replace('\\', '/', $adminGallery); // normalize slashes

        // ensure directory exists and is writable
        if (!is_dir($this->uploadDir)) {
            if (!mkdir($this->uploadDir, 0755, true) && !is_dir($this->uploadDir)) {
                error_log("GalleryController::__construct - failed to create upload dir: {$this->uploadDir}");
            }
        }
        if (!is_writable($this->uploadDir)) {
            // try to set permissions and log if still not writable
            @chmod($this->uploadDir, 0755);
            if (!is_writable($this->uploadDir)) {
                error_log("GalleryController::__construct - upload dir not writable: {$this->uploadDir}");
            }
        }
    }

    public function upload()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $errors = [];
        $success = [];
        $already = [];
        $db_failed = [];
        $db_success = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (empty($_FILES['images'])) {
                $errors[] = 'No files uploaded.';
            } else {
                $files = $_FILES['images'];

                if (!is_array($files['name'])) {
                    $files = [
                        'name'     => [$files['name']],
                        'type'     => [$files['type']],
                        'tmp_name' => [$files['tmp_name']],
                        'error'    => [$files['error']],
                        'size'     => [$files['size']],
                    ];
                }

                $names = array_filter((array)$files['name']);
                $count = count($names);

                if ($count === 0) {
                    $errors[] = 'No files selected.';
                } elseif ($count > 5) {
                    $errors[] = 'You can upload up to 5 images at once.';
                } else {
                    $allowed = ['image/jpeg','image/png','image/gif','image/webp'];
                    $maxSize = 5 * 1024 * 1024; // 5 MB
                    $saved   = []; // DB payload
                    $movedFiles = []; // track moved files and original names for rollback/messages

                    for ($i = 0; $i < $count; $i++) {
                        $tmpName  = $files['tmp_name'][$i];
                        $origName = basename($files['name'][$i]);
                        $size     = $files['size'][$i];
                        $error    = $files['error'][$i];

                        if ($error !== UPLOAD_ERR_OK) {
                            $errors[] = "Upload error for {$origName}.";
                            continue;
                        }

                        $finfo = finfo_open(FILEINFO_MIME_TYPE);
                        $type  = finfo_file($finfo, $tmpName);
                        finfo_close($finfo);

                        if ($type === false || !in_array($type, $allowed)) {
                            $errors[] = "{$origName} is not a supported image type.";
                            continue;
                        }

                        if ($size > $maxSize) {
                            $errors[] = "{$origName} exceeds the 5MB limit.";
                            continue;
                        }

                        $ext       = pathinfo($origName, PATHINFO_EXTENSION);
                        $safeName  = preg_replace('/[^A-Za-z0-9_\-]/', '_', pathinfo($origName, PATHINFO_FILENAME));
                        $finalName = sprintf('%s_%s.%s', $safeName, uniqid(), $ext);
                        $targetPath = rtrim($this->uploadDir, '/') . '/' . $finalName;

                        $hash = @md5_file($tmpName);
                        $isDuplicate = $hash && method_exists($this->model, 'existsByHash') && $this->model->existsByHash($hash);

                        if ($isDuplicate) {
                            $already[] = "{$origName} already uploaded.";
                            continue;
                        }

                        if (move_uploaded_file($tmpName, $targetPath)) {
                            $relativePath = 'admin/gallery/' . $finalName; // consistent with gallery.php storage
                            $saved[] = [
                                'filename'   => $finalName,
                                'filepath'   => $relativePath,
                                'filehash'   => $hash,
                                'created_at' => date('Y-m-d H:i:s'),
                                'orig_name'  => $origName,
                                'targetPath' => $targetPath,
                            ];
                            // don't push to success yet — wait for DB insert
                        } else {
                            // detailed logging to help debug
                            $lastErr = error_get_last();
                            $errMsg = isset($lastErr['message']) ? $lastErr['message'] : 'unknown';
                            $errors[] = "Failed to move {$origName} to {$targetPath}. PHP error: {$errMsg}";
                            error_log("GalleryController::upload move_uploaded_file failed for {$origName} -> {$targetPath}. error: {$errMsg}");
                        }
                    }

                    if (!empty($saved)) {
                        // prepare DB payload without internal keys
                        $dbPayload = array_map(function($item){
                            return [
                                'filename'   => $item['filename'],
                                'filepath'   => $item['filepath'],
                                'filehash'   => $item['filehash'],
                                'created_at' => $item['created_at'],
                            ];
                        }, $saved);

                        $inserted = $this->model->insertImages($dbPayload);
                        if ($inserted) {
                            foreach ($saved as $s) {
                                $db_success[] = "{$s['orig_name']} uploaded and saved to database.";
                            }
                        } else {
                            // rollback moved files to keep consistency
                            foreach ($saved as $s) {
                                if (!empty($s['targetPath']) && file_exists($s['targetPath'])) {
                                    @unlink($s['targetPath']);
                                }
                                $db_failed[] = "{$s['orig_name']} uploaded but failed to save to database (file removed).";
                            }
                            error_log('GalleryController: insertImages failed. Payload: ' . print_r($dbPayload, true));
                        }
                    }
                }
            }
        }

        $_SESSION['gallery_alert'] = [
            'errors'     => $errors,
            'success'    => $db_success, // only report DB-confirmed successes
            'already'    => $already,
            'db_failed'  => $db_failed,  // new: DB failures
        ];

        $uploads = $this->model->getRecent(20);
        include __DIR__ . '/../view/gallery.php';
    }
     public function index() {
        return $this->model->getRecent(20);
    }
    
}
