CREATE DATABASE enjoyride;

USE enjoyride;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('passenger', 'driver', 'admin') NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    phone VARCHAR(20),
    profile_pic VARCHAR(255),
    vehicle_info TEXT,
    license_number VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE rides (
    id INT AUTO_INCREMENT PRIMARY KEY,
    passenger_id INT NOT NULL,
    driver_id INT,
    pickup_location VARCHAR(255) NOT NULL,
    dropoff_location VARCHAR(255) NOT NULL,
    pickup_lat DECIMAL(10, 8) NOT NULL,
    pickup_lng DECIMAL(11, 8) NOT NULL,
    dropoff_lat DECIMAL(10, 8) NOT NULL,
    dropoff_lng DECIMAL(11, 8) NOT NULL,
    distance DECIMAL(10, 2) NOT NULL,
    fare DECIMAL(10, 2) NOT NULL,
    status ENUM('requested', 'accepted', 'in_progress', 'completed', 'cancelled') NOT NULL,
    request_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    pickup_time TIMESTAMP NULL,
    dropoff_time TIMESTAMP NULL,
    payment_status ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
    razorpay_order_id VARCHAR(255),
    razorpay_payment_id VARCHAR(255),
    FOREIGN KEY (passenger_id) REFERENCES users(id),
    FOREIGN KEY (driver_id) REFERENCES users(id)
);

CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ride_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    transaction_id VARCHAR(255) NOT NULL,
    status ENUM('pending', 'completed', 'failed') NOT NULL,
    payment_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ride_id) REFERENCES rides(id)
);