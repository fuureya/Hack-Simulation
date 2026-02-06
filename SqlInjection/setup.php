<?php
require_once 'db.php';

// Tabel Users
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE,
    password VARCHAR(255),
    role VARCHAR(20),
    email VARCHAR(100)
)");

// Tabel Patients
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    nik VARCHAR(20),
    birthdate DATE,
    address TEXT
)");

// Tabel Doctors
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS doctors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    specialist VARCHAR(100),
    schedule TEXT
)");

// Tabel Medical Records
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS medical_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT,
    doctor_id INT,
    diagnosis TEXT,
    treatment TEXT,
    notes TEXT,
    FOREIGN KEY (patient_id) REFERENCES patients(id),
    FOREIGN KEY (doctor_id) REFERENCES doctors(id)
)");

// Tabel Billing
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS billing (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT,
    total DECIMAL(10,2),
    payment_method VARCHAR(50),
    card_number VARCHAR(20),
    FOREIGN KEY (patient_id) REFERENCES patients(id)
)");

// Cek data awal
$res = mysqli_query($conn, "SELECT COUNT(*) as count FROM users");
$row = mysqli_fetch_assoc($res);

if ($row['count'] == 0) {
    // Seeding Users
    mysqli_query($conn, "INSERT INTO users (username, password, role, email) VALUES 
    ('admin_hms', 'P@ssw0rdAdmin2026', 'admin', 'admin@hospital.com'),
    ('dr_smith', 'smith123', 'doctor', 'smith@hospital.com'),
    ('staff_nina', 'nina789', 'staff', 'nina@hospital.com')");

    // Seeding Patients
    mysqli_query($conn, "INSERT INTO patients (name, nik, birthdate, address) VALUES 
    ('Budi Santoso', '3201010101010001', '1985-05-15', 'Jl. Merdeka No. 10, Jakarta'),
    ('Siti Aminah', '3201010101010002', '1992-11-20', 'Jl. Mawar No. 5, Bandung')");

    // Seeding Doctors
    mysqli_query($conn, "INSERT INTO doctors (id, name, specialist, schedule) VALUES 
    (1, 'Dr. Sarah Connor', 'Bedah Umum', 'Senin - Rabu (09:00 - 15:00)'),
    (2, 'Dr. John Doe', 'Jantung', 'Kamis - Jumat (10:00 - 17:00)')");

    // Seeding Records
    mysqli_query($conn, "INSERT INTO medical_records (patient_id, doctor_id, diagnosis, treatment, notes) VALUES 
    (1, 1, 'Appendicitis Akut', 'Operasi Appendectomy', 'Pasien harus istirahat total selama 1 minggu'),
    (2, 2, 'Hipertensi Ringan', 'Amlodipine 5mg', 'Kurangi asupan garam dan olahraga teratur')");

    // Seeding Billing
    mysqli_query($conn, "INSERT INTO billing (patient_id, total, payment_method, card_number) VALUES 
    (1, 15000000.00, 'Credit Card', '4532-XXXX-XXXX-1122'),
    (2, 500000.00, 'Debit Card', '6011-XXXX-XXXX-3344')");

    echo "Database HMS berhasil di-setup dan diisi data seed.";
} else {
    echo "Database sudah ada.";
}
?>
