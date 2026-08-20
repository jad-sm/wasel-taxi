-- Wasel Taxi Platform — Database Schema
-- Import this file into MySQL before using the app:
--   mysql -u root -p < schema.sql

CREATE DATABASE IF NOT EXISTS wasel_taxi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE wasel_taxi;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(120) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    phone VARCHAR(30) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS rides (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ride_code VARCHAR(12) NOT NULL UNIQUE,
    user_id INT NOT NULL,
    vehicle_type ENUM('car','moto','caravan') NOT NULL,
    pickup_location VARCHAR(255) NOT NULL,
    dropoff_location VARCHAR(255) NOT NULL,
    notes VARCHAR(255) DEFAULT NULL,
    status ENUM('pending','confirmed','ongoing','completed','cancelled') NOT NULL DEFAULT 'pending',
    requested_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    sender ENUM('client','support') NOT NULL DEFAULT 'client',
    body TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;
