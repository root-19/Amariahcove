<?php
class AccommodationModel
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
            
            // Create tables if they don't exist
            $this->createTables();
        } catch (Exception $e) {
            error_log("AccommodationModel::__construct - Error: " . $e->getMessage());
            $this->pdo = null;
        }
    }

    private function createTables()
    {
        if (!$this->pdo) return;

        try {
            // Create accommodations table
            $this->pdo->exec("
                CREATE TABLE IF NOT EXISTS accommodations (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    title VARCHAR(255) NOT NULL,
                    description TEXT NOT NULL,
                    status_type ENUM('vip_pools', 'hotel_rooms', 'glamping') NOT NULL,
                    price_per_night DECIMAL(10,2) DEFAULT NULL,
                    max_guests INT DEFAULT NULL,
                    amenities TEXT DEFAULT NULL,
                    location VARCHAR(255) DEFAULT NULL,
                    is_active BOOLEAN DEFAULT TRUE,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");

            // Create accommodation_images table
            $this->pdo->exec("
                CREATE TABLE IF NOT EXISTS accommodation_images (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    accommodation_id INT NOT NULL,
                    filename VARCHAR(255) NOT NULL,
                    filepath VARCHAR(255) NOT NULL,
                    filehash VARCHAR(64) DEFAULT NULL,
                    description TEXT DEFAULT NULL,
                    is_primary BOOLEAN DEFAULT FALSE,
                    sort_order INT DEFAULT 0,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (accommodation_id) REFERENCES accommodations(id) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
        } catch (Exception $e) {
            error_log("AccommodationModel::createTables - Error: " . $e->getMessage());
        }
    }

    public function createAccommodation($data)
    {
        if (!$this->pdo) {
            error_log("AccommodationModel::createAccommodation - PDO connection is null");
            return false;
        }

        try {
            $this->pdo->beginTransaction();
            
            $sql = "INSERT INTO accommodations (title, description, status_type, price_per_night, max_guests, amenities, location) 
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
            
            // Debug: Log the SQL and parameters
            error_log("AccommodationModel::createAccommodation - SQL: " . $sql);
            error_log("AccommodationModel::createAccommodation - Params: " . print_r($params, true));
            
            $result = $stmt->execute($params);
            
            if (!$result) {
                error_log("AccommodationModel::createAccommodation - Execute failed: " . print_r($stmt->errorInfo(), true));
                $this->pdo->rollBack();
                return false;
            }
            
            $accommodationId = $this->pdo->lastInsertId();
            $this->pdo->commit();
            
            error_log("AccommodationModel::createAccommodation - Success! ID: " . $accommodationId);
            return $accommodationId;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("AccommodationModel::createAccommodation - Error: " . $e->getMessage());
            return false;
        }
    }

    public function addImages($accommodationId, $images)
    {
        if (!$this->pdo || empty($images)) {
            error_log("AccommodationModel::addImages - PDO is null or images array is empty");
            return false;
        }

        try {
            $this->pdo->beginTransaction();
            
            $sql = "INSERT INTO accommodation_images (accommodation_id, filename, filepath, filehash, description, is_primary, sort_order) 
                    VALUES (:accommodation_id, :filename, :filepath, :filehash, :description, :is_primary, :sort_order)";
            $stmt = $this->pdo->prepare($sql);
            
            // Debug: Log images data
            error_log("AccommodationModel::addImages - Adding " . count($images) . " images for accommodation ID: $accommodationId");
            error_log("AccommodationModel::addImages - Images data: " . print_r($images, true));
            
            foreach ($images as $index => $image) {
                $params = [
                    ':accommodation_id' => $accommodationId,
                    ':filename' => $image['filename'],
                    ':filepath' => $image['filepath'],
                    ':filehash' => $image['filehash'],
                    ':description' => $image['description'],
                    ':is_primary' => $index === 0, // First image is primary
                    ':sort_order' => $index
                ];
                
                $result = $stmt->execute($params);
                if (!$result) {
                    error_log("AccommodationModel::addImages - Failed to insert image: " . print_r($stmt->errorInfo(), true));
                } else {
                    error_log("AccommodationModel::addImages - Successfully inserted image: " . $image['filename']);
                }
            }
            
            $this->pdo->commit();
            error_log("AccommodationModel::addImages - All images saved successfully");
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("AccommodationModel::addImages - Error: " . $e->getMessage());
            return false;
        }
    }

    public function getAllAccommodations($statusType = null)
    {
        if (!$this->pdo) return [];

        try {
            $sql = "SELECT * FROM accommodations WHERE is_active = TRUE";
            $params = [];
            
            if ($statusType) {
                $sql .= " AND status_type = :status_type";
                $params[':status_type'] = $statusType;
            }
            
            $sql .= " ORDER BY created_at DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            $accommodations = $stmt->fetchAll();
            
            // Get images for each accommodation
            foreach ($accommodations as &$accommodation) {
                $accommodation['images'] = $this->getAccommodationImages($accommodation['id']);
            }
            
            return $accommodations;
        } catch (Exception $e) {
            error_log("AccommodationModel::getAllAccommodations - Error: " . $e->getMessage());
            return [];
        }
    }

    public function getAccommodationById($id)
    {
        if (!$this->pdo) return null;

        try {
            $sql = "SELECT * FROM accommodations WHERE id = :id AND is_active = TRUE";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            
            $accommodation = $stmt->fetch();
            if ($accommodation) {
                $accommodation['images'] = $this->getAccommodationImages($id);
            }
            
            return $accommodation;
        } catch (Exception $e) {
            error_log("AccommodationModel::getAccommodationById - Error: " . $e->getMessage());
            return null;
        }
    }

    public function getAccommodationImages($accommodationId)
    {
        if (!$this->pdo) return [];

        try {
            $sql = "SELECT * FROM accommodation_images WHERE accommodation_id = :accommodation_id ORDER BY sort_order ASC, created_at ASC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':accommodation_id' => $accommodationId]);
            
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("AccommodationModel::getAccommodationImages - Error: " . $e->getMessage());
            return [];
        }
    }

    public function updateAccommodation($id, $data)
    {
        if (!$this->pdo) return false;

        try {
            $sql = "UPDATE accommodations SET 
                    title = :title, 
                    description = :description, 
                    status_type = :status_type, 
                    price_per_night = :price_per_night, 
                    max_guests = :max_guests, 
                    amenities = :amenities, 
                    location = :location,
                    updated_at = CURRENT_TIMESTAMP
                    WHERE id = :id";
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':title' => $data['title'],
                ':description' => $data['description'],
                ':status_type' => $data['status_type'],
                ':price_per_night' => $data['price_per_night'] ?? null,
                ':max_guests' => $data['max_guests'] ?? null,
                ':amenities' => $data['amenities'] ?? null,
                ':location' => $data['location'] ?? null
            ]);
        } catch (Exception $e) {
            error_log("AccommodationModel::updateAccommodation - Error: " . $e->getMessage());
            return false;
        }
    }

    public function deleteAccommodation($id)
    {
        if (!$this->pdo) return false;

        try {
            $this->pdo->beginTransaction();
            
            // Delete images first (due to foreign key constraint)
            $sql = "DELETE FROM accommodation_images WHERE accommodation_id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            
            // Delete accommodation
            $sql = "DELETE FROM accommodations WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([':id' => $id]);
            
            $this->pdo->commit();
            return $result;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("AccommodationModel::deleteAccommodation - Error: " . $e->getMessage());
            return false;
        }
    }

    public function getStatusTypes()
    {
        return [
            'vip_pools' => 'VIP Pools',
            'hotel_rooms' => 'Hotel Rooms',
            'glamping' => 'Glamping'
        ];
    }
}
