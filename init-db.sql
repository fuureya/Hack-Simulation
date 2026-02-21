-- Create databases for all labs
CREATE DATABASE IF NOT EXISTS labsec_dashboard;
CREATE DATABASE IF NOT EXISTS neobank_csrf;
CREATE DATABASE IF NOT EXISTS cinemax_xss;
CREATE DATABASE IF NOT EXISTS neohms_sqli;

-- New OWASP Labs (2025)
CREATE DATABASE IF NOT EXISTS safevault_crypto;
CREATE DATABASE IF NOT EXISTS quickloan_insecure;
CREATE DATABASE IF NOT EXISTS devops_misconfig;
CREATE DATABASE IF NOT EXISTS securelogin_auth;
CREATE DATABASE IF NOT EXISTS audittrail_log;
CREATE DATABASE IF NOT EXISTS insecure_library;

-- Setup for CSRF Lab
USE neobank_csrf;
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE,
    password VARCHAR(255),
    acc_number VARCHAR(15) UNIQUE,
    pin VARCHAR(6),
    balance DECIMAL(15,2),
    role VARCHAR(20)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_account VARCHAR(15),
    receiver_account VARCHAR(15),
    amount DECIMAL(15,2),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Setup for XSS Lab
USE cinemax_xss;
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    movie_title VARCHAR(255),
    customer_name VARCHAR(255),
    showtime VARCHAR(100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE,
    password VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Setup for SQL Injection Lab
USE neohms_sqli;
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE,
    password VARCHAR(255),
    role VARCHAR(20),
    email VARCHAR(100)
);

CREATE TABLE IF NOT EXISTS patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    nik VARCHAR(20),
    birthdate DATE,
    address TEXT
);

CREATE TABLE IF NOT EXISTS doctors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    specialist VARCHAR(100),
    schedule TEXT
);

CREATE TABLE IF NOT EXISTS medical_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT,
    doctor_id INT,
    diagnosis TEXT,
    treatment TEXT,
    notes TEXT
);

CREATE TABLE IF NOT EXISTS billing (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT,
    total DECIMAL(10,2),
    payment_method VARCHAR(50),
    card_number VARCHAR(20)
);
