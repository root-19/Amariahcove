<?php
class AmentiesController
{
    public $model;
    protected $uploadDir;

    public function __construct()
    {
        require_once __DIR__ . '/../model/AmetiesModel.php';
        $this->model = new AmetiesModel();
        $this->uploadDir = __DIR__ . '/../amenties/';

        if (!is_dir($this->uploadDir)) {
            @mkdir($this->uploadDir, 0755, true);
        }
        if (!is_writable($this->uploadDir)) {
            @chmod($this->uploadDir, 0755);
        }
    }

    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return [
            'amenties' => $this->model->getAllAmenties(),
            'statusTypes' => $this->model->getStatusTypes()
        ];
    }

    public function create()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $errors = [];
        $success = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $statusType = $_POST['status_type'] ?? '';

            if (empty($title)) $errors[] = 'Title is required';
            if (empty($description)) $errors[] = 'Description is required';
            if (!in_array($statusType, ['ATV', 'banana_boat', 'jetski'])) {
                $errors[] = 'Invalid status type';
            }
            if (empty($_FILES['images']['name'][0])) $errors[] = 'At least one image is required';

            if (empty($errors)) {
                $amentiesData = [
                    'title' => $title,
                    'description' => $description,
                    'status_type' => $statusType,
                ];

                $amentiesId = $this->model->createAmenties($amentiesData);

                if ($amentiesId) {
                    $images = $this->processImages($_FILES['images'], $amentiesId);

                    if (!empty($images)) {
                        if ($this->model->addAmentiesImages($amentiesId, $images)) {
                            $success[] = 'Amenties created successfully with ' . count($images) . ' images';
                        } else {
                            $errors[] = 'Amenties created but failed to save images';
                        }
                    } else {
                        $errors[] = 'Amenties created but no valid images were uploaded';
                    }
                } else {
                    $errors[] = 'Failed to create amenties';
                }
            }
        }

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => empty($errors),
                'errors' => $errors,
                'messages' => $success
            ]);
            exit;
        }

        $_SESSION['amenties_alert'] = [
            'errors' => $errors,
            'success' => $success
        ];
    }

    private function processImages($files, $amentiesId)
    {
        $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $maxSize = 5 * 1024 * 1024;
        $maxFiles = 10;
        $processedImages = [];

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
            if ($files['error'][$i] !== UPLOAD_ERR_OK) continue;

            $origName = $files['name'][$i];
            $tmpName = $files['tmp_name'][$i];
            $size = $files['size'][$i];
            $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

            if (!in_array($ext, $allowedExt)) continue;
            if ($size > $maxSize) continue;

            $finalName = bin2hex(random_bytes(8)) . '_' . preg_replace('/[^A-Za-z0-9._-]/', '_', $origName);
            $targetPath = $this->uploadDir . $finalName;

            if (move_uploaded_file($tmpName, $targetPath)) {
                $filehash = hash_file('sha256', $targetPath);
                $relativePath = 'amenties/' . $finalName;

                $processedImages[] = [
                    'filename' => $finalName,
                    'filepath' => $relativePath,
                    'filehash' => $filehash,
                    'description' => $_POST['image_descriptions'][$i] ?? ''
                ];
                $saved++;
            }
        }

        return $processedImages;
    }

    public function edit($id)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $amenties = $this->model->getAmentiesById($id);
        if (!$amenties) {
            $_SESSION['amenties_alert'] = [
                'errors' => ['Amenties not found'],
                'success' => []
            ];
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }

        $errors = [];
        $success = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $statusType = $_POST['status_type'] ?? '';

            if (empty($title)) $errors[] = 'Title is required';
            if (empty($description)) $errors[] = 'Description is required';
            if (!in_array($statusType, ['vip_pools', 'hotel_rooms', 'glamping'])) $errors[] = 'Invalid status type';

            if (empty($errors)) {
                $amentiesData = [
                    'title' => $title,
                    'description' => $description,
                    'status_type' => $statusType,
                ];

                if ($this->model->updateAmenties($id, $amentiesData)) {
                    $success[] = 'Amenties updated successfully';
                } else {
                    $errors[] = 'Failed to update amenties';
                }
            }
        }

        $_SESSION['amenties_alert'] = [
            'errors' => $errors,
            'success' => $success
        ];

        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }

    public function delete($id)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $amenties = $this->model->getAmentiesById($id);
        if (!$amenties) {
            $_SESSION['amenties_alert'] = [
                'errors' => ['Amenties not found'],
                'success' => []
            ];
        } else {
            foreach ($amenties['images'] as $image) {
                $filePath = __DIR__ . '/../amenties/' . $image['filename'];
                if (file_exists($filePath)) @unlink($filePath);
            }

            if ($this->model->deleteAmenties($id)) {
                $_SESSION['amenties_alert'] = [
                    'errors' => [],
                    'success' => ['Amenties deleted successfully']
                ];
            } else {
                $_SESSION['amenties_alert'] = [
                    'errors' => ['Failed to delete amenties'],
                    'success' => []
                ];
            }
        }

        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }
}
