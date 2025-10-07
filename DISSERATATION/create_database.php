<?php
// Database credentials (adjust if needed)
$host = 'localhost';
$dbName = 'royal_academy';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

try {
    // Connect to MySQL server without specifying database first
    $pdo = new PDO("mysql:host=$host;charset=$charset", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbName CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
    echo "Database '$dbName' created or already exists.<br>";

    // Use the database
    $pdo->exec("USE $dbName");

    // Create tables
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS admin (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
        CREATE TABLE IF NOT EXISTS students (
            id VARCHAR(20) PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            class VARCHAR(50),
            dob DATE,
            source VARCHAR(20) DEFAULT 'manual'
        );
        CREATE TABLE IF NOT EXISTS lecturers (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            subject VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL
        );
        CREATE TABLE IF NOT EXISTS parents (
            id INT AUTO_INCREMENT PRIMARY KEY,
            student_id VARCHAR(20),
            parent_email VARCHAR(100) NOT NULL,
            FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
        );
        CREATE TABLE IF NOT EXISTS courses (
            id VARCHAR(20) PRIMARY KEY,
            title VARCHAR(150) NOT NULL,
            type VARCHAR(50) NOT NULL
        );
        CREATE TABLE IF NOT EXISTS course_materials (
            id INT AUTO_INCREMENT PRIMARY KEY,
            course_id VARCHAR(20),
            filename VARCHAR(255),
            filedata LONGTEXT,
            FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
        );
    ");

    echo "Tables created successfully.";

} catch (PDOException $e) {
    echo "Error during database setup: " . $e->getMessage();
    exit;
}
?>
