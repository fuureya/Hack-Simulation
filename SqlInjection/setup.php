<?php
require_once 'db.php';

// Create database if not exists
mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS neohms_sqli");
mysqli_select_db($conn, "neohms_sqli");

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

if ($row['count'] <= 3) { // Jika hanya ada data awal atau kosong
    // --- SEEDING USERS (50 Data) ---
    $roles = ['admin', 'doctor', 'staff', 'nurse'];
    for ($i = 1; $i <= 50; $i++) {
        $u = "user" . $i;
        $p = "pass" . $i;
        $r = $roles[array_rand($roles)];
        $e = "$u@hospital.com";
        mysqli_query($conn, "INSERT INTO users (username, password, role, email) VALUES ('$u', '$p', '$r', '$e')");
    }

    // --- SEEDING DOCTORS (50 Data) ---
    $specs = ['Bedah Umum', 'Jantung', 'Anak', 'Saraf', 'Mata', 'Kulit', 'Gigi', 'Dalam'];
    $names = ['Dr. Andi', 'Dr. Budi', 'Dr. Citra', 'Dr. Dewi', 'Dr. Eko', 'Dr. Fani', 'Dr. Gani', 'Dr. Hana'];
    for ($i = 1; $i <= 50; $i++) {
        $n = $names[array_rand($names)] . " " . $i;
        $s = $specs[array_rand($specs)];
        $sch = "Senin - Jumat (08:00 - 16:00)";
        mysqli_query($conn, "INSERT INTO doctors (id, name, specialist, schedule) VALUES ($i, '$n', '$s', '$sch')");
    }

    // --- SEEDING PATIENTS (50 Data) ---
    $p_names = ['Samsul', 'Rina', 'Joko', 'Maya', 'Tono', 'Lusi', 'Hendra', 'Sari'];
    for ($i = 1; $i <= 50; $i++) {
        $n = $p_names[array_rand($p_names)] . " " . $i;
        $nik = "3201" . str_pad($i, 12, '0', STR_PAD_LEFT);
        $bd = "19" . rand(70, 99) . "-" . rand(1, 12) . "-" . rand(1, 28);
        $addr = "Jl. Contoh No. $i, Kota Digital";
        mysqli_query($conn, "INSERT INTO patients (id, name, nik, birthdate, address) VALUES ($i, '$n', '$nik', '$bd', '$addr')");
    }

    // --- SEEDING MEDICAL RECORDS (50 Data) ---
    $diagnoses = ['Flu Burung', 'Typus', 'Maag Akut', 'Patah Tulang', 'Katarak', 'Alergi', 'Migrain'];
    for ($i = 1; $i <= 50; $i++) {
        $pid = rand(1, 50);
        $did = rand(1, 50);
        $diag = $diagnoses[array_rand($diagnoses)];
        $treat = "Istirahat dan Obat Rutin";
        $note = "Pasien perlu kontrol minggu depan.";
        mysqli_query($conn, "INSERT INTO medical_records (patient_id, doctor_id, diagnosis, treatment, notes) VALUES ($pid, $did, '$diag', '$treat', '$note')");
    }

    // --- SEEDING BILLING (50 Data) ---
    $methods = ['Credit Card', 'Debit Card', 'Cash', 'Insurance'];
    for ($i = 1; $i <= 50; $i++) {
        $pid = rand(1, 50);
        $tot = rand(100000, 5000000);
        $met = $methods[array_rand($methods)];
        $card = rand(4000, 6000) . "-XXXX-XXXX-" . rand(1000, 9999);
        mysqli_query($conn, "INSERT INTO billing (patient_id, total, payment_method, card_number) VALUES ($pid, $tot, '$met', '$card')");
    }

    echo "Database HMS berhasil di-setup dengan 50 data untuk masing-masing tabel.";
} else {
    echo "Database sudah terisi data.";
}
?>
