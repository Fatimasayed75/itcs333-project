<?php
require_once __DIR__ . '/utils/crud.php';
require_once __DIR__ . '/server/room.php';

use Utils\Crud;

// Database connection settings
$host = 'db';
$dbname = 'rbs';
$username = 'root';
$password = '271202';

try {
    // Create a new PDO instance to connect to MySQL server
    // $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo = new PDO("mysql:host=$host;port=3306", $username, $password); 
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if the database exists and create it if it doesn't
    $stmt = $pdo->query("SHOW DATABASES LIKE '$dbname'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("CREATE DATABASE `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
        echo "Database created successfully.\n";
    }

    // Connect to the newly created database
    $pdo->exec("USE `$dbname`");

    // Check if the database is empty and create tables if necessary
    $stmt = $pdo->query("SHOW TABLES LIKE 'bookings'");
    if ($stmt->rowCount() == 0) {
        // Database is empty, run the SQL script to create tables
        $sql = file_get_contents(__DIR__ . '/database/rbs.sql');
        $pdo->exec($sql);
        echo "Database and tables created successfully.\n";
    } else {
        echo "Database already exists.\n";
    }
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
    exit;
}

// Run the server
$server = new RoomServer($pdo);
$server->run();