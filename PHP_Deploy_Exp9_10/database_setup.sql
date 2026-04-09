-- Run this after creating your database in InfinityFree control panel.
-- Open phpMyAdmin, select your database, then run this script.

CREATE TABLE IF NOT EXISTS student_forms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(180) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    usn VARCHAR(60) NOT NULL,
    gender VARCHAR(20) NOT NULL,
    languages VARCHAR(255) NOT NULL,
    dob DATE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
