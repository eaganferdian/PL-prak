-- Database: royal_hall_booking
CREATE DATABASE IF NOT EXISTS royal_hall_booking;
USE royal_hall_booking;

-- Tabel users
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel rooms
CREATE TABLE rooms (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    capacity INT NOT NULL,
    location VARCHAR(100),
    image_path VARCHAR(255),
    hourly_rate DECIMAL(10,2) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel facilities
CREATE TABLE facilities (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    icon VARCHAR(50),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel room_facilities (many-to-many)
CREATE TABLE room_facilities (
    room_id INT,
    facility_id INT,
    PRIMARY KEY (room_id, facility_id),
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
    FOREIGN KEY (facility_id) REFERENCES facilities(id) ON DELETE CASCADE
);

-- Tabel bookings
CREATE TABLE bookings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    room_id INT,
    purpose TEXT NOT NULL,
    booking_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    total_cost DECIMAL(10,2),
    status ENUM('pending', 'approved', 'rejected', 'completed') DEFAULT 'pending',
    admin_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
);

-- Sample data
INSERT INTO users (username, email, password, full_name, role) VALUES 
('admin', 'admin@castle.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Royal Administrator', 'admin'),
('knight_arthur', 'arthur@castle.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sir Arthur', 'user');

INSERT INTO rooms (name, description, capacity, location, hourly_rate) VALUES 
('Throne Room', 'Grand hall for royal ceremonies and important meetings', 200, 'Main Castle - East Wing', 500.00),
('Royal Library', 'Quiet study room with ancient manuscripts', 50, 'Main Castle - West Wing', 200.00),
('Knights Hall', 'Training and meeting hall for knights', 100, 'Barracks Area', 150.00),
('Royal Garden Pavilion', 'Outdoor pavilion for celebrations', 150, 'Royal Gardens', 300.00);

INSERT INTO facilities (name, icon, description) VALUES 
('Projector', '📽️', 'HD Projector and screen'),
('Sound System', '🔊', 'Professional audio equipment'),
('WiFi', '📶', 'High-speed internet'),
('Catering', '🍽️', 'Food and beverage service'),
('Air Conditioning', '❄️', 'Climate control'),
('Whiteboard', '📋', 'Writing board and markers');

INSERT INTO room_facilities (room_id, facility_id) VALUES 
(1, 2), (1, 3), (1, 5), (1, 6),
(2, 3), (2, 5), (2, 6),
(3, 1), (3, 2), (3, 5),
(4, 2), (4, 4);