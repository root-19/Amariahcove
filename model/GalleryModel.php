<?php
class GalleryModel
{
    protected $pdo;

    public function __construct()
    {
        try {
            $configPath = __DIR__ . '/../database/database.php';
            if (file_exists($configPath)) {
                require_once $configPath;
                $db = Database::getInstance();
                $this->pdo = $db->getConnection();
            } else {
                global $pdo;
                $this->pdo = $pdo ?? null;
            }
        } catch (Exception $e) {
            error_log("GalleryModel::__construct - Error: " . $e->getMessage());
            $this->pdo = null;
        }
    }

    public function insertImages(array $rows)
    {
        if (!$this->pdo) return false;

        $sql = "INSERT INTO gallery_images (message, filename, filepath, filehash, created_at) 
                VALUES (:message, :filename, :filepath, :filehash, :created_at)";
        $stmt = $this->pdo->prepare($sql);

        try {
            $this->pdo->beginTransaction();
            foreach ($rows as $r) {
                $stmt->execute([
                    ':message'   => $r['message'],
                    ':filename'   => $r['filename'],
                    ':filepath'   => $r['filepath'],
                    ':filehash'   => $r['filehash'],
                    ':created_at' => $r['created_at'],
                ]);
            }
            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }

    public function getRecent($limit = 20)
    {
        if (!$this->pdo) return [];
        $stmt = $this->pdo->prepare("SELECT * FROM gallery_images ORDER BY id DESC LIMIT :lim");
        $stmt->bindValue(':lim', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function existsByHash(string $hash): bool
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM galleries WHERE filehash = :hash");
        $stmt->execute([':hash' => $hash]);
        return $stmt->fetchColumn() > 0;
    }
   
    // get recent uploads

}
