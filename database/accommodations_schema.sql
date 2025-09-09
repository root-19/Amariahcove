-- Accommodations database schema
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
