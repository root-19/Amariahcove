<?php
require_once __DIR__ . '/../database/Database.php';

class FeedbackModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create(string $name, string $email, int $rate, string $message): int {
        $sql = "INSERT INTO feedback (name, email, rate, message, created_at) 
                VALUES (:name, :email, :rate, :message, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':rate' => $rate,
            ':message' => $message,
        ]);
        return (int)$this->db->lastInsertId();
    }

 public function list(int $limit = 20, int $offset = 0): array {
    $sql = "SELECT id, name, email, rate, message, created_at 
            FROM feedback 
            ORDER BY created_at DESC 
            LIMIT :limit OFFSET :offset";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}

