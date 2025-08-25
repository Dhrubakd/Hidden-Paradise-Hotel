-- Create Database (run in phpMyAdmin or MySQL CLI)
CREATE DATABASE IF NOT EXISTS hotel_db;
USE hotel_db;

-- Table: guests
CREATE TABLE IF NOT EXISTS guests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    contact VARCHAR(20) NOT NULL,
    email VARCHAR(100)
);

-- Table: rooms
CREATE TABLE IF NOT EXISTS rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_number VARCHAR(10) NOT NULL,
    type ENUM('Single', 'Double', 'Suite') NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    status ENUM('Available', 'Booked') DEFAULT 'Available'
);

-- Table: services
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    price DECIMAL(10,2) NOT NULL
);

-- Table: reservations
CREATE TABLE IF NOT EXISTS reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    guest_id INT NOT NULL,
    room_id INT NOT NULL,
    check_in_date DATE NOT NULL,
    check_out_date DATE NOT NULL,
    FOREIGN KEY (guest_id) REFERENCES guests(id),
    FOREIGN KEY (room_id) REFERENCES rooms(id)
);

-- Table: reservation_services (junction for many-to-many)
CREATE TABLE IF NOT EXISTS reservation_services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reservation_id INT NOT NULL,
    service_id INT NOT NULL,
    FOREIGN KEY (reservation_id) REFERENCES reservations(id),
    FOREIGN KEY (service_id) REFERENCES services(id)
);

-- Table: payments
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reservation_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    paid TINYINT DEFAULT 0,
    FOREIGN KEY (reservation_id) REFERENCES reservations(id)
);

-- Sample Data
INSERT INTO rooms (room_number, type, price) VALUES
('101', 'Single', 50.00),
('102', 'Double', 80.00),
('103', 'Suite', 150.00);

INSERT INTO services (name, price) VALUES
('Extra Bed', 20.00),
('Food', 30.00),
('Airport Pickup', 50.00),
('Sightseeing', 100.00);

-- Sample users (for simple auth; in production, use a users table with hashed passwords)
-- For now, hardcoded in PHP