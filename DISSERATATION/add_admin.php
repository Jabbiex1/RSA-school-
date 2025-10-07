<?php
$host = 'localhost';
$dbName = 'royal_academy';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbName;charset=$charset", $user, $pass);
} catch (PDOException $e) {
    die("DB connection failed: " . $e->getMessage());
}

// Change these to your desired admin credentials
$username = 'santigie';
$email = 'santigie@gmail.com';
$passwordPlain = 'san111'; // Your constant admin password

$passwordHash = password_hash($passwordPlain, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("INSERT INTO admin (username, email, password) VALUES (?, ?, ?)");
$stmt->execute([$username, $email, $passwordHash]);

echo "Admin user created successfully.";
