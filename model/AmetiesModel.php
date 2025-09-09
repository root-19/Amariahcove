<?php
class AmetiesModel
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
            
            $this->createTables();
        } catch (Exception $e) {
            $this->pdo = null;
        }
    }

    private function createTables()
    {
        if (!$this->pdo) return;

        try {
            $this->pdo->exec("
                CREATE TABLE IF NOT EXISTS amenties (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    title VARCHAR(255) NOT NULL,
                    description TEXT NOT NULL,
                    status_type ENUM('ATV', 'banana_boat', 'jetski') NOT NULL,
                    price_per_night DECIMAL(10,2) DEFAULT NULL,
                    max_guests INT DEFAULT NULL,
                    amenities TEXT DEFAULT NULL,
                    location VARCHAR(255) DEFAULT NULL,
                    is_active BOOLEAN DEFAULT TRUE,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");

            $this->pdo->exec("
                CREATE TABLE IF NOT EXISTS amenties_images (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    amenties_id INT NOT NULL,
                    filename VARCHAR(255) NOT NULL,
                    filepath VARCHAR(255) NOT NULL,
                    filehash VARCHAR(64) DEFAULT NULL,
                    description TEXT DEFAULT NULL,
                    is_primary BOOLEAN DEFAULT FALSE,
                    sort_order INT DEFAULT 0,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (amenties_id) REFERENCES amenties(id) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
        } catch (Exception $e) {
            // silently fail
        }
    }

    // CREATE
    public function createAmenties($data)
    {
        if (!$this->pdo) return false;

        try {
            $this->pdo->beginTransaction();
            
            $sql = "INSERT INTO amenties(title, description, status_type, price_per_night, max_guests, amenities, location) 
                    VALUES (:title, :description, :status_type, :price_per_night, :max_guests, :amenities, :location)";
            $stmt = $this->pdo->prepare($sql);
            
            $params = [
                ':title' => $data['title'],
                ':description' => $data['description'],
                ':status_type' => $data['status_type'],
                ':price_per_night' => $data['price_per_night'] ?? null,
                ':max_guests' => $data['max_guests'] ?? null,
                ':amenities' => $data['amenities'] ?? null,
                ':location' => $data['location'] ?? null
            ];
            
            $result = $stmt->execute($params);
            if (!$result) {
                $this->pdo->rollBack();
                return false;
            }
            
            $amentiesId = $this->pdo->lastInsertId();
            $this->pdo->commit();
            
            return $amentiesId;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    // ADD IMAGES
    public function addAmentiesImages($amentiesId, $images)
    {
        if (!$this->pdo || empty($images)) return false;

        try {
            $this->pdo->beginTransaction();
            
            $sql = "INSERT INTO amenties_images (amenties_id, filename, filepath, filehash, description, is_primary, sort_order) 
                    VALUES (:amenties_id, :filename, :filepath, :filehash, :description, :is_primary, :sort_order)";
            $stmt = $this->pdo->prepare($sql);
            
            foreach ($images as $index => $image) {
                $params = [
                    ':amenties_id' => $amentiesId,
                    ':filename' => $image['filename'],
                    ':filepath' => $image['filepath'],
                    ':filehash' => $image['filehash'] ?? null,
                    ':description' => $image['description'] ?? null,
                    ':is_primary' => $index === 0,
                    ':sort_order' => $index
                ];
                
                $stmt->execute($params);
            }
            
            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    // READ ALL
    public function getAllAmenties($statusType = null)
    {
        if (!$this->pdo) return [];

        try {
            $sql = "SELECT * FROM amenties WHERE is_active = TRUE";
            $params = [];
            
            if ($statusType) {
                $sql .= " AND status_type = :status_type";
                $params[':status_type'] = $statusType;
            }
            
            $sql .= " ORDER BY created_at DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            $amenties = $stmt->fetchAll();
            
            foreach ($amenties as &$a) {
                $a['images'] = $this->getAmentiesImages($a['id']);
            }
            
            return $amenties;
        } catch (Exception $e) {
            return [];
        }
    }

    // READ BY ID
    public function getAmentiesById($id)
    {
        if (!$this->pdo) return null;

        try {
            $sql = "SELECT * FROM amenties WHERE id = :id AND is_active = TRUE";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            
            $a = $stmt->fetch();
            if ($a) {
                $a['images'] = $this->getAmentiesImages($id);
            }
            
            return $a;
        } catch (Exception $e) {
            return null;
        }
    }

    // GET IMAGES
    public function getAmentiesImages($amentiesId)
    {
        if (!$this->pdo) return [];

        try {
            $sql = "SELECT * FROM amenties_images WHERE amenties_id = :amenties_id ORDER BY sort_order ASC, created_at ASC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':amenties_id' => $amentiesId]);
            
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }

    // UPDATE
    public function updateAmenties($id, $data)
    {
        if (!$this->pdo) return false;

        try {
            $sql = "UPDATE amenties SET 
                    title = :title, 
                    description = :description, 
                    status_type = :status_type, 
                    updated_at = CURRENT_TIMESTAMP
                    WHERE id = :id";
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':title' => $data['title'],
                ':description' => $data['description'],
                ':status_type' => $data['status_type']
            ]);
        } catch (Exception $e) {
            return false;
        }
    }

    // DELETE
    public function deleteAmenties($id)
    {
        if (!$this->pdo) return false;

        try {
            $this->pdo->beginTransaction();
            
            $sql = "DELETE FROM amenties_images WHERE amenties_id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            
            $sql = "DELETE FROM amenties WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([':id' => $id]);
            
            $this->pdo->commit();
            return $result;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    // STATUS TYPES
    public function getStatusTypes()
    {
        return [
        'ATV' => 'ATV',
        'banana_boat' => 'Banana Boat',
        'jetski' => 'Jetski'
    ];
    }
}
