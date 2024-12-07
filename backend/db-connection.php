<?php
require_once __DIR__ . '/utils/crud.php';

// Database connection settings
$dbhost = "localhost";
$dbport = "3306";
$dbname = "rbs";
$dbuser = "root";
$dbpass = "";

try {
    // Create a new PDO instance to connect to MySQL server
    // $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo = new PDO("mysql:host={$dbhost};port={$dbport};dbname={$dbname}", $dbuser, $dbpass);
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
    // $stmt = $pdo->query("SHOW TABLES LIKE 'bookings'");
    if ($stmt->rowCount() == 0) {
        // Database is empty, run the SQL script to create tables
        $sql = file_get_contents(__DIR__ . '/database/rbs.sql');
        $pdo->exec($sql);
    }
    // return $pdo;
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
    exit;
}

// Run the server
// $server = new RoomServer($pdo);
// $server->run();